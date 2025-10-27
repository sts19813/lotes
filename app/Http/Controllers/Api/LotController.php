<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lot;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;


class LotController extends Controller
{

    public function index(Request $request)
    {
        $query = Lot::with(['stage.phase.project']);

        if ($request->project_id) {
            $query->whereHas('stage.phase.project', function($q) use ($request) {
                $q->where('id', $request->project_id);
            });
        }
        if ($request->phase_id) {
            $query->whereHas('stage.phase', function($q) use ($request) {
                $q->where('id', $request->phase_id);
            });
        }
        if ($request->stage_id) {
            $query->where('stage_id', $request->stage_id);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'name' => 'required|string',
            'depth' => 'nullable|numeric',
            'front' => 'nullable|numeric',
            'area' => 'nullable|numeric',
            'price_square_meter' => 'nullable|numeric',
            'total_price' => 'nullable|numeric',
            'status' => 'nullable|string',
            'chepina' => 'nullable|string'
        ]);

        $lot = Lot::create($request->all());

        return response()->json(
            $lot->load(['stage.phase.project', 'customFields']),
            201
        );
    }

    public function show(Lot $lot)
    {
        return $lot->load(['stage.phase.project', 'customFields']);
    }

    public function update(Request $request, Lot $lot)
    {
        $lot->update($request->all());
        return response()->json($lot->load(['stage.phase.project', 'customFields']));
    }

    public function destroy(Lot $lot)
    {
        $lot->delete();
        return response()->json(null, 204);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        $file = $request->file('file')->getPathname();
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $errors = [];
        $successCount = 0;

        // Se comienzan a leer datos desde la fila 3 (índice 2)
        foreach (array_slice($rows, 2) as $index => $row) {
            $rowNumber = $index + 3; // Para indicar el número de Excel

            if (empty($row[3])) continue; // Si nombre vacío, se ignora

            $validator = Validator::make([
                'project_id' => $row[0],
                'phase_id'   => $row[1],
                'stage_id'   => $row[2],
                'name'       => $row[3],
                'depth'      => $row[4],
                'front'      => $row[5],
                'area'       => $row[6],
                'price_square_meter' => $row[7],
                'total_price' => $row[8],
                'status'      => $row[9],
                'chepina'     => $row[10],
            ], [
                'project_id' => 'required|exists:projects,id',
                'phase_id'   => 'required|exists:phases,id',
                'stage_id'   => 'required|exists:stages,id',
                'name'       => 'required|string',
                'depth'      => 'nullable|numeric|min:0',
                'front'      => 'nullable|numeric|min:0',
                'area'       => 'nullable|numeric|min:0',
                'price_square_meter' => 'nullable|numeric|min:0',
                'total_price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $errorMessages = implode(" | ", $validator->errors()->all());
                $errors[] = "Fila $rowNumber: $errorMessages";
                continue;
            }

            Lot::create([
                'project_id' => $row[0],
                'phase_id' => $row[1],
                'stage_id' => $row[2],
                'name' => $row[3],
                'depth' => $row[4],
                'front' => $row[5],
                'area' => $row[6],
                'price_square_meter' => $row[7],
                'total_price' => $row[8],
                'status' => $row[9],
                'chepina' => $row[10],
            ]);

            $successCount++;
        }

        return response()->json([
            'success' => $successCount,
            'errors' => $errors
        ], 200);
    }
}
