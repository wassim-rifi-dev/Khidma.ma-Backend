<?php

namespace App\Services;

use App\Models\professional;

class ProfessionalServices {
    public function create(array $data) {
        return professional::create($data);
    }

    public function getProfessionalInfo(int $user_id) {
        return professional::where('user_id' , $user_id)->first();
    }
}
