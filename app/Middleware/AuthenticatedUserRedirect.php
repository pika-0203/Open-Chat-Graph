<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Auth;

class AuthenticatedUserRedirect
{
    public function handle(?string $return_to)
    {
        if (Auth::check()) {
            return redirect($return_to);
        }
    }
}
