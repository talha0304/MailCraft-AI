<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserVerficationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct(int $otp, string $subject)
    {
        $this->otp = $otp;
        $this->subject = $subject;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject, // Use the subject passed to the constructor
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.EmailVerfication', // Blade view for the email content
            with: [
                'otp' => $this->otp, // Pass the OTP to the view
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
