<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPostEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $data)
    {
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Selamat Atas Feed Baru Anda!',
        );
    }

    public function content()
    {
        return new Content(
            view: 'new-post-email',
            with: ['title' => $this->data['title'], 'body' => $this->data['body']]
        );
    }
}
