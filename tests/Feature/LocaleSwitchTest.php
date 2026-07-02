<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Language switcher: route stores locale; SetLocale middleware applies it. */
class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function switching_to_indonesian_stores_the_locale(): void
    {
        $this->get('/lang/id')->assertSessionHas('locale', 'id');
    }

    /** @test */
    public function switching_to_english_stores_the_locale(): void
    {
        $this->get('/lang/en')->assertSessionHas('locale', 'en');
    }

    /** @test */
    public function an_unsupported_locale_is_ignored(): void
    {
        $this->get('/lang/zz');
        $this->assertNotSame('zz', session('locale'));
    }

    /** @test */
    public function the_middleware_applies_the_session_locale(): void
    {
        $this->withSession(['locale' => 'id'])->get('/login');
        $this->assertSame('id', app()->getLocale());

        $this->withSession(['locale' => 'en'])->get('/login');
        $this->assertSame('en', app()->getLocale());
    }
}
