<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;
use App\Mail\NuevaSolicitudMail;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
            'city'       => 'nullable|string|max:255',
            'phase_id'   => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'stage_id'   => 'nullable|integer',
            'lot_number' => 'nullable|string|max:50',
        ]);

        $lead = Lead::create($request->all());

        // Lista de correos destino
        $destinatarios = [
            "hi@davidsabido.com",
            "info@cicyucatan.com"
        ];

        Mail::to($destinatarios)->send(new NuevaSolicitudMail($lead));

        // Aquí podrías generar el PDF o redirigir
        return response()->json([
            'success' => true,
            'message' => 'Lead registrado correctamente',
            'lead' => $lead,
        ]);
    }
}
