<?php

namespace App\Application\Auth\UseCases;

use Illuminate\Support\Facades\Password;

class RequestPasswordReset
{
    public function execute($email): void
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            // You *could* wrap this in a custom Domain/Application exception if you want
            throw new \RuntimeException(__($status));
        }
    }
}
