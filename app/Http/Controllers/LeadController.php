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
            $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'company'        => 'nullable|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:20',
            'event_type'     => 'nullable|string|max:255',
            'estimated_date' => 'nullable|date',
            'message'        => 'nullable|string',

            'phase_id'       => 'nullable|integer',
            'project_id'     => 'nullable|integer',
            'stage_id'       => 'nullable|integer',
            'lot_number'     => 'nullable|string|max:50',
            'lots' => 'nullable|string|max:255',
        ]);

        $lead = Lead::create($validated);

        $destinatarios = [
            "hi@davidsabido.com",
            "solicitudes@visityucatan.com"
        ];

        Mail::to($destinatarios)->send(
            new NuevaSolicitudMail($lead)
        );

        return response()->json([
            'success' => true,
            'message' => 'Solicitud enviada correctamente',
        ]);
    }
}
