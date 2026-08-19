<div class="ai-guide">
    <header class="ai-guide-intro">
        <h2>{{ __('ai.guide.title') }}</h2>
        <p>{{ __('ai.guide.overview') }}</p>
    </header>

    <div class="ai-guide-flow" role="img" aria-label="{{ __('ai.guide.flow_label') }}">
        <article class="ai-guide-node">
            <span>1</span>
            <strong>{{ __('ai.guide.s1_title') }}</strong>
            <small>{{ __('ai.guide.s1_body') }}</small>
        </article>
        <div class="ai-guide-join" aria-hidden="true"><span class="material-symbols-outlined">chevron_right</span></div>
        <article class="ai-guide-node">
            <span>2</span>
            <strong>{{ __('ai.guide.s2_title') }}</strong>
            <small>{{ __('ai.guide.s2_body') }}</small>
        </article>
        <div class="ai-guide-join" aria-hidden="true"><span class="material-symbols-outlined">chevron_right</span></div>
        <article class="ai-guide-node is-core">
            <span>3</span>
            <strong>{{ __('ai.guide.s3_title') }}</strong>
            <small>{{ __('ai.guide.s3_body') }}</small>
        </article>
        <div class="ai-guide-join" aria-hidden="true"><span class="material-symbols-outlined">chevron_right</span></div>
        <article class="ai-guide-node">
            <span>4</span>
            <strong>{{ __('ai.guide.s4_title') }}</strong>
            <small>{{ __('ai.guide.s4_body') }}</small>
        </article>
    </div>

    <div class="ai-guide-cols">
        <section>
            <h3>{{ __('ai.guide.example_title') }}</h3>
            <ol class="ai-guide-list">
                <li>
                    <div>
                        <strong>{{ __('ai.guide.ex1_title') }}</strong>
                        <span>{{ __('ai.guide.ex1_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.ex2_title') }}</strong>
                        <span>{{ __('ai.guide.ex2_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.ex3_title') }}</strong>
                        <span>{{ __('ai.guide.ex3_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.ex4_title') }}</strong>
                        <span>{{ __('ai.guide.ex4_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.ex5_title') }}</strong>
                        <span>{{ __('ai.guide.ex5_body') }}</span>
                    </div>
                </li>
            </ol>
        </section>
        <section>
            <h3>{{ __('ai.guide.checks_title') }}</h3>
            <ol class="ai-guide-list">
                <li>
                    <div>
                        <strong>{{ __('ai.guide.c1_title') }}</strong>
                        <span>{{ __('ai.guide.c1_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.c2_title') }}</strong>
                        <span>{{ __('ai.guide.c2_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.c3_title') }}</strong>
                        <span>{{ __('ai.guide.c3_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.c4_title') }}</strong>
                        <span>{{ __('ai.guide.c4_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.c5_title') }}</strong>
                        <span>{{ __('ai.guide.c5_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.c6_title') }}</strong>
                        <span>{{ __('ai.guide.c6_body') }}</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>{{ __('ai.guide.c7_title') }}</strong>
                        <span>{{ __('ai.guide.c7_body') }}</span>
                    </div>
                </li>
            </ol>
        </section>
    </div>

    <div class="ai-guide-cols">
        <section>
            <h3>{{ __('ai.guide.key_title') }}</h3>
            <ul class="ai-guide-rules">
                <li class="is-yes">{{ __('ai.guide.key_yes') }}</li>
                <li class="is-no">{{ __('ai.guide.key_no_users') }}</li>
                <li class="is-no">{{ __('ai.guide.key_no_client') }}</li>
            </ul>
        </section>
        <section>
            <h3>{{ __('ai.guide.user_title') }}</h3>
            <ul class="ai-guide-rules">
                <li class="is-yes">{{ __('ai.guide.user_yes_id') }}</li>
                <li class="is-yes">{{ __('ai.guide.user_yes_usage') }}</li>
                <li>{{ __('ai.guide.user_required') }}</li>
            </ul>
        </section>
    </div>

    <section class="ai-guide-notes">
        <h3>{{ __('ai.guide.remember_title') }}</h3>
        <dl>
            <div>
                <dt>{{ __('ai.guide.r_endpoint') }}</dt>
                <dd class="font-mono">POST /api/v1/ai/chat/completions</dd>
            </div>
            <div>
                <dt>{{ __('ai.guide.r_header') }}</dt>
                <dd class="font-mono">Authorization: Bearer sk-…</dd>
            </div>
            <div>
                <dt>{{ __('ai.guide.r_test') }}</dt>
                <dd>{{ __('ai.guide.r_test_body') }}</dd>
            </div>
            <div>
                <dt>{{ __('ai.guide.r_staging') }}</dt>
                <dd>{{ __('ai.guide.r_staging_body') }}</dd>
            </div>
        </dl>
    </section>
</div>
