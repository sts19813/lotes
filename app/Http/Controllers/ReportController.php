<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Report;

class ReportController extends Controller
{
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
        for ($year = 0; $year <= 5; $year++) {
            $valorProp = $precioTotal * pow(1 + $plusvaliaRate, $year);
            $montoPagado = ($year >= 1) ? ($mensualidad * 12 * $year + $engancheMonto) : $engancheMonto;
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
        ]);

        // Generar PDF
        $pdf = Pdf::loadView('reports.cotizacion', ['lot' => (object) $pdfData])
            ->setPaper('a4', 'portrait')
            ->getDomPDF()
            ->set_option('isRemoteEnabled', true);

        return Pdf::loadView('reports.cotizacion', ['lot' => (object) $pdfData])
                  ->setPaper('a4', 'portrait')
                  ->download('cotizacion_'.$data['name'].'.pdf');
    }

    // Descargar directamente por admin desde panel (GET)
   
}
