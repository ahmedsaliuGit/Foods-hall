@component('mail::message')
# One Last Step

We just need you to confirmed your email address, to prove that you're a human. you get right? cool!

@component('mail::button', ['url' => url('/register/confirm?token='. $user->confirmation_token)])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
