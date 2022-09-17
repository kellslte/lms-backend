@component('mail::message')
# Hi TechAda

[The ADA Programmes Team](mailto:theadaproject@enugutechhub.en.gov.ng) has invited you to use Slack with them, in the ADA Software Engineering Community. Click on the button below to join the community

@component('mail::button', ['url' => "{$link}"])
Join Community
@endcomponent

### PS: Ignore this email if you are already on the Slack community!

Much love, <br>
{{ config('app.name') }}
@endcomponent
