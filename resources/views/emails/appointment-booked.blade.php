<x-mail::message>
# Appointment Confirmation

Dear {{ $appointment->patient->user->name }},

Your appointment with **Dr. {{ $appointment->doctor->user->name }}** has been successfully booked.

**Appointment Details:**
- **Date:** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}
- **Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
- **Type:** <span style="text-transform: uppercase">{{ $appointment->type }}</span>
- **Consulting Fee:** ৳{{ $appointment->fee }}

@if($appointment->type === 'online')
**Video Call Link:**
You can join your online consultation using the following link at the scheduled time:

<x-mail::button :url="$appointment->meeting_link" color="success">
Join Video Call
</x-mail::button>
<br>
Alternatively, copy and paste this link in your browser: <br>
<a href="{{ $appointment->meeting_link }}">{{ $appointment->meeting_link }}</a>
@else
**Token Number:** {{ $appointment->token_number }}

Please visit the clinic at the scheduled time with your token number.
@endif

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
