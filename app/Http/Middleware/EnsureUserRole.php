<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Enums\ShelterStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Panel route gruplarını rol bazlı korur.
 * admin için ek olarak barınağın onaylı olmasını şart koşar.
 */
class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        abort_if($user === null, 403);
        abort_if($user->is_banned, 403, 'Hesabınız askıya alınmış.');
        abort_if($user->role->value !== $role, 403);

        if ($user->role === Role::Admin) {
            abort_unless(
                $user->shelter && $user->shelter->status === ShelterStatus::Approved,
                403,
                'Barınağınız henüz onaylanmadı.'
            );
        }

        return $next($request);
    }
}
