@component('mail::message')
# Reset Your Password

Hello {{ $data['user']->name }},

You requested to reset your password. Click the button below to reset it:

@component('mail::button', ['url' => $data['resetUrl']])
Reset Password
@endcomponent

Or copy and paste this link into your browser:
{{ $data['resetUrl'] }}

This link will expire in 60 minutes.

If you did not request a password reset, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

