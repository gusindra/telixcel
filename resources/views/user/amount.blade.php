@if($mutation=='debit')
<div class="text-red-600 dark:text-red-300 text-center uppercase py-1 px-1">- {{number_format($amount)}}</div>
@else
<div class="text-gray-600 dark:text-slate-300 text-center uppercase py-1 px-1">{{number_format($amount)}}</div>
@endif
