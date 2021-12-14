<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    public $asunto;
    public $datos;
    public $titulo;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($asunto, $datos, $titulo)
    {
        $this->asunto = $asunto;
        $this->titulo = $titulo;
        $this->datos = $datos;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->asunto)->view("notificacion");
    }
}
