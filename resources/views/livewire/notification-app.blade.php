<div wire:poll.visible>
    <div class="relative">
        <x-jet-dropdown align="right" width="60">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white supports-backdrop-blur:bg-white/60 dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-600 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 dark:text-slate-300 dark:hover:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($data['count']>0)
                        <span id="icon-notif" class="absolute top-0 right-0 px-2 py-1 text-xs font-bold leading-none text-red-100 transform bg-red-600 rounded-full">{{$data['count']}}</span>
                        @endif
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <div class="w-60 overflow-y-auto h-auto max-h-96">
                    <!-- Notification -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Notification') }}
                    </div>
                    @foreach($data['waiting'] as $wait)
                        <a class="block px-4 py-2 text-sm font-bold leading-5 bg-pink-400 text-white hover:bg-pink-300 focus:outline-none focus:bg-pink-600 transition" href="{{route('message')}}?id={{Hashids::encode($wait->id)}}">
                            <div class="flex items-center">
                                <div class="truncate1">
                                    <span class="uppercase">Waiting</span> <br>
                                    <span class="capitalize text-xs" style="word-wrap: break-word;white-space: pre-wrap;word-break: break-all;">{{$wait->name}}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                    @if($data['notif']->count()>0)
                        @foreach($data['notif'] as $item)
                            <a class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:bg-blue-600  dark:text-slate-800 {{$item->status=='unread' ?'bg-green-200':''}} {{$item->status=='new' ?'bg-gray-200':''}} hover:bg-gray-300 focus:outline-none focus:bg-gray-100 transition" href="{{route('notification.read', [$item->id])}}">
                                <div class="flex items-center">
                                    <div class="truncate1">
                                        <span class="uppercase">{{$item->type}} : {{$item->ticket && $item->ticket->request && $item->ticket->request->client ? $item->ticket->request->client->name:''}}</span> <br>
                                        <span class="capitalize text-xs" style="word-wrap: break-word;white-space: pre-wrap;word-break: break-all;">{{$item->notification}}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach

                        <div class="block px-4 py-2 text-xs text-gray-600 text-center">
                            <a href="{{ route('notification') }}">{{ __('View more') }}</a>
                        </div>
                    @else
                        <div class="block px-4 py-2 text-xs text-gray-600 text-center">
                            <a href="{{ route('notification') }}">{{ __('View more') }}</a>
                        </div>
                    @endif
                </div>
            </x-slot>
        </x-jet-dropdown>
    </div>
    @if($data['count']>0)
        <style>
            .apply-shake {
                animation: shake 1s;
                animation-iteration-count: infinite;
            }

            @keyframes shake {
                0% { transform: translate(1px, 1px) rotate(0deg); }
                10% { transform: translate(-1px, -2px) rotate(-1deg); }
                20% { transform: translate(-3px, 0px) rotate(1deg); }
                30% { transform: translate(3px, 2px) rotate(0deg); }
                40% { transform: translate(1px, -1px) rotate(1deg); }
                50% { transform: translate(-1px, 2px) rotate(-1deg); }
                60% { transform: translate(-3px, 1px) rotate(0deg); }
                70% { transform: translate(3px, 1px) rotate(-1deg); }
                80% { transform: translate(-1px, -1px) rotate(1deg); }
                90% { transform: translate(1px, 2px) rotate(0deg); }
                100% { transform: translate(1px, -2px) rotate(-1deg); }
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                window.addEventListener('event-notification', event => {
                    var d = document.getElementById("icon-notif");
                    document.getElementById('sound').play();
                    console.log(1);
                    setTimeout((e) => {
                        var d = document.getElementById("icon-notif");
                        d.classList.add("apply-shake");
                    }, 3000);
                    d.classList.remove("apply-shake");
                });
            });
        </script>
    @endif
</div>
