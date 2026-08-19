<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiApplication;

class AiAdminController extends Controller
{
    public function applications()
    {
        return view('ai.applications');
    }

    public function usage()
    {
        return view('ai.usage');
    }

    public function logs()
    {
        return view('ai.logs');
    }

    public function settings()
    {
        return view('ai.settings');
    }

    public function application(AiApplication $application)
    {
        return $this->showSection($application, 'settings');
    }

    public function applicationUsage(AiApplication $application)
    {
        return $this->showSection($application, 'usage');
    }

    public function applicationRequests(AiApplication $application)
    {
        return $this->showSection($application, 'requests');
    }

    public function applicationTest(AiApplication $application)
    {
        return $this->showSection($application, 'test');
    }

    private function showSection(AiApplication $application, string $section)
    {
        return view('ai.application-show', compact('application', 'section'));
    }
}
