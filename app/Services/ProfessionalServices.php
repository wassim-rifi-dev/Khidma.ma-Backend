<?php

namespace App\Services;

use App\Models\professional;

class ProfessionalServices {
    public function create(array $data) {
        return professional::create($data);
    }
}
