<?php

namespace App\Services;

use App\Models\User;

class AuthServices {
    public function existeEmail(string $email) {
        return User::where('email' , $email)->existe();
    }

    public function createNewUser(array $data) {
        return User::create($data);
    }
}
