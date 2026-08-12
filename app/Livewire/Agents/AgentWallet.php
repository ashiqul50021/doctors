<?php

namespace App\Livewire\Agents;

use Livewire\Component;

class AgentWallet extends Component
{
    public $total_referrals = 48;
    public $total_commission = 14500.00;
    public $available_balance = 4200.00;
    public $pending_balance = 1200.00;

    public $withdraw_amount = '';
    public $payment_method = 'bKash';
    public $account_number = '';

    public function submitWithdraw()
    {
        $this->validate([
            'withdraw_amount' => 'required|numeric|min:500|max:' . $this->available_balance,
            'account_number' => 'required|string',
        ]);

        session()->flash('message', 'Payout request of ৳' . number_format($this->withdraw_amount, 2) . ' submitted successfully!');
        $this->reset(['withdraw_amount', 'account_number']);
    }

    public function render()
    {
        return view('components.agents.agent-wallet');
    }
}
