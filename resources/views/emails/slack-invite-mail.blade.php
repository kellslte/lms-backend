@component('mail::message')
# Hi TechAda

(The ADA Programmes Team)[mailto:theadaproject@enugutechhub.en.gov.ng] has invited you to use Slack with them, in the ADA Software Engineering Community. Click on the button below to join the community

@component('mail::button', ['url' => 'https://join.slack.com/t/adasoftwareen-8ux5224/shared_invite/zt-1f030yug6-y5wWPgd1QJljglul2xALJg'])
Join Community
@endcomponent

Much love,<br>
{{ config('app.name') }}
@endcomponent
