@component('mail::message')
# Hi, {{ $user->name }}

Here is your login link.

@component('mail::button', ['url' => $url])
Click to login
@endcomponent

If you cannot see the button click on the link below to login.

{!! $url !!}

Regards,<br>
{{ config('app.name') }}
@endcomponent
