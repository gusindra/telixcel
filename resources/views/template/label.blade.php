@if($type==='welcome' || $type=='new')
<div class="border-2 border-transparent border-green-500 bg-green-200 text-green-600 px-3">{{$type}}</div>
@elseif($type==='api')
<div class="border-2 border-transparent border-blue-500 bg-blue-100 text-blue-600 px-3">{{$type}}</div>
@elseif($type==='question')
<div class="border-2 border-transparent border-yellow-500 bg-yellow-100 text-yellow-600 px-3">{{$type}}</div>
@elseif($type==='error')
<div class="border-2 border-transparent border-red-500 bg-red-100 text-red-600 px-3">{{$type}}</div>
@elseif($type==='helper')
<div class="border-2 border-transparent border-green-400 bg-green-100 text-gray-600 px-3">{{$type}}</div>
@else
<div class="border-2 border-transparent border-gray-500 bg-gray-200 text-gray-600 px-3">{{$type}}</div>
@endif
