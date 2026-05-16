<?php

declare(strict_types=1);

namespace App\Scopes\Shelter;

use App\Enums\Account\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Tenant izolasyonu: admin oturumunda sorgular kendi barınağına filtrelenir.
 * superadmin, user ve misafir için koşul eklenmez.
 */
class ShelterScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if ($user && $user->role === Role::Admin && $user->shelter) {
            $builder->where($model->getTable().'.shelter_id', $user->shelter->id);
        }
    }
}
