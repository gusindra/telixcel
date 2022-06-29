@if($mutation=='debit')
<div class="text-red-600 text-center uppercase py-1 px-1">- {{number_format($amount)}}</div>
@else
<div class="text-gray-600 text-center uppercase py-1 px-1">{{number_format($amount)}}</div>
@endif
