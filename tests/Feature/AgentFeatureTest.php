<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Speciality;
use Modules\Agents\Models\Agent;
use Modules\Agents\Models\AgentTransaction;

class AgentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_registration_creates_pending_agent_profile(): void
    {
        $response = $this->post(route('agent.register.submit'), [
            'name' => 'Agent John',
            'email' => 'john.agent@example.com',
            'mobile' => '01712345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('agent.login'));
        $this->assertDatabaseHas('users', [
            'email' => 'john.agent@example.com',
            'role' => 'agent'
        ]);

        $user = User::where('email', 'john.agent@example.com')->first();
        $this->assertDatabaseHas('agents', [
            'user_id' => $user->id,
            'status' => 'pending'
        ]);
    }

    public function test_unauthenticated_user_cannot_access_agent_dashboard(): void
    {
        $response = $this->get(route('agent.dashboard'));
        $response->assertRedirect(route('agent.login'));
    }

    public function test_active_agent_can_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'agent']);
        $agent = Agent::create([
            'user_id' => $user->id,
            'phone' => '01711111111',
            'referral_code' => 'AGT-XYZ123',
            'status' => 'active',
            'wallet_balance' => 100.00
        ]);

        $response = $this->actingAs($user)->get(route('agent.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('AGT-XYZ123');
    }

    public function test_pending_agent_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'agent']);
        $agent = Agent::create([
            'user_id' => $user->id,
            'phone' => '01711111111',
            'referral_code' => 'AGT-XYZ123',
            'status' => 'pending',
            'wallet_balance' => 0.00
        ]);

        $response = $this->actingAs($user)->get(route('agent.dashboard'));
        // Redirected by RoleMiddleware because they aren't authorized (status pending)
        $response->assertRedirect(route('patient.dashboard'));
    }

    public function test_agent_can_book_appointment_and_earn_commission(): void
    {
        // 1. Setup Agent
        $agentUser = User::factory()->create(['role' => 'agent']);
        $agent = Agent::create([
            'user_id' => $agentUser->id,
            'phone' => '01711111111',
            'referral_code' => 'AGT-XYZ123',
            'can_book_appointments' => true,
            'booking_commission_rate' => 50.00,
            'status' => 'active',
            'wallet_balance' => 0.00
        ]);

        // 2. Setup Doctor
        $doctorUser = User::factory()->create(['role' => 'doctor']);
        $speciality = Speciality::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'speciality_id' => $speciality->id,
            'consultation_fee' => 500.00,
            'status' => 'approved'
        ]);

        // 3. Post Booking
        $response = $this->actingAs($agentUser)->post(route('agent.booking.submit', $doctor->id), [
            'appointment_date' => today()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00:00',
            'type' => 'offline',
            'patient_name' => 'Test Patient',
            'patient_email' => 'patient.test@example.com',
            'patient_phone' => '01812345678',
            'reason' => 'Heart Checkup',
        ]);

        // Verify redirect to agent dashboard with success
        $response->assertRedirect(route('agent.dashboard'));
        $response->assertSessionHas('success');

        // Verify patient created
        $this->assertDatabaseHas('users', [
            'email' => 'patient.test@example.com',
            'role' => 'patient'
        ]);

        // Verify appointment created with agent_id
        $this->assertDatabaseHas('appointments', [
            'doctor_id' => $doctor->id,
            'agent_id' => $agent->id,
            'fee' => 500.00,
            'status' => 'confirmed'
        ]);

        // Verify agent wallet balance updated
        $agent->refresh();
        $this->assertEquals(50.00, $agent->wallet_balance);

        // Verify transaction logged
        $this->assertDatabaseHas('agent_transactions', [
            'agent_id' => $agent->id,
            'type' => 'commission_booking',
            'amount' => 50.00,
            'status' => 'completed'
        ]);
    }
}
