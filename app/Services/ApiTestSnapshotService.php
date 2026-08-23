<?php

namespace App\Services;

use App\Models\FoodListing;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ApiTestSnapshotService
{
    public function build(): array
    {
        return [
            'session_user' => Auth::user()?->only(['id', 'name', 'email', 'role']),
            'users' => $this->users(),
            'listings' => $this->listings(),
        ];
    }

    private function users()
    {
        return User::query()
            ->select('id', 'name', 'email', 'role', 'business_name', 'ngo_name', 'latitude', 'longitude', 'created_at')
            ->orderBy('id')
            ->get();
    }

    private function listings()
    {
        return FoodListing::query()
            ->with(['donor:id,name,email', 'claimedBy:id,name,email'])
            ->orderByDesc('id')
            ->get();
    }
}
