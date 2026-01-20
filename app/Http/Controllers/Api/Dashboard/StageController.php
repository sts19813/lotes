<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Factories\LotsDataSourceFactory;

class StageController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'phase_id'   => 'required|integer',
            'source'     => 'nullable|in:adara,naboo',
        ]);

        $source = $request->get('source', 'adara');

        $dataSource = LotsDataSourceFactory::make($source);

        return response()->json(
            $dataSource->getStages(
                (int) $request->project_id,
                (int) $request->phase_id
            )
        );
    }
}
