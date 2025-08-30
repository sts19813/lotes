<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Report;
use App\Mail\CotizacionGenerada;
use Illuminate\Support\Facades\Mail;

class ReportController extends Controller
{

    public function index()
    {
        $reports = Report::latest()->paginate(10);

        return view('reports.index', compact('reports'));
    }
    public function generate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'area' => 'required|numeric',
            'price_square_meter' => 'required|numeric',
            'down_payment_percent' => 'nullable|numeric',
            'financing_months' => 'nullable|integer',
            'annual_appreciation' => 'nullable|numeric',
            'chepina' => 'nullable|string',
            'lead_name' => 'nullable|string',
            'lead_phone' => 'nullable|string',
            'lead_email' => 'nullable|string',
            'city' => 'nullable|string',
            'desarrollo_id' => 'nullable|integer',
            'desarrollo_name' => 'nullable|string',
            'phase_id' => 'nullable|integer',
            'stage_id' => 'nullable|integer',
        ]);

        // Cálculos principales
        $precioTotal = $data['area'] * $data['price_square_meter'];
        $enganchePorc = $data['down_payment_percent'] ?? 30;
        $engancheMonto = $precioTotal * ($enganchePorc / 100);
        $meses = $data['financing_months'] ?? 60;
        $mensualidad = ($precioTotal - $engancheMonto) / max(1, $meses);
        $plusvaliaRate = $data['annual_appreciation'] ?? 0.15;
        $plusvaliaTotal = $precioTotal * pow(1 + $plusvaliaRate, 5);
        $roi = ($plusvaliaTotal - $precioTotal) / $precioTotal * 100;

        // Proyección anual
        $years = [];
        $totalAnios = (int) ceil($meses / 12);

        for ($year = 0; $year <= $totalAnios; $year++) {
        $valorProp = $precioTotal * pow(1 + $plusvaliaRate, $year);

        // Lo pagado hasta ese año (enganche + mensualidades de ese año)
        $mesesPagados = min($meses, $year * 12);
        $montoPagado = $engancheMonto + ($mensualidad * $mesesPagados);

        $plusvaliaAcum = $valorProp - $precioTotal;
        $roiAnual = ($valorProp - $precioTotal) / $precioTotal * 100;

        $years[] = [
            'year' => $year,
            'valorProp' => $valorProp,
            'montoPagado' => $montoPagado,
            'plusvaliaAcum' => $plusvaliaAcum,
            'roiAnual' => $roiAnual,
        ];
    }

        $chepinaUrl = $data['chepina'] ? url($data['chepina']) : url('/assets/img/CHEPINA.svg');




        // Guardar en BD
      try {
            $report = Report::create([
                'name' => $data['name'],
                'area' => round($data['area'], 2),
                'price_square_meter' => round($data['price_square_meter'], 2),
                'down_payment_percent' => round($enganchePorc, 2),
                'financing_months' => $meses,
                'annual_appreciation' => round($plusvaliaRate, 2),
                'chepina' => $data['chepina'] ?? null,
                'lead_name' => $data['lead_name'] ?? null,
                'lead_phone' => $data['lead_phone'] ?? null,
                'lead_email' => $data['lead_email'] ?? null,
                'city' => $data['city'] ?? null,
                'precio_total' => round($precioTotal, 2),
                'enganche_porcentaje' => round($enganchePorc, 2),
                'enganche_monto' => round($engancheMonto, 2),
                'mensualidad' => round($mensualidad, 2),
                'plusvalia_total' => round($plusvaliaTotal, 2),
                'roi' => round($roi, 2),
                'years_data' => $years,
                'chepina_url' => $chepinaUrl,
                'desarrollo_id' => $data['desarrollo_id'] ?? null,
                'desarrollo_name' => $data['desarrollo_name'] ?? null,
                'phase_id' => $data['phase_id'] ?? null,
                'stage_id' => $data['stage_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }                // Preparar datos para PDF
        $pdfData = array_merge($data, [
            'precioTotal' => $precioTotal,
            'enganchePorc' => $enganchePorc,
            'engancheMonto' => $engancheMonto,
            'meses' => $meses,
            'mensualidad' => $mensualidad,
            'plusvaliaRate' => $plusvaliaRate,
            'plusvaliaTotal' => $plusvaliaTotal,
            'roi' => $roi,
            'years' => $years,
            'chepinaUrl' => $chepinaUrl,
               // ✅ NUEVOS CAMPOS para PDF
            'desarrollo_id' => $data['desarrollo_id'] ?? null,
            'desarrollo_name' => $data['desarrollo_name'] ?? null,
            'phase_id' => $data['phase_id'] ?? null,
            'stage_id' => $data['stage_id'] ?? null,
        ]);

    

        $pdf = Pdf::loadView('reports.cotizacion', ['lot' => (object) $pdfData])
                ->setPaper('a4', 'portrait')
                ->output(); // importante: usar ->output() para pasar a Mail

        // Enviar al usuario
        if (!empty($data['lead_email'])) {
            Mail::to($data['lead_email'])->send(new CotizacionGenerada((object)$pdfData, $pdf));
        }

        // Enviar al admin
        Mail::to('hi@davidsabido.com	')->send(new CotizacionGenerada((object)$pdfData, $pdf));

        // Retornar descarga al navegador
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf;
        }, 'cotizacion_'.$data['name'].'.pdf');
    }

    public function download(Report $report)
    {
        // reconstruir arreglo con las mismas llaves que en generate()
        $pdfData = [
            'name' => $report->name,
            'area' => $report->area,
            'price_square_meter' => $report->price_square_meter,
            'precioTotal' => $report->precio_total,
            'enganchePorc' => $report->enganche_porcentaje,
            'engancheMonto' => $report->enganche_monto,
            'meses' => $report->financing_months,
            'mensualidad' => $report->mensualidad,
            'plusvaliaRate' => $report->annual_appreciation,
            'plusvaliaTotal' => $report->plusvalia_total,
            'roi' => $report->roi,
            'chepinaUrl' => $report->chepina_url,
            'lead_name' => $report->lead_name,
            'lead_phone' => $report->lead_phone,
            'lead_email' => $report->lead_email,
            'city' => $report->city,
            'years' => $report->years_data ?? [],
            'desarrollo_id' => $report->desarrollo_id,
            'desarrollo_name' => $report->desarrollo_name,
            'phase_id' => $report->phase_id,
            'stage_id' => $report->stage_id,
        ];

        $pdf = Pdf::loadView('reports.cotizacion', ['lot' => (object) $pdfData])
            ->setPaper('a4', 'portrait');

        return $pdf->download('cotizacion_'.$report->name.'.pdf');
    }
   
}
