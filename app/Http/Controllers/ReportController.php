<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Report;
use App\Mail\CotizacionGenerada;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\Desarrollos;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::latest()->paginate(10);
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

        $chepinaUrl = $data["chepina"]
            ? url($data["chepina"])
            : url("/assets/img/CHEPINA.svg");


        $chepinaBase64 = null;

       if (!empty($data['chepina'])) {
            try {
                // Si la URL requiere HTTPS sin verificar, usa withoutVerifying()
                $response = Http::withoutVerifying()->get($data['chepina']);
                
                if ($response->successful()) {
                    $mimeType = $response->header('Content-Type') ?? 'image/jpeg';
                    $chepinaBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($response->body());
                } else {
                    throw new \Exception('Respuesta no exitosa: ' . $response->status());
                }

            } catch (\Exception $e) {
               

                // fallback a imagen local
                $chepinaPath = public_path("assets/img/CHEPINA.svg");
                if (file_exists($chepinaPath)) {
                    $chepinaBase64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($chepinaPath));
                }
            }
        } else {
            // fallback a imagen local
            $chepinaPath = public_path("assets/img/CHEPINA.svg");
            if (file_exists($chepinaPath)) {
                $chepinaBase64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($chepinaPath));
            }
        }

        if (!empty($data["project_id"])) {
            $desarrolloName = $this->getProjectName($data["project_id"]);
        }

        if (!empty($data["project_id"]) && !empty($data["phase_id"])) {
            $phaseName = $this->getPhaseName(
                $data["project_id"],
                $data["phase_id"]
            ); // string, no array
        }

        if (
            !empty($data["project_id"]) &&
            !empty($data["phase_id"]) &&
            !empty($data["stage_id"])
        ) {
            $stageName = $this->getStageName(
                $data["project_id"],
                $data["phase_id"],
                $data["stage_id"]
            );
        }

        // Guardar en BD
        try {
            $report = Report::create([
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
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        } // Preparar datos para PDF
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
        ]);

        $desarrollo = null;
        if (!empty($data['desarrollo_id'])) {
            $desarrollo = Desarrollos::find($data['desarrollo_id']);
        }
        $desarrolloLogo = $desarrollo?->path_logo ?? null;
        $desarrolloNombre = $desarrollo?->name ?? null;

        // Agregar al arreglo de datos para el PDF
        $pdfData = array_merge($pdfData, [
            "desarrollo_logo" => $desarrolloLogo,
            "desarrollo_name" => $desarrolloNombre ?? $pdfData['desarrollo_name'] ?? null,
            "chepina_base64" => $chepinaBase64 ?? url("/assets/img/CHEPINA.svg"),
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
            Mail::to($data["lead_email"])->send(
                new CotizacionGenerada((object) $pdfData, $pdf)
            );
        }

        // Enviar al admin
        Mail::to("hi@davidsabido.com	")->send(
            new CotizacionGenerada((object) $pdfData, $pdf)
        );

        // Retornar descarga al navegador
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf;
        }, "cotizacion_" . $data["name"] . ".pdf");
    }

  
    public function download(Report $report)
    {
        $phaseName = null;
        $stageName = null;
        $desarrolloName = null;

        if (!empty($report->desarrollo_id)) {
            $desarrolloName = $this->getProjectName($report->desarrollo_id);
        }

        if (!empty($report->desarrollo_id) && !empty($report->phase_id)) {
            $phaseName = $this->getPhaseName(
                $report->desarrollo_id,
                $report->phase_id
            );
        }

        if (!empty($report->desarrollo_id) && !empty($report->phase_id) && !empty($report->stage_id)) {
            $stageName = $this->getStageName(
                $report->desarrollo_id,
                $report->phase_id,
                $report->stage_id
            );
        }

        // Obtener chepina_base64
        $chepinaBase64 = null;
        if (!empty($report->chepina)) {
            try {
                $response = Http::withoutVerifying()->get($report->chepina);
                if ($response->successful()) {
                    $mimeType = $response->header('Content-Type') ?? 'image/jpeg';
                    $chepinaBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($response->body());
                } else {
                    throw new \Exception('Respuesta no exitosa: ' . $response->status());
                }
            } catch (\Exception $e) {
                $chepinaPath = public_path("assets/img/CHEPINA.svg");
                if (file_exists($chepinaPath)) {
                    $chepinaBase64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($chepinaPath));
                }
            }
        } else {
            $chepinaPath = public_path("assets/img/CHEPINA.svg");
            if (file_exists($chepinaPath)) {
                $chepinaBase64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($chepinaPath));
            }
        }

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
            "chepina_base64" => $chepinaBase64 ?? url("/assets/img/CHEPINA.svg"),
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

    public function getProjectName(int $projectId): ?string
    {
        try {
            $response = Http::withHeaders([
                "accept" => "application/json",
                "X-API-KEY" => env("ADARA_API_KEY"),
            ])
                ->withoutVerifying()
                ->get(env("ADARA_API_URL") . "/projects");

            if ($response->successful()) {
                $projects = $response->json();

                foreach ($projects as $project) {
                    if ((int) $project["id"] === $projectId) {
                        return $project["name"] ?? null;
                    }
                }
            }
        } catch (\Exception $e) {
            // opcional: log del error
        }

        return null;
    }

    public function getPhaseName(int $projectId, int $phaseId): ?string
    {
        try {
            $response = Http::withHeaders([
                "accept" => "application/json",
                "X-API-KEY" => env("ADARA_API_KEY"),
            ])
                ->withoutVerifying()
                ->get(env("ADARA_API_URL") . "/projects/{$projectId}/phases");

            if ($response->successful()) {
                $phases = $response->json();

                // Buscar la fase por ID
                foreach ($phases as $phase) {
                    if ((int) $phase["id"] === $phaseId) {
                        return $phase["name"] ?? null;
                    }
                }
            }
        } catch (\Exception $e) {
            // Aquí puedes loguear el error si quieres
        }

        return null;
    }

    private function getStageName(int $projectId, int $phaseId, int $stageId): ?string {
        try {
            $response = Http::withHeaders([
                "accept" => "application/json",
                "X-API-KEY" => env("ADARA_API_KEY"),
            ])
                ->withoutVerifying()
                ->get(
                    env("ADARA_API_URL") .
                        "/projects/{$projectId}/phases/{$phaseId}/stages/{$stageId}"
                );

            if ($response->successful()) {
                $data = $response->json();

                return $data["stage"]["name"] ?? null;
            }
        } catch (\Exception $e) {
        }

        return null;
    }
}
