@component('mail::message')
# Hi {{ $user->name }},

You have been onboarded on our LMS as a facilitator.

Here are your account and login details:

User Name: {{  $user->name }}
Login Email: {{ $user->email }}
Your Email for password recovery: {{ $user->recovery_email }}

Your account password: {{ $password }}

[Click here to login]("https://lms.theadaproject.com.ng/facilitator")

Regards,<br>
The ADA Team
@endcomponent
