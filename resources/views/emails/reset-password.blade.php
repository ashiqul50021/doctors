<x-mail::message>
# Password Reset Request

Hello,

You are receiving this email because we received a password reset request for your account on **{{ config('app.name') }}**.

<x-mail::button :url="$resetUrl" color="primary">
Reset Password
</x-mail::button>

This password reset link will expire in 60 minutes.

If you did not request a password reset, no further action is required.

Regards,<br>
{{ config('app.name') }}

<x-mail::subcopy>
If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
[{{ $resetUrl }}]({{ $resetUrl }})
</x-mail::subcopy>
</x-mail::message>
