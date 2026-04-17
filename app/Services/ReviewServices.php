<?php

namespace App\Services;

use App\Models\Reviews;

class ReviewServices
{
    public function createReview(array $data)
    {
        return Reviews::create($data);
    }
}
