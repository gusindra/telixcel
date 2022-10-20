<div>
    <!-- Header Box -->
    <div class="bg-gray-400 dark:bg-slate-800 h-10 lg:static md:static sm:fixed sm:inset-x-0 sm:top-0 shadow-md">
        <div class="w-full mx-auto">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 flex items-center">
                @if($client)
                    <p class="ml-3 font-medium text-white truncate">
                        <div class="md:h-auto text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition pr-3 mt-1">
                            <img class="h-7 w-7 rounded-full object-cover" src="{{ $client->profile_photo_url }}" alt="{{ $client->name }}" />
                        </div>
                        <span class="mt-1 text-white">
                            {{$client->name}}
                        </span>
                    </p>
                @endif
                </div>
                @if(@$handling_session)
                    @if($handling_session->agent_id == $user_id && $handling_session->client_id == $client_id)
                    <button class="mr-2 p-1 border-transparent bg-red-500 hover:bg-red-800 text-white" title="Close Session" wire:click="showModalConfirmation">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Chat Log -->
    <div id="messageArea" class="h-1/2">
        @if(@$handling_session && $handling_session->agent_id == $user_id && $handling_session->client_id == $client_id && $data['count']>2)
            @if($handling_session->view_transcript=="requested")
            <p class="text-center dark:bg-slate-300 bg-yellow-500 text-gray-100">
                <span class="text-xs">Client want to see transcript, give the permission ?  </span>
                <span class="text-right">
                    <a class="text-xs p-4 underline" href="#" wire:click="updateTransript('no')">No</a>
                    <a class="text-xs p-4 underline" href="#" wire:click="updateTransript('yes')">Yes</a>
                </span>
            </p>
            @endif
        <p class="text-center dark:bg-slate-300 {{$transcript ? 'bg-gray-600 text-gray-100' : 'bg-gray-200 text-gray-800'}}">
            <a class="text-xs p-4 underline" href="#" wire:click="showTransript">{{$transcript ? 'Show less':'See transcript'}}</a>
        </p>
        @endif
        <div id="messageBox" wire:poll.visible class="overflow-auto h-screen bg-green-50 dark:bg-slate-700 py-0" style="display: flex;flex-direction: column;;overflow: auto;">
            @foreach($data['request'] as $item)
                <div class="px-4 sm:px-4 buble-chat object-left group {{preg_match("/[a-z]/i", $item->source_id)?'':'text-right1 right-0'}}">
                    <p class="{{preg_match("/[a-z]/i", $item->source_id)?'':'text-right right-0'}}"><small class="text-gray-500 dark:text-slate-300 font-medium">{{preg_match("/[a-z]/i", $item->source_id)?$client->name:($item->from=='bot' || $item->from=='api'?'Bot':@$item->agent->name)}}</small></p>
                    <div class="flex justify-between">
                        <div class="text-sm flex-auto z-9 p-2 xl:p-3 bg-gradient-to-br {{preg_match("/[a-z]/i", $item->source_id)?'items-start':'order-last text-right1'}} {{preg_match("/[a-z]/i", $item->source_id)?'from-green-100 to-green-200 rounded-tr-lg rounded-b-lg':'from-indigo-100 to-indigo-200 rounded-b-lg rounded-tl-lg right-0'}}">
                            @if($item->type=='image')
                            <div class="flex justify-center">
                                <div class="order-last">
                                    <img src="{{$item->media}}" class="shadow-lg" width="300" />
                                </div>
                                <div></div>
                            </div>
                            @endif
                            <div class="{{$item->source_id?($item->type!=='text'?'rounded-tr-lg rounded-b-lg':''):($item->type!=='text'?'from-indigo-100 to-indigo-200 rounded-sm right-0 p-4':'')}} mb-1 flex items-stretch justify-between w-100 whitespace-pre-wrap">
                                <div class="flex flex-row">
                                    @if($item->type=='document')
                                        <img src="{{url('/backend/img/'.substr(strrchr($item->media, '.'), 1).'.png')}}" class="h-8" />
                                    @endif
                                    <span class="{{$item->type=='document' ? 'ml-2 mt-2':''}} dark:text-slate-600">{!!chatRender($item)!!}</span>
                                </div>
                                @if($item->media!='')
                                    @if($item->type=='document')
                                        <a href="{{$item->media}}" data-testid="audio-download" data-icon="audio-download" class=""><svg viewBox="0 0 34 34" width="34" height="34" class=""><path fill="currentColor" d="M17 2c8.3 0 15 6.7 15 15s-6.7 15-15 15S2 25.3 2 17 8.7 2 17 2m0-1C8.2 1 1 8.2 1 17s7.2 16 16 16 16-7.2 16-16S25.8 1 17 1z"></path><path fill="currentColor" d="M22.4 17.5h-3.2v-6.8c0-.4-.3-.7-.7-.7h-3.2c-.4 0-.7.3-.7.7v6.8h-3.2c-.6 0-.8.4-.4.8l5 5.3c.5.7 1 .5 1.5 0l5-5.3c.7-.5.5-.8-.1-.8z"></path></svg></a>
                                    @endif
                                    @if($item->type=='video')
                                        <video width="320" height="240" controls>
                                            <source src="{{$item->media}}" type="video/mp4">
                                            <source src="{{$item->media}}" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                    @if($item->type=='audio')
                                        <audio controls>
                                            <source src="{{$item->media}}" type="audio/ogg">
                                            <source src="{{$item->media}}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    @endif
                                @endif
                            </div>
                            <p class="px-3 text-xs font-thin text-gray-400 {{$item->source_id?'text-right right-0':'text-left left-0'}} dark:text-slate-500">{{$item->date}}</p>

                        </div>
                        <div class="flex-1">
                        @if(@$handling_session)
                            @if($handling_session->agent_id == $user_id && $handling_session->client_id == $client_id)
                                @if($item->activeTickets->count()==0 && $item->source_id)
                                <section class="max-w-sm mx-auto rounded-lg shadow-sm flex flex-row items-center ml-2">
                                    <aside class="invisible group-hover:visible h-full text-center flex-grow flex flex-col text-sm ml-auto divide-y relative rounded-md">
                                        <a class="text-blue-500 hover:text-blue-700 h-1/2 flex items-center text-left text-xs dark:bg-slate-800 w-1/2 p-1" href="#" wire:click="ticketShowModal({{ $item->id }}, '{{ $item->reply }}')">Convert Ticket</a>
                                        <a class="text-blue-500 hover:text-blue-700 h-1/2 flex items-center text-left text-xs dark:bg-slate-800 w-1/2 p-1" href="#" wire:click="forwardShowModal({{ $item->id }}, '{{ $item->reply }}')">Forward</a>
                                        <!-- <a class="text-red-500 hover:text-red-700 h-1/2  flex items-center text-left text-xs" href="#">Forward</a> -->
                                    </aside>
                                </section>
                                @endif
                            @endif
                        @endif
                        </div>
                    </div>
                </div>
                @if($item->tickets)
                <div class="w-100 sticky top-0 z-9 bottom-0">
                    @foreach($item->tickets as $ticket)
                        @if(@$handling_session)
                            @if($handling_session->agent_id == $user_id && $handling_session->client_id == $client_id)
                                @if ($ticket->status=='open')
                                    <a class="mt-4 flex justify-center gap-x-2 text-xs text-gray-100 px-10 py-1 mb-0 bg-red-600 hover:bg-red-800 cursor-pointer" wire:click="ticketUpdateModal({{$ticket->request_id}}, {{$ticket->id}}, '{{$ticket->reasons}}', '{{$ticket->status}}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                        </svg>
                                        <span class="flex-initial"> Ticket #{{$ticket->request_id}}</span>
                                    </a>
                                @elseif($ticket->status=='waiting')
                                    <a class="flex mt-4 justify-center gap-x-2 text-xs text-gray-100 px-10 py-1 mb-4 bg-blue-600 hover:bg-blue-800 cursor-pointer" wire:click="ticketUpdateModal({{$ticket->request_id}}, {{$ticket->id}}, '{{$ticket->reasons}}', '{{$ticket->status}}', '{{$ticket->forward_to}}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="flex-initial">
                                            <span>{{$ticket->created_at->format('d M Y')}}</span>
                                            Forward to <strong>{{$ticket->forwardUser->name}}</strong>
                                        </span>
                                    </a>
                                @endif
                            @endif
                        @else
                            @if ($ticket->status=='open')
                                    <a class="flex mt-4 justify-center gap-x-2 text-xs text-gray-100 px-10 py-1 mb-0 bg-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                        </svg>
                                        <span class="flex-initial"> Ticket #{{$ticket->request_id}}</span>
                                    </a>
                                @elseif($ticket->status=='waiting')
                                    <a class="flex mt-4 justify-center gap-x-2 text-xs text-gray-100 px-10 py-1 mb-4 bg-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="flex-initial">
                                            <span>{{$ticket->created_at->format('d M Y')}}</span>
                                            Forward to <strong>{{$ticket->forwardUser->name}}</strong>
                                        </span>
                                    </a>
                                @endif
                        @endif
                    @endforeach
                </div>
                @endif
            @endforeach
            <br>
            <div class="hidden md:block">
                <br><br><br><br><br><br><br>
            </div>
        </div>
        @if($client)
            <div class="{{count($data['quick'])==0?'hidden':''}} lg:absolute lg:bottom-24 bottom-1 lg:w-2/4 w-100">
                <div class="relative z-10 w-full mt-1 bg-gray-200 rounded-md shadow-lg top-0 border-2 border-gray-400">
                    <ul class="p-0 overflow-auto h-auto max-h-screen text-base leading-6 rounded-md shadow-xs focus:outline-none sm:text-sm sm:leading-5">
                        @foreach ($data['quick'] as $quick)
                            <li role="option" class="relative text-gray-900 cursor-default select-none border border-gray-300">
                                <button wire:click="showQuickModal({{$quick->id}})" class="py-2 pl-3 text-xs text-gray-900 cursor-default select-none pr-9 w-full text-left focus:text-white focus:bg-indigo-600 hover:text-white hover:bg-indigo-600">
                                    {{$quick->name}} :
                                    @foreach ($quick->actions as $action)
                                        {{$action->message}}
                                    @endforeach
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="py-3 grid grid-cols-8 z-10 md:static sm:fixed sm:inset-x-0 sm:bottom-0 lg:fixed lg:bottom-0 dark:bg-slate-800">
                @if(@$handling_session)
                    @if($handling_session->agent_id == $user_id && $handling_session->client_id == $client_id)
                    <div class="flex items-center justify-center col-span-1 align-text-bottom">
                        <button class="cursor-pointer text-sm text-grey-500 p-2" wire:click="actionShowModal">
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
                        <x-textarea
                            id="message"
                            class="mt-1 block w-full h-full text-sm"
                            placeholder="{{ __('write a reply...') }}"
                            wire:model="message"
                        />
                    </div>
                    <div class="flex items-center justify-center col-span-1 align-text-bottom">
                        <button class="p-2 sm:p-2 md:p-4 lg:p-4 bg-green-600 text-white rounded-full" wire:click="sendMessage">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 rotate-30" viewBox="0 0 20 20" fill="currentColor">
                                    <path style="transform: rotate(90deg);transform-origin: 50% 50%;" d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
                    </div>
                    @elseif($handling_session->agent_id == $user_id)
                    <div class="flex w-full items-center justify-center col-span-12 my-6 text-sm text-gray-400">
                        <p class="px-4">You still have active conversation. Please end last chat before join new conversation</p>
                    </div>
                    @else
                    <div class="flex w-full items-center justify-center col-span-12 my-6 text-sm text-gray-400">
                        <p class="px-4">This conversation already handle by other Agent.</p>
                    </div>
                    @endif
                @else
                    <div class="justify-center w-100 flex col-span-12 mt-3">
                        <x-jet-action-message class="mr-3" on="handle">
                            {{ __('Other agent already join conversation.') }}
                        </x-jet-action-message>
                        <x-jet-action-message class="mr-3" on="exist">
                            {{ __('You still have active conversation.') }}
                        </x-jet-action-message>
                    </div>
                    <div class="justify-center w-100 flex col-span-12 mt-3">
                        <button class="bg-green-600 text-white border border-gray-300 px-4 py-2" wire:click="joinChat">
                            {{__('Join')}}
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Attachment Modal -->
    <x-jet-dialog-modal wire:model="modalAttachment">
        <x-slot name="title">
            {{ __('Attachment message') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                    <!-- Profile Photo File Input -->
                    <input type="file"
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

                    <!-- <x-jet-label for="photo" value="{{ __('Photo') }}" /> -->

                    <!-- New Profile Photo Preview -->
                    <div class="mt-2" x-show="photoPreview">
                        <span class="block h-48 w-full"
                            x-bind:style="'background-size: contain; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <!-- <x-jet-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                        {{ __('Select A New Photo') }}
                    </x-jet-secondary-button> -->

                    <x-jet-input-error for="photo" class="mt-2" />
                </div>
                @if(!$photo)
                <x-jet-label for="photo" value="{{ __('Link') }}" />
                <input class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 w-full my-1 text-sm" type="text" placeholder="{{ __('Link Attachment') }}"
                            x-ref="link_attachment"
                            wire:model.defer="link_attachment">
                @endif
                <x-jet-label for="photo" value="{{ __('Caption') }}" />
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

    <!-- Form Add Ticket Modal -->
    <x-jet-dialog-modal wire:model="modalTicket">
        <x-slot name="title">
            {{ __('Open Ticket') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <label for="no">Ticket No</label>
                <input wire:model="request_id" wire:model.defer="request_id" value="request_id" readonly />
                <x-textarea wire:model="reason"
                            wire:model.defer="reason"
                            value="reason" class="mt-1 block w-full" placeholder="Reason"></x-textarea>
                <x-jet-input-error for="name" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalTicket')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="sendTicket" wire:loading.attr="disabled">
                {{ __('Submit') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Update Ticket Modal -->
    <x-jet-dialog-modal wire:model="modalUpdateTicket">
        <x-slot name="title">
            {{ __('Update ticket') }}
        </x-slot>
        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <label for="no">Ticket No <input class="text-center" wire:model="request_id" wire:model.defer="request_id" value="request_id" readonly /></label>

                <x-jet-label for="ticket_status" value="{{ $ticket_reason }}" />
                <select
                    name="ticket_status"
                    id="ticket_status"
                    class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="ticket_status"
                    >
                    <option selected>-- Select status --</option>
                    @if($ticket_type==null)
                    <option value="open">Open</option>
                    <option value="handled">Handled</option>
                    @endif
                    <option value="close">Close</option>
                </select>
                @if($ticket_type==null)
                <x-jet-input-error for="ticket_status" class="mt-2" />
                <x-textarea wire:model="ticket_solution"
                            wire:model.defer="ticket_solution"
                            value="ticket_solution" class="mt-1 block w-full" placeholder="Solution"></x-textarea>
                <x-jet-input-error for="name" class="mt-2" />
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalUpdateTicket')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="ticketUpdate" wire:loading.attr="disabled">
                {{ __('Update') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Form Forward Modal -->
    <x-jet-dialog-modal wire:model="modalForward">
        <x-slot name="title">
            {{ __('Foward Message') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <label for="no">Select Team Member / Divisi</label>
                <select
                    name="forward_to"
                    id="forward_to"
                    class="border-gray-300 focus:border-indigo-300 focus:ring dark:bg-slate-800 focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="forward_to"
                    >
                    <option selected>-- Select agent --</option>
                    @foreach ($team->users->sortBy('name') as $user)
                        @if(auth()->user()->email != $user->email)
                            <option value="{{$user->id}}">{{ $user->email .' ( '. $user->name.' )' }}</option>
                        @endif
                    @endforeach
                </select>
                <label for="no">Notes</label>
                <x-textarea wire:model="reason"
                            wire:model.defer="reason"
                            value="reason" class="mt-1 block w-full" placeholder="Reason"></x-textarea>
                <x-jet-input-error for="name" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalForward')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="sendForward" wire:loading.attr="disabled">
                {{ __('Submit') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Close Session Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="closeModal">
        <x-slot name="title">
            {{ __('Close Chat Session') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to finish this session?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('closeModal')" wire:loading.attr="disabled">
                {{ __('later') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="closeChat(1)" wire:loading.attr="disabled">
                {{ __('Yes') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
    <script>
        window.addEventListener('contentChanged', (e) => {
            var out = document.getElementById("messageBox");
            out.scrollTop = out.scrollHeight;
            out.scrollTop = out.scrollHeight;
        });
    </script>
</div>
