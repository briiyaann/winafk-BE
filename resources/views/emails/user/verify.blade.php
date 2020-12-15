@component('mail::message')

<div style="width: 250px; margin:auto">
    <p>Hi {{ $user->firstname }},</p>
    <br/>
    <p>We're happy you signed up for WinAFK. To start using our platform, please confirm your email address.</p>
</div>

@component('mail::button', ['url' => env('APP_URL') . '/user/verify/' . $user->verification_code])
Verify Now
@endcomponent

Welcome to WinAFK!<br>
{{ config('app.name') }}
<br>

<p>Did you receive this email without signing up? <a href="#">Click Here</a>. This verification link will expire in 24 hours.</p>
@endcomponent
