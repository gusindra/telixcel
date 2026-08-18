<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AiSwaggerController extends Controller
{
    public function ui(Request $request)
    {
        $tab = $request->query('tab', 'endpoint');
        if (! in_array($tab, ['endpoint', 'docs'], true)) {
            $tab = 'endpoint';
        }

        return view('ai.docs', [
            'spec' => $this->readSpec(),
            'tab' => $tab,
        ]);
    }

    public function spec(): Response
    {
        $path = $this->specPath();

        return response((string) file_get_contents($path), 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="ai_doc.json"',
        ]);
    }

    private function readSpec(): array
    {
        $path = $this->specPath();
        $data = json_decode((string) file_get_contents($path), true);
        abort_unless(is_array($data), 500);

        return $data;
    }

    private function specPath(): string
    {
        $path = base_path('swagger/ai_doc.json');
        abort_unless(is_file($path), 404);

        return $path;
    }
}
