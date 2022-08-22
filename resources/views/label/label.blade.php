@if(in_array($type, ['active','approved', 'done', 'paid', 'reviewed']))
<span class="border border-transparent border-green-400 bg-green-50 text-green-600 text-xs font-bold rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif(in_array($type, ['expired','unpaid', 'unread']))
<span class="border border-transparent border-yellow-500 bg-yellow-50 text-yellow-600 text-xs font-bold rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif(in_array($type, ['failed','cancel']))
<span class="border border-transparent border-red-500 bg-red-50 text-red-600 text-xs font-bold rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif(in_array($type, ['working','submit']))
<span class="border border-transparent border-blue-500 bg-blue-50 text-blue-600 text-xs font-bold rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif($type!='')
<span class="border border-gray-300 bg-gray-200 text-gray-600 text-xs rounded-md font-bold text-center uppercase py-1 px-2">{{$type}}</span>
@endif
