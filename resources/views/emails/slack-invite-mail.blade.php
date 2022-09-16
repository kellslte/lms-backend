@component('mail::message')
# Hi TechAda

The body of your message.

@component('mail::button', ['url' => 'https://join.slack.com/t/adasoftwareen-8ux5224/shared_invite/zt-1f030yug6-y5wWPgd1QJljglul2xALJg'])
Join Community
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
