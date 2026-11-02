<?php

namespace App\Mail;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PengingatDeadlineMail extends Mailable
{
    public function __construct(public User $user, public Notifikasi $notif)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: '🍓 '.$this->notif->judul);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pengingat');
    }
}
