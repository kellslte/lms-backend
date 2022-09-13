@component('mail::message')
# Hi {{ $user->firstname }},

You have been onboarded on our LMS as a facilitator.

Here are your account and login details:

User Name: {{ $user->firstname }} {{ $user->lastname }}
Login Email: {{ $user->email }}
Your Email for password recovery: {{ $user->recovery_email }}

Your account password: {{ $password }}

[Click here to login](config('app.url'))

Regards,<br>
The ADA Team
@endcomponent
