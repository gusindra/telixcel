@php
    $isProject = request()->routeIs('project') || request()->routeIs('project.show');
    $isCommercial = request()->routeIs('commercial') || request()->routeIs('commercial.show') || request()->routeIs('commercial.edit.show');
    $isOrder = request()->routeIs('order') || request()->routeIs('show.order') || request()->routeIs('invoice') || request()->routeIs('show.invoice') || request()->routeIs('commission*');
    $isAdmin = Auth::user()->activeRole && str_contains(Auth::user()->activeRole->role->name ?? '', 'Admin');
    $isSuperAdmin = Auth::user()->activeRole && str_contains(Auth::user()->activeRole->role->name ?? '', 'Super Admin');
    $hasRoleLayer = @Auth::user()->role || Auth::user()->super->first();
    $isAssistant = request()->routeIs('assistant') || request()->routeIs('project*') || request()->routeIs('commercial*') || request()->routeIs('order') || request()->routeIs('show.order') || request()->routeIs('show.invoice') || request()->routeIs('invoice') || request()->routeIs('commission*');
@endphp

<aside class="tx-sidebar" :class="{ 'is-open': sidebarOpen }">
    <button
        type="button"
        class="tx-sidebar-edge"
        @click="sidebarCollapsed = !sidebarCollapsed"
        :aria-label="sidebarCollapsed ? '{{ __('Expand menu') }}' : '{{ __('Collapse menu') }}'"
    >
        <span class="material-symbols-outlined" x-text="sidebarCollapsed ? 'chevron_right' : 'chevron_left'">chevron_left</span>
    </button>
    <div class="tx-sidebar-brand flex h-16 shrink-0 items-center justify-between gap-3 px-4 lg:px-6">
        <a href="{{ route('dashboard') }}" class="tx-focus flex items-center gap-3 min-w-0 rounded-md focus:outline-none" @click="closeMobileNav()">
            <span class="tx-brand-mark" aria-hidden="true">T</span>
            <span class="tx-brand-text min-w-0">
                <span class="tx-brand-name block truncate">{{ config('app.name', 'Telixcel') }}</span>
            </span>
        </a>
        <button
            type="button"
            class="tx-iconbtn tx-sidebar-close h-9 w-9 rounded-lg"
            @click="sidebarOpen = false"
            aria-label="{{ __('Close menu') }}"
        >
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 pb-4 space-y-1">
        <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" :label="__('Dashboard')">
            <x-nav-icon name="dashboard" />
            <span class="tx-nav-label">{{ __('Dashboard') }}</span>
        </x-sidebar-link>

        @if ($hasRoleLayer && $isAdmin)
            <x-sidebar-link href="{{ route('user.index') }}" :active="request()->routeIs('user.index')" :label="__('Users')">
                <x-nav-icon name="users" />
                <span class="tx-nav-label">{{ __('Users') }}</span>
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('billing') }}" :active="request()->routeIs('billing')" :label="__('Billing')">
                <x-nav-icon name="billing" />
                <span class="tx-nav-label">{{ __('Billing') }}</span>
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('ai.applications') }}" :active="request()->routeIs('ai.*')" :label="__('AI Manager')">
                <x-nav-icon name="spark" />
                <span class="tx-nav-label">{{ __('AI Manager') }}</span>
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('agent') }}" :active="request()->routeIs('agent')" :label="__('AI Chat')">
                <x-nav-icon name="chat" />
                <span class="tx-nav-label">{{ __('AI Chat') }}</span>
            </x-sidebar-link>
        @endif

        <x-sidebar-link href="{{ route('ticket') }}" :active="request()->routeIs('ticket')" :label="__('Ticket')">
            <x-nav-icon name="ticket" />
            <span class="tx-nav-label">{{ __('Ticket') }}</span>
        </x-sidebar-link>

        @if (Auth::user()->activeRole)
            <div class="tx-nav-group"
                 :class="{ 'is-expanded': assistExpanded }"
                 x-init="assistExpanded = {{ $isAssistant ? 'true' : 'false' }}"
                 @mouseenter="openAssistFly($event.currentTarget)"
                 @mouseleave="scheduleCloseAssistFly()"
                 @focusin="openAssistFly($event.currentTarget)"
                 @focusout="scheduleCloseAssistFly()">
                <div class="tx-nav-parent">
                    <x-sidebar-link href="{{ route('assistant') }}" :active="$isAssistant" :label="__('Assistant')">
                        <x-nav-icon name="briefcase" />
                        <span class="tx-nav-label">{{ __('Assistant') }}</span>
                    </x-sidebar-link>
                    <button
                        type="button"
                        class="tx-nav-caret"
                        @click="assistExpanded = !assistExpanded"
                        :aria-expanded="assistExpanded.toString()"
                        aria-label="{{ __('Assistant submenu') }}"
                    >
                        <span class="material-symbols-outlined">expand_more</span>
                    </button>
                </div>

                <div class="tx-nav-children ml-5 mt-1 pl-3 border-l tx-border space-y-1">
                    @include('layouts.partials.assistant-links')
                </div>
            </div>
        @else
            <x-sidebar-link href="{{ route('client') }}" :active="request()->routeIs('client')" :label="__('Customers')">
                <x-nav-icon name="users" />
                <span class="tx-nav-label">{{ __('Customers') }}</span>
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('template') }}" :active="request()->routeIs('template')" :label="__('Templates')">
                <x-nav-icon name="file" />
                <span class="tx-nav-label">{{ __('Templates') }}</span>
            </x-sidebar-link>
            @if (Auth::user()->currentTeam && Auth::user()->currentTeam->user_id == Auth::user()->id)
                <x-sidebar-link href="{{ route('billing') }}" :active="request()->routeIs('billing')" :label="__('Report')">
                    <x-nav-icon name="billing" />
                    <span class="tx-nav-label">{{ __('Report') }}</span>
                </x-sidebar-link>
            @endif
        @endif

        @if ($isSuperAdmin)
            <x-sidebar-link href="{{ route('settings') }}" :active="request()->routeIs('settings')" :label="__('Settings')">
                <x-nav-icon name="settings" />
                <span class="tx-nav-label">{{ __('Settings') }}</span>
            </x-sidebar-link>
        @endif
    </nav>
</aside>

<div
    class="tx-assist-pop"
    x-ref="assistFly"
    x-show="assistFlyOpen && sidebarCollapsed && isLg()"
    x-cloak
    @mouseenter="cancelCloseAssistFly()"
    @mouseleave="scheduleCloseAssistFly()"
>
    <div class="tx-nav-flyout-title">{{ __('Assistant') }}</div>
    @include('layouts.partials.assistant-links')
</div>
