@component('ai.layout')
    @include('ai.application-nav', ['application' => $application, 'section' => $section])

    @if($section === 'usage')
        @livewire('ai.usage-page', ['application' => $application])
    @elseif($section === 'requests')
        <x-page-section>
            <livewire:table.ai-requests :application-id="$application->id" searchable="request_id,model,end_user_name,end_user_id,status" />
        </x-page-section>
    @elseif($section === 'test')
        @livewire('ai.application-test-page', ['application' => $application])
    @else
        @livewire('ai.application-detail-page', ['application' => $application])
    @endif
@endcomponent
