{{--
    Sidebar shell (Material 3 style). To restore the top-nav Jetstream layout:
    rename this file, then rename layouts/app_old.blade.php to app.blade.php.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-cloak
      x-data="{
          darkMode: localStorage.getItem('dark') === 'true',
          sidebarOpen: false,
          sidebarCollapsed: localStorage.getItem('tx-sidebar-collapsed') === '1',
          assistExpanded: false,
          assistFlyOpen: false,
          assistFlyTimer: null,
          isLg() { return window.matchMedia('(min-width: 1024px)').matches; },
          closeMobileNav() { if (!this.isLg()) this.sidebarOpen = false; },
          openAssistFly(group) {
              if (!this.isLg() || !this.sidebarCollapsed) return;
              var fly = this.$refs.assistFly;
              if (!fly || !group) return;
              if (this.assistFlyTimer) { clearTimeout(this.assistFlyTimer); this.assistFlyTimer = null; }
              this.assistFlyOpen = true;
              var r = group.getBoundingClientRect();
              fly.style.position = 'fixed';
              fly.style.top = r.top + 'px';
              fly.style.left = (r.right - 4) + 'px';
              this.$nextTick(function () {
                  var h = fly.offsetHeight || 0;
                  fly.style.top = Math.max(8, Math.min(r.top, window.innerHeight - h - 8)) + 'px';
              });
          },
          scheduleCloseAssistFly() {
              var self = this;
              if (this.assistFlyTimer) clearTimeout(this.assistFlyTimer);
              this.assistFlyTimer = setTimeout(function () { self.assistFlyOpen = false; }, 180);
          },
          cancelCloseAssistFly() {
              if (this.assistFlyTimer) { clearTimeout(this.assistFlyTimer); this.assistFlyTimer = null; }
          },
          closeAssistFly() {
              this.assistFlyOpen = false;
              if (this.assistFlyTimer) { clearTimeout(this.assistFlyTimer); this.assistFlyTimer = null; }
          }
      }"
      x-init="$watch('darkMode', val => localStorage.setItem('dark', val)); $watch('sidebarCollapsed', val => { localStorage.setItem('tx-sidebar-collapsed', val ? '1' : '0'); closeAssistFly(); }); var closeFly = function () { closeAssistFly(); }; window.addEventListener('scroll', closeFly, true); document.addEventListener('scroll', closeFly, true);"
      :class="{ 'dark': darkMode, 'tx-nav-open': sidebarOpen, 'tx-nav-collapsed': sidebarCollapsed }"
      :data-dark="darkMode">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Telixcel') }}</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block">
    <link rel="stylesheet" href="{{ url('backend/css/offcanvas.scss') }}">
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
    <link rel="stylesheet" href="{{ url('css/tail.css') }}">
    <style>
        [x-cloak] { display: none !important; }

        /* ---- Telixcel shell: Material 3 tokens (light + designed dark) ---- */
        :root {
            --tx-bg:            #f6f8fe;
            --tx-surface:       #ffffff;
            --tx-surface-1:     #f1f4fb;
            --tx-border:        #d7dce7;
            --tx-border-strong: #c3c9d6;
            --tx-fg:            #1a1c1e;
            --tx-fg-muted:      #464a52;
            --tx-fg-subtle:     #767b85;
            --tx-primary:       #005ac2;
            --tx-primary-hover: #0a4ea1;
            --tx-on-primary:    #ffffff;
            --tx-hover:         #eceff6;
            --tx-active:        rgba(0, 90, 194, .09);
            --tx-ring:          #005ac2;
            --tx-header:        rgba(255, 255, 255, .92);
            --tx-header-h:      56px;
            --tx-sidebar-w:     240px;
            --tx-sidebar-rail:  72px;
        }
        .dark {
            --tx-bg:            #020617;
            --tx-surface:       #051424;
            --tx-surface-1:     #0f172a;
            --tx-border:        #1e293b;
            --tx-border-strong: #334155;
            --tx-fg:            #d4e4fa;
            --tx-fg-muted:      #a3adbf;
            --tx-fg-subtle:     #6b7688;
            --tx-primary:       #adc6ff;
            --tx-primary-hover: #4d8eff;
            --tx-on-primary:    #002e6a;
            --tx-hover:         #122131;
            --tx-active:        rgba(173, 198, 255, .10);
            --tx-ring:          #4d8eff;
            --tx-header:        rgba(5, 20, 36, .92);
            --tx-header-h:      56px;
        }

        .tx-root       { background: var(--tx-bg); color: var(--tx-fg); }
        .tx-canvas     { background: var(--tx-bg); }
        .tx-surface    { background: var(--tx-surface); }
        .tx-surface-1  { background: var(--tx-surface-1); }
        .tx-fg         { color: var(--tx-fg); }
        .tx-fg-muted   { color: var(--tx-fg-muted); }
        .tx-fg-subtle  { color: var(--tx-fg-subtle); }
        .tx-border     { border-color: var(--tx-border) !important; }

        html, body { max-width: 100%; }
        html.tx-nav-open, html.tx-nav-open body { overflow: hidden; }
        .tx-canvas, .tx-main, .tx-page, .tx-section, .tx-card {
            min-width: 0;
            max-width: 100%;
        }

        /* ---- mobile: drawer menu (default) ---- */
        .tx-sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; height: 100dvh;
            width: min(300px, 86vw); z-index: 50;
            display: flex; flex-direction: column;
            background: var(--tx-surface); border-right: 1px solid var(--tx-border);
            transform: translateX(-105%);
            transition: transform .22s cubic-bezier(.16, 1, .3, 1);
        }
        .tx-sidebar.is-open { transform: translateX(0); box-shadow: 0 24px 60px -20px rgba(0,0,0,.45); }
        .tx-sidebar-edge { display: none; }
        .tx-scrim {
            background: rgba(2, 6, 23, .5);
        }
        .tx-main {
            display: flex; flex-direction: column;
            min-height: 100vh; min-height: 100dvh; min-width: 0; width: 100%;
            padding-top: var(--tx-header-h);
        }
        .tx-header {
            position: fixed; top: 0; left: 0; right: 0;
            z-index: 40; height: var(--tx-header-h);
            display: flex; align-items: center; gap: 6px; padding: 0 10px;
            background: var(--tx-header);
            border-bottom: 1px solid var(--tx-border);
            overflow: visible;
        }
        .tx-header::before {
            content: ""; position: absolute; inset: 0; z-index: -1; pointer-events: none;
            background: var(--tx-header);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        }
        .jetstream-modal { z-index: 200 !important; }
        .tx-page { flex: 1 1 auto; min-width: 0; padding: 12px 12px 32px; }
        .tx-main:has(.agent-nova) {
            height: 100vh; height: 100dvh; overflow: hidden;
        }
        .tx-page:has(.agent-nova) {
            display: flex; flex-direction: column;
            min-height: 0; overflow: hidden;
            padding: 12px;
        }
        .tx-nav-flyout-title { display: none; }
        .tx-nav-parent { display: flex; align-items: center; gap: 2px; }
        .tx-nav-parent > .tx-nav { flex: 1 1 auto; min-width: 0; }
        .tx-nav-caret {
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; flex: none;
            border: 0; background: transparent; color: var(--tx-fg-muted);
            border-radius: 8px; cursor: pointer;
        }
        .tx-nav-caret:hover { background: var(--tx-hover); color: var(--tx-fg); }
        .tx-nav-caret .material-symbols-outlined { font-size: 22px; transition: transform .16s ease; }
        .tx-nav-group.is-expanded .tx-nav-caret .material-symbols-outlined { transform: rotate(180deg); }
        .tx-nav-children { display: none; }
        .tx-nav-group.is-expanded .tx-nav-children { display: block; }
        .tx-sidebar-close { display: inline-flex; }

        @media (min-width: 640px) {
            :root, .dark { --tx-header-h: 60px; }
            .tx-header { padding: 0 16px; }
            .tx-page { padding: 16px 16px 40px; }
        }

        /* ---- desktop: persistent sidebar + rail ---- */
        @media (min-width: 1024px) {
            .tx-sidebar {
                transform: none; box-shadow: none; overflow: visible;
                width: var(--tx-sidebar-w);
                transition: width .22s cubic-bezier(.16, 1, .3, 1);
            }
            .tx-sidebar-edge {
                display: inline-flex; position: absolute; top: 22px; right: -12px; z-index: 55;
                width: 24px; height: 24px; padding: 0;
                align-items: center; justify-content: center;
                border-radius: 999px; border: 1px solid var(--tx-border);
                background: var(--tx-surface); color: var(--tx-fg-muted);
                cursor: pointer; box-shadow: 0 1px 2px rgba(15, 23, 42, .08);
            }
            .tx-sidebar-edge:hover { color: var(--tx-primary); background: var(--tx-hover); }
            .tx-sidebar-edge .material-symbols-outlined { font-size: 18px; }
            .tx-sidebar-close { display: none; }
            .tx-nav-caret { display: none; }
            .tx-nav-children { display: block; }
            .tx-main {
                margin-left: var(--tx-sidebar-w); width: auto;
                transition: margin-left .22s cubic-bezier(.16, 1, .3, 1);
            }
            .tx-header {
                left: var(--tx-sidebar-w); padding: 0 24px;
                transition: left .22s cubic-bezier(.16, 1, .3, 1);
            }
            .tx-page { padding: 16px 24px 40px; }

            html.tx-nav-collapsed .tx-sidebar { width: var(--tx-sidebar-rail); }
            html.tx-nav-collapsed .tx-main { margin-left: var(--tx-sidebar-rail); }
            html.tx-nav-collapsed .tx-header { left: var(--tx-sidebar-rail); }
            html.tx-nav-collapsed .tx-sidebar nav { overflow: visible; padding-left: 8px; padding-right: 8px; }
            html.tx-nav-collapsed .tx-brand-text { display: none; }
            html.tx-nav-collapsed .tx-sidebar-brand { justify-content: center; padding-left: 0; padding-right: 0; }
            html.tx-nav-collapsed .tx-sidebar .tx-nav-label {
                position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
                overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;
            }
            html.tx-nav-collapsed .tx-sidebar .tx-nav {
                justify-content: center; padding: 10px 0; gap: 0; border-left: 0;
            }
            html.tx-nav-collapsed .tx-nav-group { position: relative; }
            html.tx-nav-collapsed .tx-nav-children { display: none !important; }
            html.tx-nav-collapsed .tx-sidebar a.tx-nav[data-label]:hover::after,
            html.tx-nav-collapsed .tx-sidebar a.tx-nav[data-label]:focus-visible::after {
                content: attr(data-label);
                position: absolute; left: calc(100% + 10px); top: 50%;
                transform: translateY(-50%);
                background: var(--tx-fg); color: var(--tx-surface);
                font-size: 12px; font-weight: 600; line-height: 1;
                padding: 6px 8px; border-radius: 6px; white-space: nowrap;
                z-index: 80; pointer-events: none;
                box-shadow: 0 8px 24px rgba(15, 23, 42, .18);
            }
            html.tx-nav-collapsed .tx-nav-group .tx-nav-parent > a.tx-nav:hover::after,
            html.tx-nav-collapsed .tx-nav-group .tx-nav-parent > a.tx-nav:focus-visible::after {
                display: none;
            }
            .tx-assist-pop {
                position: fixed; z-index: 90;
                min-width: 196px; padding: 8px;
                border: 1px solid var(--tx-border); border-radius: 10px;
                background: var(--tx-surface);
                box-shadow: 0 12px 32px rgba(15, 23, 42, .16);
            }
            .tx-assist-pop .tx-nav-flyout-title {
                display: block; padding: 6px 10px 8px;
                font-size: 11px; font-weight: 600; letter-spacing: .04em;
                text-transform: uppercase; color: var(--tx-fg-subtle);
            }
            .tx-assist-pop .tx-nav {
                justify-content: flex-start; padding: 8px 10px; gap: 10px;
            }
            html.tx-nav-collapsed .tx-assist-pop .tx-nav-label {
                position: static; width: auto; height: auto; margin: 0;
                overflow: visible; clip: auto; clip-path: none; white-space: nowrap;
            }
        }

        /* page wrappers from old Jetstream pages fight the shell */
        .tx-page > .py-4,
        .tx-page > .py-2,
        .tx-page > .py-10 { padding-top: 0; padding-bottom: 0; }
        .tx-page .max-w-7xl {
            max-width: 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* page tabs (AI / Settings / Assistant / Report) */
        .tx-subnav {
            position: sticky;
            top: var(--tx-header-h);
            z-index: 26;
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 12px;
            margin: -12px -12px 16px;
            padding: 0 8px;
            border-bottom: 1px solid var(--tx-border);
            background: var(--tx-surface);
        }
        .tx-page > .tx-subnav ~ .tx-subnav {
            top: calc(var(--tx-header-h) + 44px);
            z-index: 25;
        }
        @media (min-width: 640px) { .tx-subnav { margin: -16px -16px 16px; padding: 0 12px; } }
        @media (min-width: 1024px) { .tx-subnav { margin: -16px -24px 20px; padding: 0 16px; } }
        .tx-subnav-track {
            display: flex; align-items: stretch; gap: 2px;
            flex: 1 1 auto; min-width: 0;
            overflow-x: auto; -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .tx-subnav-track::-webkit-scrollbar { display: none; }
        .tx-subnav-meta {
            flex: 0 1 46%;
            display: inline-flex; align-items: center; justify-content: flex-end;
            min-width: 0; max-width: 50%;
            padding: 0 4px 0 12px;
            font-size: 13px; font-weight: 600; line-height: 1.2;
            color: var(--tx-fg); text-align: right;
        }
        .tx-subnav-meta-name {
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .tx-tab {
            flex: 0 0 auto;
            display: inline-flex; align-items: center;
            height: 44px; padding: 0 12px;
            font-size: 13px; font-weight: 500; line-height: 1;
            color: var(--tx-fg-muted);
            border: 0; border-bottom: 2px solid transparent;
            background: transparent; font-family: inherit;
            white-space: nowrap; text-decoration: none; cursor: pointer;
        }
        .tx-tab:hover { color: var(--tx-fg); }
        .tx-tab-active {
            color: var(--tx-primary);
            border-bottom-color: var(--tx-primary);
            font-weight: 600;
        }

        .ai-docs-bar {
            display: flex; flex-wrap: wrap; align-items: center;
            justify-content: space-between; gap: 10px;
            margin: 0 0 16px; padding: 0 0 14px;
            border-bottom: 1px solid var(--tx-border);
        }
        .ai-docs-bar .ai-usage-seg {
            border-color: var(--tx-border);
            background: var(--tx-surface-1);
        }
        .ai-docs-bar .ai-usage-seg a {
            height: 28px; padding: 0 11px; border: 0; border-radius: 8px;
            display: inline-flex; align-items: center;
            background: transparent; color: var(--tx-fg-muted);
            font-size: 12px; font-weight: 600; text-decoration: none; font-family: inherit;
        }
        .ai-docs-bar .ai-usage-seg a.is-on { background: var(--tx-surface); color: var(--tx-fg); }
        .ai-docs-bar-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .ai-guide { display: flex; flex-direction: column; gap: 22px; }
        .ai-guide-intro h2 {
            margin: 0; font-size: 18px; font-weight: 700; letter-spacing: -.02em; color: var(--tx-fg);
        }
        .ai-guide-intro p {
            margin: 8px 0 0; font-size: 14px; line-height: 1.6; color: var(--tx-fg-muted); max-width: 72ch;
        }
        .ai-guide-flow {
            display: grid;
            grid-template-columns: minmax(0,1fr) 20px minmax(0,1fr) 20px minmax(0,1fr) 20px minmax(0,1fr);
            align-items: stretch;
        }
        .ai-guide-node {
            min-width: 0; padding: 12px;
            border: 1px solid var(--tx-border); border-radius: 10px;
            background: var(--tx-surface-1);
            display: flex; flex-direction: column; gap: 6px;
        }
        .ai-guide-node.is-core { background: var(--tx-active); border-color: var(--tx-primary); }
        .ai-guide-node > span {
            width: 20px; height: 20px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 6px; background: var(--tx-surface);
            font-size: 11px; font-weight: 700; color: var(--tx-primary);
        }
        .ai-guide-node strong { font-size: 13px; font-weight: 700; line-height: 1.3; color: var(--tx-fg); }
        .ai-guide-node small { font-size: 12px; line-height: 1.4; color: var(--tx-fg-muted); }
        .ai-guide-join {
            display: flex; align-items: center; justify-content: center; color: var(--tx-fg-subtle);
        }
        .ai-guide-join .material-symbols-outlined { font-size: 18px; }
        .ai-guide-cols { display: grid; grid-template-columns: 1fr; gap: 20px 28px; }
        @media (min-width: 860px) { .ai-guide-cols { grid-template-columns: 1fr 1fr; } }
        .ai-guide h3 {
            margin: 0 0 10px; font-size: 12px; font-weight: 700;
            letter-spacing: .04em; text-transform: uppercase; color: var(--tx-fg-subtle);
        }
        .ai-guide-list { margin: 0; padding: 0; list-style: none; counter-reset: step; }
        .ai-guide-list li {
            display: grid;
            grid-template-columns: 22px minmax(0, 1fr);
            column-gap: 10px;
            padding: 8px 0; border-top: 1px solid var(--tx-border);
            counter-increment: step;
        }
        .ai-guide-list li:first-child { border-top: 0; padding-top: 0; }
        .ai-guide-list li:last-child { padding-bottom: 0; }
        .ai-guide-list li::before {
            content: counter(step);
            width: 22px; height: 22px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 6px; background: var(--tx-surface-1);
            font-size: 11px; font-weight: 700; color: var(--tx-primary);
        }
        .ai-guide-list li > div { min-width: 0; }
        .ai-guide-list strong { display: block; font-size: 13px; color: var(--tx-fg); }
        .ai-guide-list span { display: block; margin-top: 2px; font-size: 13px; line-height: 1.5; color: var(--tx-fg-muted); }
        .ai-guide-rules { margin: 0; padding: 0; list-style: none; }
        .ai-guide-rules li {
            position: relative; margin: 0; padding: 8px 0 8px 16px;
            border-top: 1px solid var(--tx-border);
            font-size: 13px; line-height: 1.5; color: var(--tx-fg);
        }
        .ai-guide-rules li:first-child { border-top: 0; padding-top: 0; }
        .ai-guide-rules li:last-child { padding-bottom: 0; }
        .ai-guide-rules li::before {
            content: ""; position: absolute; left: 0; top: 15px;
            width: 7px; height: 7px; border-radius: 99px; background: var(--tx-border-strong);
        }
        .ai-guide-rules li:first-child::before { top: 7px; }
        .ai-guide-rules li.is-yes::before { background: #047857; }
        .ai-guide-rules li.is-no::before { background: #be123c; }
        .ai-guide-notes { padding-top: 4px; border-top: 1px solid var(--tx-border); }
        .ai-guide-notes dl {
            margin: 0; display: grid; grid-template-columns: 1fr; gap: 12px 24px;
        }
        @media (min-width: 720px) { .ai-guide-notes dl { grid-template-columns: 1fr 1fr; } }
        .ai-guide-notes dt { font-size: 11px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: var(--tx-fg-subtle); }
        .ai-guide-notes dd { margin: 4px 0 0; font-size: 13px; line-height: 1.45; color: var(--tx-fg); }
        @media (max-width: 760px) {
            .ai-guide-flow { display: flex; flex-direction: column; }
            .ai-guide-join { height: 18px; }
            .ai-guide-join .material-symbols-outlined { transform: rotate(90deg); }
        }

        .ai-swagger { min-height: 420px; }
        .ai-swagger .swagger-ui { font-family: inherit; }
        .ai-swagger .swagger-ui .topbar,
        .ai-swagger .swagger-ui .information-container .info .title small { display: none; }
        .ai-swagger .swagger-ui .wrapper { padding: 0; }
        .ai-swagger .swagger-ui .scheme-container {
            box-shadow: none; background: var(--tx-surface-1);
            padding: 12px 0; margin: 0 0 12px; border-radius: 8px;
        }
        .ai-swagger .swagger-ui .info { margin: 0 0 16px; }
        .ai-swagger .swagger-ui .info .title { color: var(--tx-fg); font-size: 20px; }
        .ai-swagger .swagger-ui .info p, .ai-swagger .swagger-ui .info li { color: var(--tx-fg-muted); }
        html body .swagger-ui { background: transparent; }

        .ai-usage {
            --ug-bg: var(--tx-surface-1);
            --ug-elev: var(--tx-surface);
            --ug-card: var(--tx-surface);
            --ug-line: var(--tx-border);
            --ug-fg: var(--tx-fg);
            --ug-muted: var(--tx-fg-muted);
            --ug-subtle: var(--tx-fg-subtle);
            --ug-accent: var(--tx-primary);
            --ug-accent-2: var(--tx-primary-hover);
            --ug-ok: #047857;
            --ug-bad: #be123c;
            --ug-grid: rgba(0, 90, 194, .08);
            --ug-lane-line: rgba(0, 90, 194, .08);
            --ug-mark: #e8f0fc;
            --ug-chip-bg: #ffffff;
            --ug-shadow: 0 8px 20px rgba(15, 23, 42, .08);
            display: flex; flex-direction: column; gap: 0;
        }
        .dark .ai-usage {
            --ug-bg: #0b1624;
            --ug-elev: #102033;
            --ug-card: #15263b;
            --ug-line: #24364d;
            --ug-fg: #e4eef9;
            --ug-muted: #8ea0b5;
            --ug-subtle: #6d7f96;
            --ug-accent: #4d8eff;
            --ug-accent-2: #93c5fd;
            --ug-ok: #34d399;
            --ug-bad: #fb7185;
            --ug-grid: rgba(147, 197, 253, .06);
            --ug-lane-line: rgba(147, 197, 253, .08);
            --ug-mark: #1b3350;
            --ug-chip-bg: rgba(11, 22, 36, .94);
            --ug-shadow: 0 10px 24px rgba(8, 18, 32, .32);
        }
        .ai-usage.is-details { gap: 12px; }
        .ai-ug-head {
            display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
            gap: 14px 18px; padding: 16px 16px 14px;
            background: var(--ug-elev); color: var(--ug-fg);
            border: 1px solid var(--ug-line); border-bottom: 0;
            border-radius: 16px 16px 0 0;
        }
        .ai-usage.is-details .ai-ug-head {
            border-bottom: 1px solid var(--ug-line); border-radius: 16px;
        }
        .ai-ug-title h2 {
            margin: 0; font-size: 18px; font-weight: 700; letter-spacing: -.02em;
            color: var(--ug-fg); line-height: 1.2;
        }
        .ai-ug-title p { margin: 4px 0 0; font-size: 12px; color: var(--ug-muted); }
        .ai-ug-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .ai-usage-seg {
            display: inline-flex; align-items: center; padding: 3px;
            border: 1px solid var(--ug-line); border-radius: 10px; background: var(--ug-bg);
        }
        .ai-usage-seg button {
            height: 28px; padding: 0 11px; border: 0; border-radius: 8px;
            background: transparent; color: var(--ug-muted);
            font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit;
        }
        .ai-usage-seg button.is-on { background: var(--ug-card); color: var(--ug-fg); }
        .ai-usage-seg button.is-live.is-on { color: var(--ug-accent-2); }
        .ai-ug-pulse {
            display: inline-block; width: 6px; height: 6px; margin-right: 5px;
            border-radius: 1px; background: var(--ug-accent); vertical-align: 1px;
            box-shadow: 0 0 0 0 rgba(77,142,255,.55);
            animation: ai-ug-pulse 1.8s ease-out infinite;
        }
        @keyframes ai-ug-pulse {
            0% { box-shadow: 0 0 0 0 rgba(77,142,255,.45); }
            70% { box-shadow: 0 0 0 6px rgba(77,142,255,0); }
            100% { box-shadow: 0 0 0 0 rgba(77,142,255,0); }
        }
        .ai-ug-actions { display: inline-flex; align-items: center; gap: 4px; }
        .ai-ug-iconbtn {
            width: 30px; height: 30px; padding: 0; border-radius: 8px;
            border: 1px solid var(--ug-line); background: var(--ug-bg); color: var(--ug-muted);
            display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer; font-family: inherit;
        }
        .ai-ug-iconbtn .material-symbols-outlined { font-size: 16px; line-height: 1; }
        .ai-ug-iconbtn:hover, .ai-ug-iconbtn.is-on { background: var(--ug-card); color: var(--ug-fg); }
        .ai-ug-popwrap { position: relative; }
        .ai-ug-pop {
            position: absolute; top: calc(100% + 8px); right: 0; z-index: 8;
            width: 220px; padding: 10px;
            background: var(--ug-card); border: 1px solid var(--ug-line); border-radius: 10px;
            box-shadow: 0 16px 40px rgba(0,0,0,.35);
        }
        .ai-ug-pop label {
            display: block; margin-bottom: 6px;
            font-size: 10px; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: var(--ug-subtle);
        }
        .ai-ug-pop input {
            width: 100%; height: 32px; padding: 0 8px;
            border: 1px solid var(--ug-line); border-radius: 7px;
            background: var(--ug-bg); color: var(--ug-fg); font-size: 12px;
        }
        .ai-ug-kpis {
            display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1px;
            background: var(--ug-line); border-left: 1px solid var(--ug-line); border-right: 1px solid var(--ug-line);
        }
        @media (min-width: 760px) { .ai-ug-kpis { grid-template-columns: repeat(5, minmax(0, 1fr)); } }
        .ai-ug-kpi {
            min-height: 78px; padding: 12px 14px;
            background: var(--ug-elev); display: flex; flex-direction: column; justify-content: flex-end;
        }
        .ai-ug-kpi-label {
            font-size: 10px; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: var(--ug-subtle);
        }
        .ai-ug-kpi-value {
            margin-top: 6px; font-size: 22px; font-weight: 650; letter-spacing: -.03em;
            color: var(--ug-fg); line-height: 1.05; font-variant-numeric: tabular-nums;
        }
        .ai-ug-trend {
            margin-top: 5px; font-size: 11px; font-weight: 600; color: var(--ug-subtle);
        }
        .ai-ug-trend.is-up { color: var(--ug-ok); }
        .ai-ug-trend.is-down { color: var(--ug-bad); }
        .ai-ug-stage {
            background: var(--ug-bg);
            border: 1px solid var(--ug-line); border-radius: 0 0 16px 16px;
        }
        .ai-ug-board {
            position: relative; height: clamp(200px, calc(100dvh - 350px), 460px);
            overflow: hidden; touch-action: none; cursor: grab;
            -webkit-user-select: none; user-select: none;
            -webkit-touch-callout: none;
            background-image:
                linear-gradient(var(--ug-lane-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--ug-lane-line) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .ai-ug-board.is-drag { cursor: grabbing; }
        .ai-ug-board.is-resize { cursor: ns-resize; }
        .ai-ug-resize {
            position: absolute; left: 0; right: 0; bottom: 0; z-index: 5;
            height: 12px; cursor: ns-resize;
        }
        .ai-ug-resize::before {
            content: ""; position: absolute; left: 50%; top: 4px;
            width: 36px; height: 3px; margin-left: -18px;
            border-radius: 99px; background: var(--ug-line);
        }
        .ai-ug-resize:hover::before, .ai-ug-board.is-resize .ai-ug-resize::before {
            background: var(--ug-muted);
        }
        .ai-ug-board-tools {
            position: absolute; top: 10px; right: 10px; z-index: 4;
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px; border: 1px solid var(--ug-line); border-radius: 10px;
            background: var(--ug-card);
        }
        .ai-ug-zoom {
            min-width: 42px; padding: 0 6px;
            font-size: 11px; font-weight: 700; color: var(--ug-muted);
            font-variant-numeric: tabular-nums;
        }
        .ai-ug-board-hint {
            position: absolute; left: 12px; bottom: 16px; z-index: 3;
            margin: 0; font-size: 11px; color: var(--ug-subtle);
            pointer-events: none;
        }
        .ai-ug-world {
            position: absolute; top: 0; left: 0;
            transform-origin: 0 0; will-change: transform;
        }
        .ai-ug-edges { position: absolute; top: 0; left: 0; overflow: visible; pointer-events: none; }
        .ai-ug-edge { fill: none; stroke: var(--ug-accent); stroke-width: 1.6; stroke-opacity: .28; }
        .ai-ug-edge.is-on { stroke-opacity: .7; }
        .ai-ug-edge.is-dim { stroke-opacity: .12; }
        .ai-ug-lane {
            position: absolute; top: 10px;
            font-size: 11px; font-weight: 700; letter-spacing: .04em;
            text-transform: uppercase; color: var(--ug-muted);
            pointer-events: none; white-space: nowrap;
        }
        .ai-ug-node {
            position: absolute; width: 184px; padding: 10px 12px;
            display: flex; flex-direction: column; gap: 3px;
            text-align: left; font-family: inherit; cursor: pointer;
            color: var(--ug-fg); background: var(--ug-card);
            border: 1px solid var(--ug-line); border-radius: 10px;
        }
        .ai-ug-node.is-dim { opacity: .4; }
        .ai-ug-node.is-on { opacity: 1; }
        .ai-ug-node.is-open, .ai-ug-node:hover { border-color: var(--ug-accent); }
        .ai-ug-node.is-ghost {
            cursor: default; opacity: .5; background: transparent;
            border-style: dashed;
        }
        .ai-ug-node-name {
            font-size: 13px; font-weight: 650; color: var(--ug-fg); line-height: 1.3;
            word-break: break-word;
        }
        .ai-ug-node-metrics {
            font-size: 11px; color: var(--ug-muted); font-variant-numeric: tabular-nums;
        }
        .ai-ug-empty { margin: 0; color: var(--ug-subtle); font-size: 12px; }
        .ai-ug-modal {
            position: fixed; inset: 0; z-index: 80;
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
        }
        .ai-ug-modal-scrim {
            position: absolute; inset: 0; border: 0; padding: 0;
            background: rgba(15, 23, 42, .34); cursor: pointer;
        }
        .ai-ug-modal-panel {
            position: relative; z-index: 1;
            width: min(440px, 100%); max-height: min(80vh, 640px);
            overflow: auto; padding: 16px;
            background: var(--ug-card); color: var(--ug-fg);
            border: 1px solid var(--ug-line); border-radius: 14px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, .18);
        }
        .ai-ug-inspect-top { display: flex; justify-content: space-between; gap: 10px; }
        .ai-ug-inspect-kicker { margin: 0; font-size: 10px; letter-spacing: .08em; text-transform: uppercase; color: var(--ug-subtle); }
        .ai-ug-modal-panel h3 { margin: 4px 0 0; font-size: 18px; color: var(--ug-fg); }
        .ai-ug-inspect-full { margin: 4px 0 0; font-size: 11px; color: var(--ug-muted); word-break: break-all; }
        .ai-ug-modal-panel h4 {
            margin: 16px 0 8px; font-size: 10px; letter-spacing: .08em;
            text-transform: uppercase; color: var(--ug-subtle);
        }
        .ai-ug-inspect-cap {
            margin-left: 6px; letter-spacing: 0; text-transform: none;
            font-weight: 600; color: var(--ug-muted);
        }
        .ai-ug-inspect-grid { margin: 14px 0 0; display: grid; grid-template-columns: 1fr 1fr; gap: 10px 12px; }
        .ai-ug-inspect-grid dt { font-size: 10px; letter-spacing: .06em; text-transform: uppercase; color: var(--ug-subtle); }
        .ai-ug-inspect-grid dd { margin: 3px 0 0; font-size: 13px; font-weight: 650; color: var(--ug-fg); }
        .ai-ug-inspect-list { display: flex; flex-direction: column; gap: 6px; }
        .ai-ug-inspect-row {
            display: grid; grid-template-columns: 1fr auto auto; gap: 8px; align-items: center;
            font-size: 11px; color: var(--ug-muted);
        }
        .ai-ug-inspect-id { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; color: var(--ug-fg); }
        .ai-ug-state { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .ai-ug-state.is-success { color: var(--ug-ok); }
        .ai-ug-state.is-error { color: var(--ug-bad); }
        .ai-series, .ai-break {
            margin-top: 12px; padding: 12px 14px 10px;
            background: var(--ug-elev); border: 1px solid var(--ug-line); border-radius: 12px;
        }
        .ai-series-bar, .ai-break-bar {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            margin-bottom: 8px;
        }
        .ai-break-title { font-size: 13px; font-weight: 700; color: var(--ug-fg); }
        .ai-break-toggle {
            display: inline-flex; align-items: center; gap: 4px;
            border: 0; padding: 0; background: transparent; cursor: pointer;
            font: inherit; color: inherit;
        }
        .ai-break-caret {
            font-size: 18px; color: var(--ug-muted); line-height: 1;
            transition: transform .14s ease;
        }
        .ai-break-caret.is-open { transform: rotate(90deg); }
        .ai-break-row { cursor: pointer; }
        .ai-break-row:hover { background: var(--tx-hover); }
        .ai-break-detail td { background: var(--ug-bg); }
        .ai-break-meta {
            margin: 0; display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 16px;
        }
        @media (min-width: 720px) { .ai-break-meta { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        .ai-break-meta dt { font-size: 10px; letter-spacing: .04em; text-transform: uppercase; color: var(--ug-subtle); }
        .ai-break-meta dd { margin: 3px 0 0; font-size: 13px; font-weight: 650; color: var(--ug-fg); }
        .ai-series-plot { position: relative; cursor: crosshair; }
        .ai-series-svg { width: 100%; height: 220px; display: block; }
        .ai-series-grid { stroke: var(--ug-line); stroke-width: 1; }
        .ai-series-ylab { fill: var(--ug-subtle); font-size: 11px; text-anchor: end; }
        .ai-series-xlab { fill: var(--ug-subtle); font-size: 11px; text-anchor: middle; }
        .ai-series-line { fill: none; stroke: var(--ug-accent); stroke-width: 2.2; stroke-linejoin: round; stroke-linecap: round; }
        .ai-series-fill { fill: var(--ug-accent); fill-opacity: .14; }
        .ai-series-guide {
            position: absolute; top: 18px; bottom: 30px; width: 1px;
            background: var(--ug-muted); opacity: .45; pointer-events: none;
            transform: translateX(-50%);
        }
        .ai-series-dot {
            position: absolute; width: 9px; height: 9px; margin: -4px 0 0 -4px;
            border-radius: 99px; pointer-events: none;
            background: var(--ug-card); border: 2px solid var(--ug-accent);
        }
        .ai-series-tip {
            position: absolute; top: 10px; z-index: 3; min-width: 128px;
            padding: 8px 10px; pointer-events: none;
            background: var(--ug-card); color: var(--ug-fg);
            border: 1px solid var(--ug-line); border-radius: 8px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .12);
            transform: translate(-50%, 0);
        }
        .ai-series-tip strong { display: block; margin-bottom: 6px; font-size: 12px; }
        .ai-series-tip div { display: flex; justify-content: space-between; gap: 16px; font-size: 12px; line-height: 1.5; }
        .ai-series-tip span { color: var(--ug-muted); }
        .ai-series-tip b { font-weight: 650; color: var(--ug-fg); }
        .ai-break-table th { font-size: 11px; letter-spacing: .04em; text-transform: uppercase; }

        .ai-ug-pager {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
            gap: 8px; margin-top: 10px; font-size: 12px; color: var(--ug-muted);
        }
        .ai-ug-pager-nav { display: inline-flex; align-items: center; gap: 8px; }
        .ai-ug-pager-nav .tx-btn[disabled] { opacity: .4; cursor: default; }
        .ai-usage-filters { display: grid; grid-template-columns: 1fr; gap: 10px; }
        @media (min-width: 640px) { .ai-usage-filters { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 760px) {
            .ai-ug-board { height: clamp(180px, calc(100dvh - 340px), 260px); }
            .ai-ug-board-hint { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            .ai-ug-pulse { animation: none !important; }
        }

        /* leftover Jetstream nav-links used as tabs */
        .tx-page nav .h-6 { height: auto !important; min-height: 44px; }
        .tx-page a.border-b-2 {
            height: 44px !important; padding: 0 12px !important;
            display: inline-flex !important; align-items: center !important;
            white-space: nowrap; flex-shrink: 0;
        }
        .tx-page a.border-b-2.border-indigo-400,
        .tx-page a.border-b-2.border-indigo-700 {
            color: var(--tx-primary) !important;
            border-bottom-color: var(--tx-primary) !important;
            font-weight: 600;
        }

        /* compact buttons — one size, one palette, always left */
        .tx-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 4px;
            height: 30px; padding: 0 10px; border-radius: 6px;
            font-size: 12px; font-weight: 600; line-height: 1; letter-spacing: 0;
            color: var(--tx-on-primary); background: var(--tx-primary); border: 1px solid transparent;
            cursor: pointer; text-decoration: none; white-space: nowrap;
            transition: background-color .14s, opacity .14s;
        }
        .tx-btn:hover { background: var(--tx-primary-hover); }
        .tx-btn-ghost {
            color: var(--tx-fg-muted); background: var(--tx-surface); border-color: var(--tx-border);
        }
        .tx-btn-ghost:hover { background: var(--tx-hover); color: var(--tx-primary); border-color: var(--tx-border-strong); }
        .tx-btn-danger {
            color: #fff; background: #e5484d; border-color: transparent;
        }
        .tx-btn-danger:hover { background: #ce3d42; }
        .tx-btn:disabled,
        .tx-btn[disabled] {
            opacity: .5;
            cursor: not-allowed;
        }

        .tx-page input[type="text"],
        .tx-page input[type="email"],
        .tx-page input[type="password"],
        .tx-page input[type="number"],
        .tx-page input[type="url"],
        .tx-page input[type="search"],
        .tx-page input[type="tel"],
        .tx-page input[type="date"],
        .tx-page input[type="datetime-local"],
        .tx-page input[type="time"],
        .tx-page input[type="month"],
        .tx-page input:not([type]),
        .tx-page select:not(.form-select):not(#datatables_mass_actions),
        .tx-page textarea {
            width: 100%;
            min-height: 38px;
            padding: 8px 10px;
            border: 1px solid var(--tx-border);
            border-radius: 6px;
            background: var(--tx-surface);
            color: var(--tx-fg);
            font-size: 13px;
            line-height: 1.35;
            box-shadow: none;
        }
        .tx-page textarea {
            min-height: 88px;
            resize: vertical;
        }
        .tx-page select:not(.form-select):not(#datatables_mass_actions) {
            height: 38px;
            padding-right: 28px;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 20 20'%3E%3Cpath fill='%236b7688' fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
        }
        .tx-page input[type="text"]:focus,
        .tx-page input[type="email"]:focus,
        .tx-page input[type="password"]:focus,
        .tx-page input[type="number"]:focus,
        .tx-page input[type="url"]:focus,
        .tx-page input[type="search"]:focus,
        .tx-page input[type="tel"]:focus,
        .tx-page input[type="date"]:focus,
        .tx-page input[type="datetime-local"]:focus,
        .tx-page input[type="time"]:focus,
        .tx-page input[type="month"]:focus,
        .tx-page input:not([type]):focus,
        .tx-page select:not(.form-select):not(#datatables_mass_actions):focus,
        .tx-page textarea:focus {
            outline: none;
            border-color: var(--tx-ring);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--tx-ring) 22%, transparent);
        }
        .tx-page input:disabled,
        .tx-page input[disabled],
        .tx-page select:disabled,
        .tx-page select[disabled],
        .tx-page textarea:disabled,
        .tx-page textarea[disabled] {
            opacity: 1 !important;
            background: var(--tx-surface-1) !important;
            color: var(--tx-fg-muted) !important;
            border-color: var(--tx-border) !important;
            cursor: not-allowed;
            box-shadow: none !important;
        }
        .tx-page select:disabled,
        .tx-page select[disabled] {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 20 20'%3E%3Cpath fill='%236b7688' fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 8px center !important;
            background-size: 16px !important;
        }
        .tx-row-link {
            display: inline; margin: 0; padding: 0; border: 0; background: none;
            font: inherit; font-weight: 500; color: var(--tx-primary);
            cursor: pointer; text-decoration: none;
        }
        .tx-row-link:hover { text-decoration: underline; }
        .tx-row-link-danger { color: #e5484d; }

        .tx-toolbar > .tx-btn,
        .tx-toolbar > a.tx-btn,
        .tx-actions > .tx-btn,
        .tx-actions > a.tx-btn {
            height: 30px;
            padding: 0 10px;
            font-size: 12px;
        }

        /* page content card (wraps list/table content) */
        .tx-stack { display: flex; flex-direction: column; gap: 20px; }
        .tx-section { margin: 0; }
        .tx-section-title {
            margin: 0 0 8px; font-size: 13px; font-weight: 600;
            letter-spacing: .02em; color: var(--tx-fg-muted);
        }
        .tx-toolbar,
        .tx-actions {
            display: flex; flex-wrap: wrap; align-items: center;
            justify-content: flex-start; gap: 8px; text-align: left;
        }
        .tx-toolbar { margin-bottom: 12px; }
        .tx-card {
            background: var(--tx-surface); border: 1px solid var(--tx-border);
            border-radius: 12px; padding: 12px; box-shadow: 0 1px 2px rgba(24,24,27,.04);
        }
        .tx-kv { margin: 0; }
        .tx-kv-row {
            display: flex; align-items: center; gap: 12px;
            padding: 8px 0; border-top: 1px solid var(--tx-border);
        }
        .tx-kv-row:first-child { border-top: 0; padding-top: 0; }
        .tx-kv-label {
            flex: 0 0 7.25rem; font-size: 12px; color: var(--tx-fg-muted);
        }
        .tx-kv-value {
            flex: 1 1 auto; min-width: 0;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12px; line-height: 1.45; word-break: break-all;
            color: var(--tx-fg);
        }
        .tx-kv-actions { flex: 0 0 auto; display: flex; align-items: center; gap: 6px; }
        @media (min-width: 1024px) { .tx-card { padding: 16px; } }
        .dark .tx-card { box-shadow: 0 1px 0 rgba(255,255,255,.02); }
        .tx-stat { border-left: 3px solid var(--tx-stat, var(--tx-primary)); }
        .tx-stat-value { color: var(--tx-stat, var(--tx-primary)); }
        .tx-stat-blue { --tx-stat: #005ac2; background: #f3f7ff; }
        .tx-stat-slate { --tx-stat: #475569; background: #f4f6f8; }
        .tx-stat-amber { --tx-stat: #b45309; background: #fff8ed; }
        .tx-stat-green { --tx-stat: #047857; background: #f0fdf6; }
        .tx-stat-rose { --tx-stat: #be123c; background: #fff1f3; }
        .dark .tx-stat-blue { --tx-stat: #8bb4ff; background: #0b1a33; }
        .dark .tx-stat-slate { --tx-stat: #cbd5e1; background: #121a26; }
        .dark .tx-stat-amber { --tx-stat: #fbbf24; background: #2a1c08; }
        .dark .tx-stat-green { --tx-stat: #34d399; background: #0b2418; }
        .dark .tx-stat-rose { --tx-stat: #fb7185; background: #2a1016; }
        .tx-pill {
            display: inline-flex; align-items: center;
            padding: 2px 7px; border-radius: 999px;
            font-size: 11px; font-weight: 600; line-height: 1.4;
        }
        .tx-pill-amber { color: #b45309; background: #fef3c7; }
        .tx-pill-green { color: #047857; background: #d1fae5; }
        .tx-pill-slate { color: #475569; background: #e2e8f0; }
        .dark .tx-pill-amber { color: #fde68a; background: #422006; }
        .dark .tx-pill-green { color: #6ee7b7; background: #064e3b; }
        .dark .tx-pill-slate { color: #cbd5e1; background: #1e293b; }

        /* ---- brand ---- */
        .tx-brand-mark {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 8px; flex: none; overflow: hidden;
            background: var(--tx-primary); color: var(--tx-on-primary);
            font-weight: 800; font-size: 16px;
        }
        .tx-brand-mark img { width: 100%; height: 100%; object-fit: cover; }
        .tx-brand-name { font-weight: 700; font-size: 17px; letter-spacing: -.01em; color: var(--tx-primary); line-height: 1.15; }
        .tx-brand-sub  { font-size: 11px; font-weight: 500; letter-spacing: .03em; color: var(--tx-fg-subtle); line-height: 1.2; }

        /* ---- material symbols ---- */
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined'; font-weight: 400; font-style: normal;
            font-size: 20px; line-height: 1; letter-spacing: normal; text-transform: none;
            display: inline-block; white-space: nowrap; word-wrap: normal; direction: ltr;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* ---- nav rows ---- */
        .tx-nav {
            position: relative; display: flex; align-items: center; gap: 12px;
            padding: 9px 14px; border-radius: 8px;
            font-size: 13px; font-weight: 500; letter-spacing: .01em;
            color: var(--tx-fg-muted); border-left: 2px solid transparent;
            transition: background-color .14s ease, color .14s ease;
        }
        .tx-nav:hover { background: var(--tx-hover); color: var(--tx-fg); }
        .tx-nav .material-symbols-outlined { font-size: 20px; color: inherit; flex: none; }
        .tx-nav-active { color: var(--tx-primary); border-left-color: var(--tx-primary); background: var(--tx-active); font-weight: 600; }
        .tx-nav-active .material-symbols-outlined { font-variation-settings: 'FILL' 1, 'wght' 500; }

        /* ---- header controls ---- */
        .tx-iconbtn {
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--tx-fg-muted); border-radius: 8px;
            transition: background-color .14s ease, color .14s ease;
        }
        @media (min-width: 1024px) {
            button.tx-iconbtn.tx-nav-toggle,
            button.tx-iconbtn.tx-sidebar-close { display: none; }
        }
        .tx-iconbtn:hover { background: var(--tx-hover); color: var(--tx-primary); }
        .tx-iconbtn .material-symbols-outlined { font-size: 20px; }
        .tx-link { color: var(--tx-fg-muted); transition: color .14s ease; }
        .tx-link:hover { color: var(--tx-fg); }
        .tx-project-link {
            color: var(--tx-primary); font-weight: 600;
            text-decoration: underline; text-underline-offset: 2px;
        }
        tr:hover .tx-project-link { color: var(--tx-primary-hover); }
        .tx-divider { width: 1px; height: 24px; background: var(--tx-border); }

        .tx-nav:focus-visible,
        .tx-iconbtn:focus-visible,
        .tx-focus:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px var(--tx-surface), 0 0 0 4px var(--tx-ring);
        }

        .tx-13 { font-size: 13px; line-height: 1rem; }
        .tx-11 { font-size: 11px; line-height: 1rem; }
        .tx-team-name { max-width: 8rem; }
        @media (min-width: 640px) { .tx-team-name { max-width: 12rem; } }
        .tx-avatar { border-radius: 9999px; box-shadow: 0 0 0 1px var(--tx-border); overflow: hidden; }

        .tx-crumb-slot {
            min-width: 0; flex: 1 1 auto;
            display: flex; align-items: center;
            padding: 0 6px;
        }
        .tx-crumb { min-width: 0; max-width: 100%; }
        .tx-crumb-list {
            display: flex; align-items: center;
            margin: 0; padding: 0; list-style: none;
            min-width: 0; max-width: 100%;
        }
        .tx-crumb-home {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; flex: none;
            border-radius: 8px; color: var(--tx-fg-muted);
        }
        .tx-crumb-home:hover { background: var(--tx-hover); color: var(--tx-primary); }
        .tx-crumb-home .material-symbols-outlined { font-size: 18px; line-height: 1; }
        .tx-crumb-list > li:first-child + .tx-crumb-step { margin-left: 4px; }
        .tx-crumb-sr {
            position: absolute; width: 1px; height: 1px;
            padding: 0; margin: -1px; overflow: hidden;
            clip: rect(0,0,0,0); white-space: nowrap; border: 0;
        }
        .tx-crumb-step, .tx-crumb-ellipsis {
            display: flex; align-items: center; min-width: 0;
        }
        .tx-crumb-sep {
            display: inline-flex; align-items: center; justify-content: center;
            width: 18px; flex: none; color: var(--tx-fg-subtle);
        }
        .tx-crumb-sep .material-symbols-outlined { font-size: 16px; line-height: 1; }
        .tx-crumb-link, .tx-crumb-text, .tx-crumb-dots {
            max-width: 9.5rem;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            font-size: 13px; line-height: 1.2;
        }
        .tx-crumb-link {
            color: var(--tx-fg-muted); text-decoration: none;
            border-radius: 4px;
        }
        .tx-crumb-link:hover { color: var(--tx-primary); }
        .tx-crumb-text { color: var(--tx-fg-muted); }
        .tx-crumb-step.is-current .tx-crumb-text {
            color: var(--tx-fg); font-weight: 650;
            max-width: 14rem;
        }
        .tx-crumb-dots { color: var(--tx-fg-subtle); letter-spacing: .08em; }
        .tx-crumb-ellipsis { display: none; }
        @media (max-width: 720px) {
            .tx-crumb-step.is-mid { display: none; }
            .tx-crumb-ellipsis { display: flex; }
            .tx-crumb-link, .tx-crumb-text { max-width: 7.5rem; }
            .tx-crumb-step.is-current .tx-crumb-text { max-width: 10rem; }
        }
        @media (min-width: 1100px) {
            .tx-crumb-link, .tx-crumb-text { max-width: 12rem; }
            .tx-crumb-step.is-current .tx-crumb-text { max-width: 18rem; }
        }

        /* wordmark fallback if the remote logo image fails */
        .tx-wordmark { align-items: center; gap: 10px; line-height: 1; }

        /* quiet scrollbars */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--tx-border-strong); border-radius: 4px; }

        /* Datatable stays Medicone-default in light. Dark mode remaps only. */
        .dark .dt .bg-white { background-color: var(--tx-surface) !important; }
        .dark .dt .bg-gray-50 { background-color: var(--tx-surface-1) !important; }
        .dark .dt .bg-gray-100 { background-color: var(--tx-surface-1) !important; }
        .dark .dt .text-gray-400,
        .dark .dt .text-gray-500,
        .dark .dt .text-gray-600,
        .dark .dt .text-gray-700 { color: var(--tx-fg-muted) !important; }
        .dark .dt .text-gray-800,
        .dark .dt .text-gray-900 { color: var(--tx-fg) !important; }
        .dark .dt .border-gray-200,
        .dark .dt .border-gray-300 { border-color: var(--tx-border) !important; }
        .dark .dt input,
        .dark .dt select {
            background-color: var(--tx-surface-1) !important;
            color: var(--tx-fg) !important;
            border-color: var(--tx-border) !important;
        }
        .dark .dt option { background-color: var(--tx-surface); color: var(--tx-fg); }

        @media (max-width: 639px) {
            .tx-team-name span.truncate { max-width: 4.5rem; }
            .tx-page { padding: 10px 10px 28px; }
            .tx-subnav { margin: -10px -10px 12px; padding: 0 6px; }
            .tx-card { padding: 10px; }
            .tx-kv-row { flex-wrap: wrap; }
            .tx-kv-label { flex-basis: 100%; }
            .tx-kv-actions { margin-left: auto; }
        }
    </style>
    <link rel="stylesheet" media="print" href="{{ url('backend/css/print.css') }}">
    @trixassets
    @livewireStyles
    <script src="{{ url('js/app.js') }}" defer></script>
</head>

<body class="font-sans antialiased tx-root">
    <x-jet-banner />

    @php($embed = request()->boolean('embed'))

    @if ($embed)
        <main>
            <div id="chat-event" class="hidden"></div>
            {{ $slot }}
        </main>
    @else
        <div class="tx-canvas">
            <div
                x-show="sidebarOpen"
                x-cloak
                class="tx-scrim fixed inset-0 z-40 lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            @include('layouts.partials.sidebar')

            <div class="tx-main">
                @include('layouts.partials.topbar')

                @if (auth()->user()->currentTeam && auth()->user()->currentTeam->id == env('IN_HOUSE_TEAM_ID'))
                    @livewire('search.all')
                @endif

                <main class="tx-page">
                    <div id="chat-event" class="hidden"></div>
                    {{ $slot }}
                </main>
            </div>
        </div>
    @endif

    @stack('modals')

    @livewireScripts
    @livewireChartsScripts
    @stack('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        document.addEventListener('livewire:init', () => {
            let mode = localStorage.getItem('dark') || 'false';

            if (!('dark' in localStorage)) {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    mode = 'true';
                } else {
                    mode = 'false';
                }
                localStorage.setItem('dark', mode);
                switchMode(mode === 'true' || mode === true);
            } else {
                switchMode(localStorage.getItem('dark') === 'true');
            }

            Livewire.on('view-mode', event => {
                let newMode = event.newMode === 'true' || event.newMode === true;
                localStorage.setItem('dark', newMode);
                switchMode(newMode);
            });
        });

        function switchMode(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            const rootEl = document.querySelector('[x-data]');
            if (rootEl && rootEl._x_dataStack) {
                rootEl._x_dataStack[0].darkMode = isDark;
            }

            Livewire.dispatchTo('dark', 'ModeView', { mode: isDark });
        }
    </script>

    <audio id="sound" class="hidden" controls>
        <source src="{{url('/assets/sound/notif.wav')}}" type="audio/wav">
        Your browser does not support the audio element.
    </audio>
</body>

</html>
