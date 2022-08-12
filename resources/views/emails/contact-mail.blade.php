@component('mail::message')
# Hi I'm {{ $data['name'] }}

{{ $data['message'] }}

You can reply to this message by mailing to this address: {{ $data['email'] }}

Best regards,<br>
{{ $data['name'] }}
@endcomponent
