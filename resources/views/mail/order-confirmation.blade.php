@component('mail::message')
{{ __('Order Payment Topup from :account', ['account' => $order->customer->name]) }}

@component('mail::panel')
{{ __('Invoice :no', ['no' => $order->no]) }}
@endcomponent

@component('mail::table')
    | Field       | Data         |
    | ------------- |-------------|
    | Nominal      | {{ __(':nominal', ['nominal' => $order->total*100/111]) }} |
    | Total      | {{ __(':total', ['total' => $order->total]) }} |
    | Status      | {{ __(':status', ['status' => $order->status]) }} |
@endcomponent

@foreach($order->attachments as $attach)
{{ __('Client Upload = :url', ['url' => $attach->url]) }}
@endforeach

{{ __('You can check order by clicking the button below:') }}

@component('mail::button', ['url' => $redirectUrl])
{{ __('Add Balance') }}
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
