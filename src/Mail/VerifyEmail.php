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
    protected $hash;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $password, $hash)
    {
        $this->email = $email;
        $this->password = $password;
        $this->hash = $hash;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('vendor.larauth.emails.verification_code')
            ->with([
                'email' => $this->email,
                'password' => $this->password,
                'hash' => $this->hash,
            ]);
    }
}
