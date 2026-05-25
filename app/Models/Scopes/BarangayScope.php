<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BarangayScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check()) {
            return;
        }

        $role = strtolower(Auth::user()->role_relation->name ?? 'resident');

        // Admins see all reports across all barangays
        if ($role === 'admin') {
            return;
        }

        // Residents are scoped to their own barangay
        if (Auth::user()->barangay_id) {
            $builder->where('barangay_id', Auth::user()->barangay_id);
        }
    }
}