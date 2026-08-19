@php
    $fmt = static function ($n) {
        $n = (float) $n;
        if ($n >= 1000000) {
            return rtrim(rtrim(number_format($n / 1000000, 2, '.', ''), '0'), '.').'M';
        }
        if ($n >= 1000) {
            return rtrim(rtrim(number_format($n / 1000, 1, '.', ''), '0'), '.').'K';
        }

        return number_format($n);
    };
    $treeNodes = [];
    foreach ($graph['apps'] as $node) {
        $treeNodes[] = ['layer' => 'application', 'node' => $node];
    }
    $treeNodes[] = ['layer' => 'routing', 'node' => $graph['gateway']];
    foreach ($graph['providers'] as $node) {
        $treeNodes[] = ['layer' => 'provider', 'node' => $node];
    }
    foreach ($graph['models'] as $node) {
        $treeNodes[] = ['layer' => 'model', 'node' => $node];
    }
@endphp

<div class="ai-usage {{ $tab === 'details' ? 'is-details' : '' }}" @if($tab === 'overview' && $range === 'live') wire:poll.8s @endif>
    <header class="ai-ug-head">
        <div class="ai-ug-title">
            <h2>{{ __('AI Usage Graph') }}</h2>
            <p>{{ $isGlobal ? __('Trace all applications across your AI infrastructure.') : __('Trace this application across your AI infrastructure.') }}</p>
        </div>
        <div class="ai-ug-toolbar">
            <div class="ai-usage-seg" role="tablist">
                <button type="button" role="tab" class="{{ $tab === 'overview' ? 'is-on' : '' }}" wire:click="setTab('overview')">{{ __('Overview') }}</button>
                <button type="button" role="tab" class="{{ $tab === 'details' ? 'is-on' : '' }}" wire:click="setTab('details')">{{ __('Details') }}</button>
            </div>
            <div class="ai-usage-seg ai-ug-ranges" role="group" aria-label="{{ __('Range') }}">
                @foreach($ranges as $key => $label)
                    <button type="button" class="{{ $range === $key ? 'is-on' : '' }}{{ $key === 'live' ? ' is-live' : '' }}" wire:click="setRange('{{ $key }}')">
                        @if($key === 'live')<span class="ai-ug-pulse" aria-hidden="true"></span>@endif
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="ai-ug-actions" x-data="{ q: false, f: false }">
                <div class="ai-ug-popwrap" @click.away="q = false">
                    <button type="button" class="ai-ug-iconbtn {{ $graphQuery !== '' ? 'is-on' : '' }}" @click="q = !q; f = false" title="{{ __('Search') }}">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                    <div class="ai-ug-pop" x-show="q" x-cloak>
                        <label>{{ __('Search') }}</label>
                        <input type="search" wire:model.debounce.300ms="graphQuery" placeholder="{{ __('Model, provider, app') }}">
                    </div>
                </div>
                <div class="ai-ug-popwrap" @click.away="f = false">
                    <button type="button" class="ai-ug-iconbtn {{ $endUser !== '' ? 'is-on' : '' }}" @click="f = !f; q = false" title="{{ __('Filter') }}">
                        <span class="material-symbols-outlined">tune</span>
                    </button>
                    <div class="ai-ug-pop" x-show="f" x-cloak>
                        <label>{{ __('Customer') }}</label>
                        <input type="text" wire:model.debounce.400ms="endUser" placeholder="Budi">
                    </div>
                </div>
                <button type="button" class="ai-ug-iconbtn" wire:click="$refresh" title="{{ __('Refresh') }}">
                    <span class="material-symbols-outlined">refresh</span>
                </button>
            </div>
        </div>
    </header>

    @if($tab === 'overview')
        @include('livewire.ai.usage-kpis')

        <div class="ai-ug-stage" wire:key="usage-overview">
            <div class="ai-ug-board"
                 x-data="{
                    x: 24, y: 28, s: 1, h: 280,
                    drag: false, resize: false, sx: 0, sy: 0, ox: 0, oy: 0, oh: 280,
                    pin: 0, px: 0, py: 0, ps: 1,
                    boot() {
                        var st = window.__ugPan;
                        try { st = JSON.parse(localStorage.getItem('tx-ug-board') || 'null') || st; } catch (e) {}
                        var custom = st && st.h != null && st.h !== 260 && st.h !== 280 && Math.abs(st.h - (window.innerHeight - 390)) > 24;
                        if (st) {
                            if (st.x != null) this.x = st.x;
                            if (st.y != null) this.y = st.y;
                            if (st.s != null) this.s = st.s;
                        }
                        this.h = this.clampH(custom ? st.h : this.defaultH());
                        this.save();
                    },
                    defaultH() { return this.clampH(window.innerHeight - 350); },
                    save() {
                        var st = { x: this.x, y: this.y, s: this.s, h: this.h };
                        window.__ugPan = st;
                        try { localStorage.setItem('tx-ug-board', JSON.stringify(st)); } catch (e) {}
                    },
                    clamp(n) { return Math.min(2.4, Math.max(0.35, n)); },
                    clampH(n) { return Math.min(window.innerHeight - 160, Math.max(180, n)); },
                    zoomAt(mx, my, next) {
                        var ns = this.clamp(next);
                        var k = ns / this.s;
                        this.x = mx - (mx - this.x) * k;
                        this.y = my - (my - this.y) * k;
                        this.s = ns;
                        this.save();
                    },
                    down(e) {
                        if (e.button !== 0) return;
                        if (e.target.closest && e.target.closest('.ai-ug-node, .ai-ug-board-tools, .ai-ug-resize')) return;
                        this.drag = true; this.sx = e.clientX; this.sy = e.clientY; this.ox = this.x; this.oy = this.y;
                        if (window.getSelection) window.getSelection().removeAllRanges();
                        e.preventDefault();
                    },
                    resizeDown(e) {
                        var ev = e.touches ? e.touches[0] : e;
                        this.resize = true; this.sy = ev.clientY; this.oh = this.h; this.drag = false;
                        if (window.getSelection) window.getSelection().removeAllRanges();
                        e.preventDefault();
                    },
                    move(e) {
                        if (this.resize) {
                            this.h = this.clampH(this.oh + (e.clientY - this.sy));
                            e.preventDefault();
                            return;
                        }
                        if (!this.drag) return;
                        this.x = this.ox + (e.clientX - this.sx);
                        this.y = this.oy + (e.clientY - this.sy);
                        e.preventDefault();
                    },
                    up() {
                        if (this.drag || this.resize) { this.drag = false; this.resize = false; this.save(); }
                    },
                    wheel(e) {
                        if (!(e.ctrlKey || e.metaKey)) {
                            this.x -= e.deltaX;
                            this.y -= e.deltaY;
                            this.save();
                            e.preventDefault();
                            return;
                        }
                        var r = this.$el.getBoundingClientRect();
                        this.zoomAt(e.clientX - r.left, e.clientY - r.top, this.s * (e.deltaY < 0 ? 1.12 : 1 / 1.12));
                        e.preventDefault();
                    },
                    tStart(e) {
                        if (e.touches.length === 2) {
                            var a = e.touches[0], b = e.touches[1];
                            this.pin = Math.hypot(a.clientX - b.clientX, a.clientY - b.clientY);
                            this.px = (a.clientX + b.clientX) / 2;
                            this.py = (a.clientY + b.clientY) / 2;
                            this.ps = this.s;
                            this.drag = false;
                            return;
                        }
                        if (e.touches.length === 1 && !(e.target.closest && e.target.closest('.ai-ug-node, .ai-ug-board-tools, .ai-ug-resize'))) {
                            this.drag = true; this.sx = e.touches[0].clientX; this.sy = e.touches[0].clientY; this.ox = this.x; this.oy = this.y;
                        }
                    },
                    tMove(e) {
                        if (this.resize && e.touches.length === 1) {
                            this.h = this.clampH(this.oh + (e.touches[0].clientY - this.sy));
                            return;
                        }
                        if (e.touches.length === 2 && this.pin) {
                            var a = e.touches[0], b = e.touches[1];
                            var dist = Math.hypot(a.clientX - b.clientX, a.clientY - b.clientY);
                            var r = this.$el.getBoundingClientRect();
                            this.zoomAt(this.px - r.left, this.py - r.top, this.ps * (dist / this.pin));
                            return;
                        }
                        if (this.drag && e.touches.length === 1) {
                            this.x = this.ox + (e.touches[0].clientX - this.sx);
                            this.y = this.oy + (e.touches[0].clientY - this.sy);
                        }
                    },
                    tEnd() { this.pin = 0; this.up(); },
                    bump(f) { this.zoomAt(this.$el.clientWidth / 2, this.$el.clientHeight / 2, this.s * f); },
                    reset() { this.x = 24; this.y = 28; this.s = 1; this.save(); }
                 }"
                 x-init="boot()"
                 :class="{ 'is-drag': drag, 'is-resize': resize }"
                 :style="'height:' + h + 'px'"
                 @mousedown="down($event)"
                 @mousemove.window="move($event)"
                 @mouseup.window="up()"
                 @wheel="wheel($event)"
                 @touchstart="tStart($event)"
                 @touchmove.prevent="tMove($event)"
                 @touchend="tEnd()">
                <div class="ai-ug-board-tools">
                    <button type="button" class="ai-ug-iconbtn" @click="bump(1.15)" title="{{ __('Zoom in') }}"><span class="material-symbols-outlined">add</span></button>
                    <button type="button" class="ai-ug-iconbtn" @click="bump(1/1.15)" title="{{ __('Zoom out') }}"><span class="material-symbols-outlined">remove</span></button>
                    <button type="button" class="ai-ug-iconbtn" @click="reset()" title="{{ __('Reset') }}"><span class="material-symbols-outlined">center_focus_weak</span></button>
                    <span class="ai-ug-zoom" x-text="Math.round(s * 100) + '%'">100%</span>
                </div>
                <p class="ai-ug-board-hint">{{ __('Drag to move. Ctrl+scroll or pinch to zoom. Drag the bottom edge to resize.') }}</p>
                <div class="ai-ug-world" :style="'width:{{ (int) $graph['w'] }}px;height:{{ (int) $graph['h'] }}px;transform:translate('+x+'px,'+y+'px) scale('+s+')'">
                    <svg class="ai-ug-edges" width="{{ (int) $graph['w'] }}" height="{{ (int) $graph['h'] }}" aria-hidden="true">
                        @foreach($graph['edges'] as $edge)
                            <path class="ai-ug-edge {{ $this->isLit($lit, $edge['from']) && $this->isLit($lit, $edge['to']) ? 'is-on' : 'is-dim' }}" d="{{ $edge['d'] }}"></path>
                        @endforeach
                    </svg>
                    @foreach($graph['lanes'] as $li => $lane)
                        <div class="ai-ug-lane" style="left: {{ 56 + ($li * (184 + 88)) }}px;">{{ $lane['label'] }}</div>
                    @endforeach
                    @foreach($treeNodes as $item)
                        @php
                            $node = $item['node'];
                            $on = $this->isLit($lit, $node['id']);
                            $label = $node['name'];
                            $tip = ($node['full'] ?? $node['name']).' · '.$fmt($node['requests']).' requests · '.$fmt($node['tokens']).' tokens';
                            $left = ($node['x'] ?? 0) - 92;
                            $top = ($node['y'] ?? 0) - 29;
                        @endphp
                        @if(!empty($node['ghost']))
                            <div class="ai-ug-node is-ghost" style="left: {{ $left }}px; top: {{ $top }}px;">
                                <span class="ai-ug-node-name">{{ $label }}</span>
                            </div>
                        @else
                            <button type="button"
                                    class="ai-ug-node {{ $on ? 'is-on' : 'is-dim' }} {{ $inspectModel === ($node['id'] ?? '') ? 'is-open' : '' }}"
                                    style="left: {{ $left }}px; top: {{ $top }}px;"
                                    title="{{ $tip }}"
                                    wire:click="selectNode({{ json_encode($item['layer']) }}, {{ json_encode((string) $node['id']) }})">
                                <span class="ai-ug-node-name">{{ $label }}</span>
                                <span class="ai-ug-node-metrics">{{ $fmt($node['requests']) }} req · {{ $fmt($node['tokens']) }} tok</span>
                            </button>
                        @endif
                    @endforeach
                </div>
                <div class="ai-ug-resize" @mousedown.stop="resizeDown($event)" @touchstart.stop="resizeDown($event)" title="{{ __('Drag to resize') }}"></div>
            </div>
        </div>

        @include('livewire.ai.usage-inspect')

        @php
            $metric = $seriesMetric === 'cost' ? 'cost' : 'tokens';
            $vals = array_map(fn ($p) => (float) $p[$metric], $series);
            $max = max(1, $vals ? max($vals) : 0);
            $n = count($series);
            $vw = 1000; $vh = 220; $pl = 52; $pr = 16; $pt = 18; $pb = 30;
            $iw = $vw - $pl - $pr; $ih = $vh - $pt - $pb;
            $pts = [];
            foreach ($series as $i => $p) {
                $x = $pl + ($n <= 1 ? $iw / 2 : ($i / max(1, $n - 1)) * $iw);
                $y = $pt + $ih - (((float) $p[$metric] / $max) * $ih);
                $pts[] = [round($x, 1), round($y, 1)];
            }
            $line = $pts === [] ? '' : implode(' ', array_map(fn ($p) => $p[0].','.$p[1], $pts));
            $area = $pts === [] ? '' : $pl.','.($pt + $ih).' '.$line.' '.($pts[count($pts) - 1][0]).','.($pt + $ih);
            $yTicks = [0, 0.25, 0.5, 0.75, 1];
            $xEvery = $n > 16 ? 2 : 1;
            $hoverPts = [];
            foreach ($series as $i => $p) {
                $hoverPts[] = [
                    'x' => $pts[$i][0],
                    'y' => $pts[$i][1],
                    'label' => $p['label'],
                    'tokens' => $fmt($p['tokens']),
                    'cost' => $this->formatMoney($p['cost']),
                ];
            }
        @endphp

        <div class="ai-series" wire:key="usage-series">
            <div class="ai-series-bar">
                <div class="ai-usage-seg" role="tablist">
                    <button type="button" class="{{ $seriesMetric === 'tokens' ? 'is-on' : '' }}" wire:click="setSeriesMetric('tokens')">{{ __('Tokens') }}</button>
                    <button type="button" class="{{ $seriesMetric === 'cost' ? 'is-on' : '' }}" wire:click="setSeriesMetric('cost')">{{ __('Cost') }}</button>
                </div>
            </div>
            <div class="ai-series-plot"
                 x-data="{
                    i: 0,
                    show: false,
                    pts: {{ json_encode($hoverPts) }},
                    move(e) {
                        if (!this.pts.length) return;
                        var r = this.$refs.plot.getBoundingClientRect();
                        var t = (e.clientX - r.left) / Math.max(1, r.width);
                        var vbX = {{ $pl }} + t * {{ $iw }};
                        var best = 0, bestD = 1e9;
                        for (var k = 0; k < this.pts.length; k++) {
                            var d = Math.abs(this.pts[k].x - vbX);
                            if (d < bestD) { bestD = d; best = k; }
                        }
                        this.i = best;
                        this.show = true;
                    },
                    leave() { this.show = false; }
                 }"
                 x-ref="plot"
                 @mousemove="move($event)"
                 @mouseleave="leave()">
                <svg class="ai-series-svg" viewBox="0 0 {{ $vw }} {{ $vh }}" preserveAspectRatio="none" role="img" aria-label="{{ __('Usage over time') }}">
                    @foreach($yTicks as $t)
                        @php $yy = $pt + $ih - ($t * $ih); $label = $seriesMetric === 'cost' ? $this->formatMoney($max * $t) : $fmt($max * $t); @endphp
                        <line x1="{{ $pl }}" y1="{{ $yy }}" x2="{{ $vw - $pr }}" y2="{{ $yy }}" class="ai-series-grid" />
                        <text x="{{ $pl - 8 }}" y="{{ $yy + 4 }}" class="ai-series-ylab">{{ $label }}</text>
                    @endforeach
                    @if($area !== '')
                        <polygon points="{{ $area }}" class="ai-series-fill" />
                        <polyline points="{{ $line }}" class="ai-series-line" />
                    @endif
                    @foreach($series as $i => $p)
                        @if($i % $xEvery === 0)
                            <text x="{{ $pts[$i][0] }}" y="{{ $vh - 8 }}" class="ai-series-xlab">{{ $p['label'] }}</text>
                        @endif
                    @endforeach
                </svg>
                <div x-show="show && pts.length" x-cloak>
                    <div class="ai-series-guide" :style="'left:' + (pts[i].x / {{ $vw }} * 100) + '%'"></div>
                    <div class="ai-series-dot" :style="'left:' + (pts[i].x / {{ $vw }} * 100) + '%;top:' + (pts[i].y / {{ $vh }} * 100) + '%'"></div>
                    <div class="ai-series-tip" :style="'left:' + (pts[i].x / {{ $vw }} * 100) + '%'">
                        <strong x-text="pts[i].label"></strong>
                        <div><span>{{ __('Tokens') }}</span><b x-text="pts[i].tokens"></b></div>
                        <div><span>{{ __('Cost') }}</span><b x-text="pts[i].cost"></b></div>
                    </div>
                </div>
            </div>
        </div>

        @include('livewire.ai.usage-breakdown', [
            'title' => __('Usage by model'),
            'kind' => 'model',
            'rows' => $overviewModels,
            'tableMetric' => $tableMetric,
            'empty' => __('No model usage in this range.'),
        ])

        @include('livewire.ai.usage-breakdown', [
            'title' => $isGlobal ? __('Usage by customer') : __('Usage by customer'),
            'kind' => 'customer',
            'rows' => $overviewUsers,
            'tableMetric' => $tableMetric,
            'empty' => __('No customer usage yet.'),
        ])
    @else
        @include('livewire.ai.usage-details')
    @endif
</div>
