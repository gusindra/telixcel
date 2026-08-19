@component('ai.layout')
    <section class="tx-section">
        <div class="tx-card ai-docs">
            <header class="ai-docs-bar">
                <div class="ai-usage-seg" role="tablist">
                    <a href="{{ route('ai.docs') }}" class="{{ $tab === 'endpoint' ? 'is-on' : '' }}" role="tab">{{ __('Endpoint') }}</a>
                    <a href="{{ route('ai.docs', ['tab' => 'docs']) }}" class="{{ $tab === 'docs' ? 'is-on' : '' }}" role="tab">{{ __('Docs') }}</a>
                </div>
                @if($tab === 'endpoint')
                    <div class="ai-docs-bar-actions">
                        <a href="{{ route('ai.docs.spec') }}" class="tx-btn tx-btn-ghost" target="_blank" rel="noopener">{{ __('Open JSON') }}</a>
                        <a href="{{ route('ai.docs.spec') }}" class="tx-btn" download="ai_doc.json">{{ __('Download JSON') }}</a>
                    </div>
                @endif
            </header>

            @if($tab === 'docs')
                @include('ai.docs-guide')
            @else
                <div class="ai-swagger">
                    <div id="swagger-ui"></div>
                </div>
                <link rel="stylesheet" href="{{ l5_swagger_asset('default', 'swagger-ui.css') }}">
                <style>
                    html { overflow-y: auto; }
                    body { background: var(--tx-bg) !important; }
                </style>
                <script src="{{ l5_swagger_asset('default', 'swagger-ui-bundle.js') }}"></script>
                <script>
                    window.addEventListener('load', function () {
                        if (!window.SwaggerUIBundle) return;
                        window.ui = SwaggerUIBundle({
                            spec: @json($spec),
                            dom_id: '#swagger-ui',
                            deepLinking: true,
                            filter: true,
                            persistAuthorization: true,
                            tryItOutEnabled: true,
                            docExpansion: 'list',
                            defaultModelsExpandDepth: 0,
                            presets: [SwaggerUIBundle.presets.apis],
                            layout: 'BaseLayout'
                        });
                    });
                </script>
            @endif
        </div>
    </section>
@endcomponent
