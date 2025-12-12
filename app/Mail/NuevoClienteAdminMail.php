<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevoClienteAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $cliente;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Cliente $cliente)
    {
        $this->user = $user;
        $this->cliente = $cliente;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Nuevo Cliente Registrado - ' . $this->user->name,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.nuevo-cliente-admin',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
