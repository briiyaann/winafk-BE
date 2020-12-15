<?php

namespace App\Mail;

use App\Models\Core\PasswordReset;
use App\Models\Core\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $password_reset;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param PasswordReset $password_reset
     */
    public function __construct(
        User $user,
        PasswordReset $password_reset
    ){
        $this->user = $user;
        $this->password_reset = $password_reset;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@winafk.com')
            ->subject('Forgot Password')
            ->markdown('emails.user.forgot-password');
    }
}
