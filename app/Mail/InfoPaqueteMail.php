<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InfoPaqueteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $dataOferta;


    public function __construct($data, $dataOferta)
    {
        $this->data = $data;
        $this->dataOferta = $dataOferta;
    }

    public function build()
    {
        return $this->subject('Informes Paquete: ' . $this->dataOferta->titulo)
            ->view('emails.info_paquete')
            ->with([
                'agencia' => $this->data['agencia'],
                'titulo' => $this->dataOferta['titulo'],
                'tipo' => $this->dataOferta['tipo'],
                'mensaje' => $this->data['message'],
                'nombre' => $this->data['nombre'],
                'email' => $this->data['email'],
            ]);
    }
}
