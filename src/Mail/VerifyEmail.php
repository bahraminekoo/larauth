<?php

namespace Bahraminekoo\Larauth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $email;
    protected $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $password, $hash)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('emails.verification_code')
            ->with([
                'email' => $this->email,
                'password' => $this->password,
                'hash' => $hash,
            ]);
    }
}
