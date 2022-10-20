<div>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <div class="flex">
                <div class="hidden md:block round px-2 py-1 mb-3
                {{agentStatus($team->agents)=='Online'?'bg-green-200 dark:bg-transparent border dark:border-white/40':''}}
                {{agentStatus($team->agents)=='Away'?'bg-yellow-200 dark:bg-transparent border dark:border-white/40':''}}
                {{agentStatus($team->agents)=='Offline'?'bg-gray-200 dark:bg-transparent border dark:border-white/40':''}}
                rounded">
                    {{agentStatus($team->agents)}}
                </div>
                @if($name=='' && $number=='')
                <button x-cloak x-on:click="darkMode==='true' || darkMode==true ? darkMode=false : darkMode=true;" onclick="showhide()" class="px-4 py-1 mb-4">
                    <!-- Icon Moon -->
                    <svg id="moon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <!-- Icon Sun -->
                    <svg id="sun" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
                @endif
            </div>
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        @if(!$client)
            <div class="p-6">
                <div>
                    <x-jet-label for="name" value="{{ __('Name') }}" />
                    <x-jet-input
                    wire:model="name" wire:model.defer="name"
                    id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"  required autofocus />
                </div>

                <div class="mt-4">
                    <x-jet-label for="phone" value="{{ __('Phone Number') }}" />
                    <x-jet-input
                    wire:model="number" wire:model.defer="number"
                    id="phone" class="block mt-1 w-full" type="text" name="phone" required autocomplete="current-phone" />
                </div>

                <div class="items-center py-3 text-center mt-4">
                    <button type="submit" class="inline-flex1 w-full items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition px-4 py-3" wire:click="checkClient">
                        {{ __('Message') }}
                    </button>
                </div>
            </div>
        @endif
        <div>
            <div class="{{$data && $data->id?'block':'hidden'}}">
                <div class="bg-gray-400 dark:bg-slate-700 h-12 lg:h-14 lg:static md:static sm:fixed sm:inset-x-0 sm:top-0 shadow-md">
                    <div class="w-full mx-auto p-1">
                        <div class="flex items-center justify-between flex-wrap">
                            <div class="w-0 flex-1 flex items-center">
                            @if($client)
                                <p class="ml-3 font-medium text-white truncate">
                                    <div class="md:h-auto text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition pr-3">
                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $team->profile_photo_url }}" alt="{{ $team->name }}" />
                                    </div>
                                    <span class="hidden md:inline">
                                        {{$client->name}}
                                    </span>
                                </p>
                            @endif
                            </div>
                            <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                                <button type="button" class="mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2"></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="messageArea" class="lg:max-h-screen">
                    <div wire:poll.visible id="messageBox" class="overflow-auto h-96 bg-green-50 dark:bg-slate-600" style="display: flex;flex-direction: column;height: 80vh;overflow: auto;">
                        @if(!$transcript && count($messages)>10)
                        <p class="text-center dark:bg-slate-300 {{$requestTransript ? 'bg-gray-600 text-gray-100' : 'bg-gray-200 text-gray-800'}}">
                            @if(is_null($requestTransript))
                                <a class="text-xs p-4 underline cursor-pointer" wire:click="requestTransript">See transcript</a>
                            @elseif($requestTransript=='requested')
                                <a class="text-xs p-4 underline cursor-pointer" wire:click="requestTransript">Your request was submited, click to recheck status!</a>
                            @elseif($requestTransript=='reject')
                                <a class="text-xs p-4 underline">Your request was rejected</a>
                            @endif
                        </p>
                        @endif
                        @foreach($messages as $item)
                        <div class="pb-1 px-4 sm:p-2 sm:px-3 buble-chat object-left {{$item->source_id?'':'text-right right-0'}}">
                            <small class="text-gray-500 dark:text-slate-300 font-medium">{{$item->source_id?$client->name:($item->from=='bot' || $item->from=='api'?'Bot':$item->agent->name)}}</small>
                            <div class="flex justify-between">
                                <div class="text-sm flex-auto z-9 p-2 xl:p-3 bg-gradient-to-br {{$item->source_id?'items-start':'order-last text-right'}} {{$item->source_id?'from-green-100 to-green-200 rounded-tr-lg rounded-b-lg':'from-indigo-100 to-indigo-200 dark:bg-slate-700 rounded-b-lg rounded-tl-lg right-0'}}">
                                    @if($item->type=='image')
                                        <img src="{{$item->media}}" class="bg-gray-100 max-w-400 min-w-1/2 right-0 order-last" />
                                    @elseif($item->type=='document')
                                        <div class="flex justify-between">
                                            <div><img src="{{url('/backend/img/'.substr(strrchr($item->media, '.'), 1).'.png')}}" class="h-8" /></div>
                                            <div class="text-left mt-1">{{substr(strrchr($item->media, '/'), 1)}}</div>
                                            <div><a href="{{$item->media}}" data-testid="audio-download" data-icon="audio-download" class=""><svg viewBox="0 0 34 34" width="34" height="34" class=""><path fill="currentColor" d="M17 2c8.3 0 15 6.7 15 15s-6.7 15-15 15S2 25.3 2 17 8.7 2 17 2m0-1C8.2 1 1 8.2 1 17s7.2 16 16 16 16-7.2 16-16S25.8 1 17 1z"></path><path fill="currentColor" d="M22.4 17.5h-3.2v-6.8c0-.4-.3-.7-.7-.7h-3.2c-.4 0-.7.3-.7.7v6.8h-3.2c-.6 0-.8.4-.4.8l5 5.3c.5.7 1 .5 1.5 0l5-5.3c.7-.5.5-.8-.1-.8z"></path></svg></a></div>
                                        </div>
                                    @elseif($item->type=='video')
                                        <video width="320" height="240" controls>
                                            <source src="{{$item->media}}" type="video/mp4">
                                            <source src="{{$item->media}}" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                    @elseif($item->type=='audio')
                                        <audio controls>
                                            <source src="{{$item->media}}" type="audio/ogg">
                                            <source src="{{$item->media}}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    @else
                                        <span class="whitespace-pre-wrap dark:text-slate-600 text-base">{!!chatRender($item)!!}</span><br>
                                    @endif
                                    <p class="px-1 text-xs font-thin text-gray-400 dark:text-slate-500 {{$item->source_id?'text-right right-0':'text-left left-0'}}">{{$item->date}}</p>
                                </div>
                                <div class="flex-1"></div>
                            </div>
                        </div>
                        @endforeach

                    </div>

                    <div class="bg-gray-200 z-10 dark:bg-slate-700 py-1 grid grid-cols-8 lg:static md:static sm:fixed sm:inset-x-0 sm:bottom-0">
                        <div class="flex items-center justify-center col-span-1 align-text-bottom">
                            <button class="cursor-pointer text-sm text-grey-500 dark:text-slate-300 p-1" wire:click="actionShowModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </button>
                        </div>
                        <div data-emojiarea data-type="unicode" data-global-picker="false" class="flex py-2 col-span-6 pr-2" >
                            <div class="flex items-center col-span-1 align-text-bottom emoji-button cursor-pointer text-sm text-grey-500 dark:text-slate-300 p-1">
                                <button class="cursor-pointer text-sm text-grey-500 dark:text-slate-300 p-1" >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                                    </svg>
                                </button>
                            </div>
                            <textarea
                                id="message"
                                class="mt-1 block w-full text-sm border-none focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:bg-slate-800 dark:text-slate-300"
                                placeholder="{{ __('Write a reply...') }}"
                                wire:model="message"
                            ></textarea>
                        </div>
                        @if($message!='')
                        <div class="flex items-center justify-center col-span-1 align-text-bottom">
                            <button class="p-2 sm:p-2 md:p-2 lg:p-2 bg-green-600 dark:bg-transparent text-white rounded-full" wire:click="sendMessage">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 rotate-30" viewBox="0 0 20 20" fill="currentColor">
                                    <path style="transform: rotate(90deg);transform-origin: 50% 50%;" d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                </svg>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Form Action Modal -->
                <x-jet-dialog-modal wire:model="modalAttachment">
                    <x-slot name="title">
                        {{ __('Attachment message') }}
                    </x-slot>

                    <x-slot name="content">
                        <div class="col-span-6 sm:col-span-4 p-3">
                            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                                <!-- Profile Photo File Input -->
                                <input type="file" class="hidden"
                                            wire:model="photo"
                                            x-ref="photo"
                                            x-on:change="
                                                    photoName = $refs.photo.files[0].name;
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => {
                                                        photoPreview = e.target.result;
                                                    };
                                                    reader.readAsDataURL($refs.photo.files[0]);
                                            " />

                                <x-jet-label for="photo" value="{{ __('Photo') }}" />

                                <!-- New Profile Photo Preview -->
                                <div class="mt-2" x-show="photoPreview">
                                    <span class="block h-48 w-full"
                                        x-bind:style="'background-size: contain; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                                    </span>
                                </div>

                                <x-jet-secondary-button class="mt-2 mr-2 dark:bg-slate-900" type="button" x-on:click.prevent="$refs.photo.click()">
                                    {{ __('Select A New Photo') }}
                                </x-jet-secondary-button>

                                <x-jet-input-error for="photo" class="mt-2" />
                            </div>
                            <x-textarea wire:model="message"
                                        wire:model.defer="message"
                                        value="message" class="mt-1 block w-full" placeholder="Caption"></x-textarea>
                            <x-jet-input-error for="name" class="mt-2" />
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        <x-jet-secondary-button wire:click="$toggle('modalAttachment')" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </x-jet-secondary-button>
                        <x-jet-button class="ml-2" wire:click="sendAttachment" wire:loading.attr="disabled">
                            {{ __('Send') }}
                        </x-jet-button>
                    </x-slot>
                </x-jet-dialog-modal>
            </div>
        </div>
    </x-jet-authentication-card>
    <script>
        setTimeout(function (){
            if($('#messageBox').is(':visible')){ //if the container is visible on the page
                var d = $('#messageBox');
                d.scrollTop(d.prop("scrollHeight"));
            }
        }, 100);
        var m = document.getElementById("moon");
        var s = document.getElementById("sun");
        function showhide() {
            if (m.style.display === "none") {
                m.style.display = "block";
                s.style.display = "none";
            } else {
                m.style.display = "none";
                s.style.display = "block";
            }
        }
        var dark = localStorage.getItem("alertbox");
        if(dark == true){
            m.style.display = "block";
            s.style.display = "none";
        } else {
            m.style.display = "none";
            s.style.display = "block";
        }
        var out = document.getElementById("messageBox");

        window.addEventListener('contentChanged', (e) => {
            out.scrollTop = out.scrollHeight - out.clientHeight;
        });
    </script>
</div>
