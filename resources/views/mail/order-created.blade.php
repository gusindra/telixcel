@component('mail::message')
{{ __('New Order (Request Topup) from :account', ['account' => $order->customer->name]) }}

@component('mail::panel')
{{ __('Invoice :no', ['no' => $order->no]) }}
@endcomponent

@component('mail::table')
    | Field       | Data         |
    | ------------- |-------------|
    | Nominal      | {{ __(':nominal', ['nominal' => $order->total*100/111]) }} |
    | Total      | {{ __(':total', ['total' => $order->total]) }} |
@endcomponent

{{ __('You can check order by clicking the button below:') }}

@component('mail::button', ['url' => $redirectUrl])
    {{ __('View Order') }}
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
