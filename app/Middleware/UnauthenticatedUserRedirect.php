<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Auth;

class UnauthenticatedUserRedirect
{
    public function handle(?string $return_to)
    {
        if (Auth::check() === false) {
            return redirect($return_to);
        }
    }
}
