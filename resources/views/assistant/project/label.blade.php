@if(in_array($type, ['active','approved', 'done']))
<div class="border border-transparent border-green-400 bg-green-100 text-green-600 text-center uppercase py-1 px-1">{{$type}}</div>
@elseif($type=='expired')
<div class="border border-transparent border-yellow-500 bg-yellow-100 text-yellow-600 text-center uppercase py-1 px-1">{{$type}}</div>
@elseif($type=='failed')
<div class="border border-transparent border-red-500 bg-red-100 text-red-600 text-center uppercase py-1 px-1">{{$type}}</div>
@elseif(in_array($type, ['working','submit']))
<div class="border border-transparent border-blue-500 bg-blue-100 text-blue-600 text-center uppercase py-1 px-1">{{$type}}</div>
@else
<div class="border border-gray-300 bg-gray-200 text-gray-600 text-center uppercase py-1 px-1">{{$type}}</div>
@endif
