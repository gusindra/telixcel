@component('mail::message')
{{ __('Your balance for account :account is = Rp :balance', ['account' => $saldo->name, 'balance' => number_format($saldo->balance)]) }}

{{ __('Please Top-up to avoid failed jobs when request to sending SMS') }}

{{ __('You can request to Top-up Telixcel Account by clicking the button below:') }}

@component('mail::button', ['url' => $redirectUrl])
{{ __('Request Topup') }}
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
