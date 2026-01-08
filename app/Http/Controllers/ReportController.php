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

            "precio_real" => "required|numeric",
            "precio_final" => "required|numeric",
            "tipo_aplicado" => "required|string",

            // Financiero
            "porcentaje_enganche" => "required|numeric",
            "enganche_monto" => "required|numeric",

            "porcentaje_saldo" => "nullable|numeric",
            "saldo_monto" => "nullable|numeric",

            "monto_financiado" => "required|numeric",
            "financing_months" => "required|integer",
            "mensualidad" => "required|numeric",

            "descuento_porcentaje" => "nullable|numeric",
            "financiamiento_interes" => "nullable|numeric",

            // Plusvalía
            "annual_appreciation" => "nullable|numeric",

            // Imagen
            "chepina" => "nullable|string",

            // Lead
            "lead_name" => "nullable|string",
            "lead_phone" => "nullable|string",
            "lead_email" => "nullable|string",
            "city" => "nullable|string",

            // Contexto
            "desarrollo_id" => "nullable|integer",
            "desarrollo_name" => "nullable|string",
            "phase_id" => "nullable|integer",
            "stage_id" => "nullable|integer",
            "project_id" => "nullable|integer",
            "source_type" => "nullable|string",
            "roi" => "nullable|numeric",
            "valor_final" => "nullable|numeric",
            "plusvalia_total" => "nullable|numeric",
        ]);

        // Cálculos principales
        $enganchePorc = $data["porcentaje_enganche"];
        $engancheMonto = $data["enganche_monto"];

        $saldoMonto = $data["saldo_monto"] ?? 0;

        $meses = $data["financing_months"];
        $mensualidad = $data["mensualidad"];
        $montoFinanciado = $data["monto_financiado"];

        $precioReal = $data["precio_real"];
        $precioFinal = $data["precio_final"];
        $plusvaliaTotal = $data["plusvalia_total"] ?? 0;
        $roi = $data["roi"] ?? 0;
        $valorFinal = $data["valor_final"];
        $tipoAplicado = $data["tipo_aplicado"];

        $plusvaliaRate = $data["annual_appreciation"] ?? 0.15;
        $years = [];
        $totalAnios = (int) ceil($meses / 12);

        for ($year = 0; $year <= $totalAnios; $year++) {
            $valorProp = $precioReal * pow(1 + $plusvaliaRate, $year);

            $mesesPagados = min($meses, max(0, ($year * 12) - 1));

            $montoPagado = $engancheMonto + ($mensualidad * $mesesPagados);
            $plusvaliaAcum = $valorProp - $precioReal;
            $roiAnual = (($valorProp - $precioFinal) / $precioFinal) * 100;

            $years[] = [
                "year" => $year,
                "valorProp" => round($valorProp, 2),
                "montoPagado" => round($montoPagado, 2),
                "plusvaliaAcum" => round($plusvaliaAcum, 2),
                "roiAnual" => round($roiAnual, 2),
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

                "precio_total" => round($precioFinal, 2),   // precio final enviado por JS
                "tipo_aplicado" => $tipoAplicado,

                "enganche_porcentaje" => round($data["porcentaje_enganche"], 2),
                "enganche_monto" => round($data["enganche_monto"], 2),
                "saldo_monto" => round($data["saldo_monto"] ?? 0, 2),
                "monto_financiado" => round($data["monto_financiado"], 2),

                "financing_months" => $data["financing_months"],
                "mensualidad" => round($data["mensualidad"], 2),

                "annual_appreciation" => round($data["annual_appreciation"] ?? 0.15, 2),
                "plusvalia_total" => round($plusvaliaTotal, 2),
                "roi" => round($roi, 2),
                "valor_final_plusvalia" => round($valorFinal, 2),

                "chepina" => $data["chepina"] ?? null,
                "lead_name" => $data["lead_name"] ?? null,
                "lead_phone" => $data["lead_phone"] ?? null,
                "lead_email" => $data["lead_email"] ?? null,
                "city" => $data["city"] ?? null,

                "desarrollo_id" => $data["project_id"] ?? null,
                "desarrollo_name" => $data["desarrollo_name"] ?? null,
                "phase_id" => $data["phase_id"] ?? null,
                "stage_id" => $data["stage_id"] ?? null,
                "source_type" => $data["source_type"] ?? null,
                "years_data" => json_encode($years),

            ]);

        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        $desarrollo = $data['desarrollo_id'] ? Desarrollos::find($data['desarrollo_id']) : null;
        $desarrolloLogo = $desarrollo?->path_logo ?? null;

        $chepinaBase64 = $this->getChepinaBase64($data['chepina'] ?? null);

        $pdfData = array_merge($data, [

            "precio_real" => $precioReal,
            "enganchePorc" => $enganchePorc,
            "engancheMonto" => $engancheMonto,
            "meses" => $meses,
            "mensualidad" => $mensualidad,
            "plusvaliaRate" => $plusvaliaRate,
            "chepina_base64" => $chepinaBase64,
            "chepinaUrl" => $chepinaUrl,
            "desarrollo_id" => $data["desarrollo_id"] ?? null,
            "desarrollo_name" => $desarrolloName ?? null,
            "phase_id" => $data["phase_id"] ?? null,
            "stage_id" => $data["stage_id"] ?? null,
            "phase_name" => $phaseName,
            "stage_name" => $stageName,
            "desarrollo_logo" => $desarrolloLogo,
            "years" => $years,
            "precio_final" => $precioFinal,
            "precioTotal" => $precioFinal,
            "valorFinal" => $valorFinal,
            "roi" => $roi,
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
        return response()->streamDownload(fn() => print ($pdf), "cotizacion_" . $data["name"] . ".pdf");
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

            // PRECIOS BASE
            "price_square_meter" => $report->price_square_meter,
            "precio_real" => $report->precio_real,

            // PRECIO DE COMPRA
            "precio_final" => $report->precio_total,
            "precioTotal" => $report->precio_total,

            // VALOR FINAL CON PLUSVALÍA
            "valorFinal" => $report->valor_final_plusvalia,

            // FINANCIAMIENTO
            "enganchePorc" => $report->enganche_porcentaje,
            "engancheMonto" => $report->enganche_monto,
            "saldoMonto" => $report->saldo_monto ?? 0,
            "montoFinanciado" => $report->monto_financiado,
            "meses" => $report->financing_months,
            "mensualidad" => $report->mensualidad,

            // PLUSVALÍA / ROI
            "plusvaliaRate" => $report->annual_appreciation,
            "plusvaliaTotal" => $report->plusvalia_total,
            "roi" => $report->roi,

            // CHEPINA
            "chepinaUrl" => $report->chepina,
            "chepina_base64" => $chepinaBase64,

            // LEAD
            "lead_name" => $report->lead_name,
            "lead_phone" => $report->lead_phone,
            "lead_email" => $report->lead_email,
            "city" => $report->city,

            // PROYECCIÓN (CORREGIDO)
            "years" => is_string($report->years_data)
                ? json_decode($report->years_data, true)
                : null,
            // DESARROLLO
            "desarrollo_id" => $report->desarrollo_id,
            "desarrollo_name" => $desarrolloName,
            "phase_id" => $report->phase_id,
            "stage_id" => $report->stage_id,
            "phase_name" => $phaseName,
            "stage_name" => $stageName,
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