<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Factories\LotsDataSourceFactory;

class ProjectController extends Controller
{
    public function __invoke(Request $request)
    {
        $source = $request->get('source', 'adara');

        $dataSource = LotsDataSourceFactory::make($source);

        return response()->json(
            $dataSource->getProjects()
        );
    }
}
