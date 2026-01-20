<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Factories\LotsDataSourceFactory;

class PhaseController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'source'     => 'nullable|in:adara,naboo',
        ]);

        $source = $request->get('source', 'adara');

        $dataSource = LotsDataSourceFactory::make($source);

        return response()->json(
            $dataSource->getPhases((int) $request->project_id)
        );
    }
}
