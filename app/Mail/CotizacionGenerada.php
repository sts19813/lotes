<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CotizacionGenerada extends Mailable
{
    use Queueable, SerializesModels;

    public $lot;
    public $pdf;
    /**
     * Create a new message instance.
     */
     public function __construct($lot, $pdf)
    {
        $this->lot = $lot;
        $this->pdf = $pdf;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cotizacion Generada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Tu cotización - ' . $this->lot->name)
                    ->markdown('emails.cotizacion');
    }
}
