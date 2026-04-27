<?php

namespace App\Services\Auth;

use App\Models\User;

class AuthService {
    public function existeEmail(string $email) {
        return User::where('email' , $email)->first();
    }

    public function createNewUser(array $data) {
        return User::create($data);
    }
}
