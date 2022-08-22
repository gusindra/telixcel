@if(in_array($type, ['saas']))
<span class="border border-transparent border-green-400  text-green-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif($type=='selling')
<span class="border border-transparent border-yellow-500  text-yellow-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif($type=='failed')
<span class="border border-transparent border-red-500  text-red-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@elseif(in_array($type, ['referral','submit']))
<span class="border border-transparent border-blue-500  text-blue-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@else
<span class="border border-gray-300 bg-gray-200 text-gray-600 text-xs rounded-md text-center uppercase py-1 px-2">{{$type}}</span>
@endif
