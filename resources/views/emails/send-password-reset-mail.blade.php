@component('mail::message')
# Hi,

You recently requested to reset your password. If this was not you and you suspect fraudulent acitivities being carried out on your account, please contact us.

If you sent this intentionally, click on the button to reset your password.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

If you cannot click on the button, you can copy this link and paste in your browser to reset you password

[{{ $url }}]({{ $url }})

Regards,<br>
The ADA Team.
@endcomponent
