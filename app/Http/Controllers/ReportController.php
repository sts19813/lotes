<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Report;
use App\Mail\CotizacionGenerada;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\Desarrollos;
use App\Models\Phase;
use App\Models\Stage;
use App\Services\AdaraService;

class ReportController extends Controller
{
    protected AdaraService $adara;

    public function __construct(AdaraService $adara)
    {
        $this->adara = $adara;
    }

    public function index()
    {
        $reports = Report::latest()->get();
        return view("reports.index", compact("reports"));
    }
    public function generate(Request $request)
    {
        $phaseName = null;
        $stageName = null;

        $data = $request->validate([
            "name" => "required|string",
            "area" => "required|numeric",
            "price_square_meter" => "required|numeric",
            "down_payment_percent" => "nullable|numeric",
            "financing_months" => "nullable|integer",
            "annual_appreciation" => "nullable|numeric",
            "chepina" => "nullable|string",
            "lead_name" => "nullable|string",
            "lead_phone" => "nullable|string",
            "lead_email" => "nullable|string",
            "city" => "nullable|string",
            "desarrollo_id" => "nullable|integer",
            "desarrollo_name" => "nullable|string",
            "phase_id" => "nullable|integer",
            "stage_id" => "nullable|integer",
            "project_id" => "nullable|integer",
            "source_type" => "nullable|string"
        ]);

        // Cálculos principales
        $precioTotal = $data["area"] * $data["price_square_meter"];
        $enganchePorc = $data["down_payment_percent"] ?? 30;
        $engancheMonto = $precioTotal * ($enganchePorc / 100);
        $meses = $data["financing_months"] ?? 60;
        $mensualidad = ($precioTotal - $engancheMonto) / max(1, $meses);
        $plusvaliaRate = $data["annual_appreciation"] ?? 0.15;
        $plusvaliaTotal = $precioTotal * pow(1 + $plusvaliaRate, 5);
        $roi = (($plusvaliaTotal - $precioTotal) / $precioTotal) * 100;

        // Proyección anual
        $years = [];
        $totalAnios = (int) ceil($meses / 12);

        for ($year = 0; $year <= $totalAnios; $year++) {
            $valorProp = $precioTotal * pow(1 + $plusvaliaRate, $year);

            if ($year === 0) {
                $mesesPagados = 0;
            } elseif ($year === 1) {
                $mesesPagados = min($meses, 11);
            } else {
                $mesesPagados = min($meses, 11 + (($year - 1) * 12));
            }

            $montoPagado = $engancheMonto + $mensualidad * $mesesPagados;
            $plusvaliaAcum = $valorProp - $precioTotal;
            $roiAnual = (($valorProp - $precioTotal) / $precioTotal) * 100;

            $years[] = [
                "year" => $year,
                "valorProp" => $valorProp,
                "montoPagado" => $montoPagado,
                "plusvaliaAcum" => $plusvaliaAcum,
                "roiAnual" => $roiAnual,
            ];
        }

        $chepinaBase64 = $this->getChepinaBase64($data['chepina'] ?? null);
        $chepinaUrl = $data["chepina"] ? url($data["chepina"]) : url("/assets/img/CHEPINA.svg");

        $desarrolloName = $phaseName = $stageName = null;

        if (!empty($data["desarrollo_id"])) {
            $desarrollo = Desarrollos::find($data["desarrollo_id"]);
            $sourceType = $data["source_type"] ?? $desarrollo?->source_type ?? 'adara';

            if ($sourceType === 'adara') {
                if (!empty($data["project_id"])) {
                    $desarrolloName = $this->adara->getProjectName($data["project_id"]);
                }
                if (!empty($data["phase_id"])) {
                    $phaseName = $this->adara->getPhaseName($data["project_id"], $data["phase_id"]);
                }
                if (!empty($data["stage_id"])) {
                    $stageName = $this->adara->getStageName($data["project_id"], $data["phase_id"], $data["stage_id"]);
                }
            } else {
                $desarrolloName = $data["desarrollo_name"];

                $Phase = Phase::find($data["phase_id"]);
                $phaseName = $Phase?->name ?? null;

                $Stage = Stage::find($data["stage_id"]);
                $stageName = $Stage?->name ?? null;
            }
        }


        // Guardar en BD
        try {
            Report::create([
                "name" => $data["name"],
                "area" => round($data["area"], 2),
                "price_square_meter" => round($data["price_square_meter"], 2),
                "down_payment_percent" => round($enganchePorc, 2),
                "financing_months" => $meses,
                "annual_appreciation" => round($plusvaliaRate, 2),
                "chepina" => $data["chepina"] ?? null,
                "lead_name" => $data["lead_name"] ?? null,
                "lead_phone" => $data["lead_phone"] ?? null,
                "lead_email" => $data["lead_email"] ?? null,
                "city" => $data["city"] ?? null,
                "precio_total" => round($precioTotal, 2),
                "enganche_porcentaje" => round($enganchePorc, 2),
                "enganche_monto" => round($engancheMonto, 2),
                "mensualidad" => round($mensualidad, 2),
                "plusvalia_total" => round($plusvaliaTotal, 2),
                "roi" => round($roi, 2),
                "years_data" => $years,
                "chepina_url" => $chepinaUrl,
                "desarrollo_id" => $data["project_id"] ?? null,
                "desarrollo_name" => $desarrolloName ?? null,
                "phase_id" => $data["phase_id"] ?? null,
                "stage_id" => $data["stage_id"] ?? null,
                "source_type" => $data["source_type"] ?? null,
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        $desarrollo = $data['desarrollo_id'] ? Desarrollos::find($data['desarrollo_id']) : null;
        $desarrolloLogo = $desarrollo?->path_logo ?? null;

        $pdfData = array_merge($data, [
            "precioTotal" => $precioTotal,
            "enganchePorc" => $enganchePorc,
            "engancheMonto" => $engancheMonto,
            "meses" => $meses,
            "mensualidad" => $mensualidad,
            "plusvaliaRate" => $plusvaliaRate,
            "plusvaliaTotal" => $plusvaliaTotal,
            "roi" => $roi,
            "years" => $years,
            "chepinaUrl" => $chepinaUrl,
            "desarrollo_id" => $data["desarrollo_id"] ?? null,
            "desarrollo_name" => $desarrolloName ?? null,
            "phase_id" => $data["phase_id"] ?? null,
            "stage_id" => $data["stage_id"] ?? null,
            "phase_name" => $phaseName,
            "stage_name" => $stageName,
            "desarrollo_logo" => $desarrolloLogo,
            "chepina_base64" => $chepinaBase64,
        ]);

        // Generar PDF
        $pdf = Pdf::loadView("reports.cotizacion", ["lot" => (object) $pdfData])
            ->setPaper("a4", "portrait")
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->output();

        // Enviar al usuario
        if (!empty($data["lead_email"])) {
            Mail::to($data["lead_email"])->send(new CotizacionGenerada((object) $pdfData, $pdf));
        } 
        // Enviar al admin
        Mail::to("hi@davidsabido.com")->send(new CotizacionGenerada((object) $pdfData, $pdf));

        // Retornar descarga
        return response()->streamDownload(fn() => print($pdf), "cotizacion_" . $data["name"] . ".pdf");
    }

    public function download(Report $report)
    {
        $desarrollo = Desarrollos::find($report->desarrollo_id);
        $sourceType = $report->source_type ?? $desarrollo?->source_type ?? 'adara';

        $desarrolloName = $phaseName = $stageName = null;

        if ($sourceType === 'adara') {
            if (!empty($report->desarrollo_id)) {
                $desarrolloName = $this->adara->getProjectName($report->desarrollo_id);
            }
            if (!empty($report->phase_id)) {
                $phaseName = $this->adara->getPhaseName($report->desarrollo_id, $report->phase_id);
            }
            if (!empty($report->stage_id)) {
                $stageName = $this->adara->getStageName($report->desarrollo_id, $report->phase_id, $report->stage_id);
            }
        } else {
            $desarrolloName = $report->desarrollo_name;
            
            $Phase = Phase::find($report->phase_id);
            $phaseName = $Phase?->name ?? null;

            $Stage = Stage::find($report->stage_id);
            $stageName = $Stage?->name ?? null;
        }

        $chepinaBase64 = $this->getChepinaBase64($report->chepina ?? null);

        $pdfData = [
            "name" => $report->name,
            "area" => $report->area,
            "price_square_meter" => $report->price_square_meter,
            "precioTotal" => $report->precio_total,
            "enganchePorc" => $report->enganche_porcentaje,
            "engancheMonto" => $report->enganche_monto,
            "meses" => $report->financing_months,
            "mensualidad" => $report->mensualidad,
            "plusvaliaRate" => $report->annual_appreciation,
            "plusvaliaTotal" => $report->plusvalia_total,
            "roi" => $report->roi,
            "chepinaUrl" => $report->chepina_url,
            "chepina_base64" => $chepinaBase64,
            "lead_name" => $report->lead_name,
            "lead_phone" => $report->lead_phone,
            "lead_email" => $report->lead_email,
            "city" => $report->city,
            "years" => $report->years_data ?? [],
            "desarrollo_id" => $report->desarrollo_id,
            "desarrollo_name" => $desarrolloName,
            "phase_id" => $report->phase_id,
            "stage_id" => $report->stage_id,
            "phase_name" => $phaseName,
            "stage_name" => $stageName,
            "project_id" => $report->project_id ?? null,
        ];

        $pdf = Pdf::loadView("reports.cotizacion", ["lot" => (object) $pdfData])
            ->setPaper("a4", "portrait")
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0);

        return $pdf->download("cotizacion_" . $report->name . ".pdf");
    }

    /**
     * Obtiene la imagen CHEPINA en Base64, con fallback a local
     */
    private function getChepinaBase64(?string $url): string
    {
        if ($url) {
            try {
                $response = Http::withoutVerifying()->get($url);
                if ($response->successful()) {
                    $mimeType = $response->header('Content-Type') ?? 'image/jpeg';
                    return 'data:' . $mimeType . ';base64,' . base64_encode($response->body());
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        $chepinaPath = public_path("assets/img/CHEPINA.svg");
        return 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($chepinaPath));
    }
}