@if(in_array($type, ['saas', 'credit', 'DELIVERED']))
<span class="border border-transparent border-green-400 dark:text-slate-300 text-green-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif(in_array($type, ['selling','UNDELIVERED']))
<span class="border border-transparent border-yellow-500 dark:text-slate-300 text-yellow-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif(in_array($type, ['failed','debit']) || str_contains($type, 'Reject') || str_contains(strtolower($type), 'invalid') )
<span class="border border-transparent border-red-500 dark:text-slate-300 text-red-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif(in_array($type, ['referral','submit', 'ACCEPTED']))
<span class="border border-transparent border-blue-500 dark:text-slate-300 text-blue-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif($type!='')
<span class="border dark:text-slate-300 border-gray-200 dark:text-slate-300 text-gray-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@endif
