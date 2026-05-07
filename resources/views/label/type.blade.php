@if(in_array($type, ['saas', 'credit', 'DELIVERED']))
<span class="border border-transparent bg-green-400 dark:text-slate-300 text-green-100 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif(in_array($type, ['selling','UNDELIVERED']))
<span class="border border-transparent bg-yellow-500 dark:text-slate-300 text-yellow-100 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif(in_array($type, ['failed','debit']) || str_contains($type, 'Reject') || str_contains(strtolower($type), 'invalid') )
<span class="border border-transparent bg-red-500 dark:text-slate-300 text-red-100 text-xs rounded-xs text-center uppercase">{{$type}}</span>
@elseif(in_array($type, ['referral','submit', 'ACCEPTED']))
<span class="border border-transparent bg-blue-500 dark:text-slate-300 text-blue-100 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif($type!='')
<span class="border dark:text-slate-300 bg-gray-200 dark:text-slate-600 text-gray-900 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@endif
