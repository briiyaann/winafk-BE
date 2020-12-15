@component('mail::message')
    <p>Hi {{ $user->firstname }},</p><br>
    <p>You recently requested to reset your password for your WinAFK account. Click the button bellow to reset it.</p>

@component('mail::button', ['url' => env('APP_URL') . '/user/reset-password/' . $password_reset->token])
Reset your password
@endcomponent
    <p>If you did not request a password, please ignore this email.</p>
Thanks,<br>
{{ env('APP_NAME') }}
@endcomponent
