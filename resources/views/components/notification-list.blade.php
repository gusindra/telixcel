@props(['team', 'component' => 'jet-dropdown-link'])

<div>
    <!-- It is not the man who has too little, but the man who craves more, that is poor. - Seneca -->
    <x-dynamic-component :component="$component" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
        <div class="flex items-center">
            <div class="truncate">Notice here...</div>
        </div>
        <div class="flex items-center">
            <div class="truncate">Notice here...</div>
        </div>
    </x-dynamic-component>
</div>
