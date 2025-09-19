<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VisaSolicitudMail extends Mailable
{
    use Queueable, SerializesModels;


    public $data;
    public $dataContacto;
    /**
     * Create a new message instance.
     */
    public function __construct($data, $dataContacto)
    {
        $this->data = $data;
        $this->dataContacto = $dataContacto;

    }
    //

    public function build()
    {
        return $this->subject('Nueva Solicitud de: ' . $this->data['nombre'])
            ->view('emails.templateVisa') // plantilla Blade
            ->with([
                'nombre'       => $this->data['nombre'],
                'origen'       => $this->data['origen'],
                'nacionalidad' => $this->data['nacionalidad'],
                'telefono'     => $this->data['telefono'],
                'contacto'     => $this->dataContacto['nombre'],
                'destino'      => $this->data['destino'],
                'email'        => $this->data['email'],
                'pasaporte'    => $this->data['pasaporte'],
                'salida'       => $this->data['salida'],
                'retorno'      => $this->data['retorno'],
                'residencia'   => $this->data['residencia'],
            ]);
    }
}
