<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $content) {
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject($this->content['subject'])
            ->view('emails.forgot_password_otp');
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        // envelope() is applied after build() and wins over ->subject(), so
        // the subject must come from here: it used to be the hard-coded
        // "Forgot Password OPT" (typo), which hid the subject passed in
        // $content['subject'] by AuthController.
        return new Envelope(
            subject: $this->content['subject'] ?? 'Password Reset OTP',
        );
    }

    // /**
    //  * Get the message content definition.
    //  *
    //  * @return \Illuminate\Mail\Mailables\Content
    //  */
    // public function content()
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

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
