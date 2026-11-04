<?php

namespace App\Http\Library;

use Illuminate\Http\JsonResponse;
use App\Models\User;

trait ApiHelpers
{
    protected function isAdmin(?User $user): bool
    {
        if (!empty($user)) {
            return ($user->role === "admin") ? true : false;
        }

        return false;
    }
}