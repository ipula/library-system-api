<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\DTO\ResetPasswordDTO;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPassword
{

    public function execute(ResetPasswordDTO $input): void
    {
        $status = Password::reset(
            [
                'email'=> $input->email,
                'password'=> $input->password,
                'password_confirmation'=> $input->passwordConfirmation,
                'token' => $input->token,
            ],
            function ($user) use ($input) {
                $user->password = Hash::make($input->password);
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \RuntimeException(__($status));
        }
    }
}
