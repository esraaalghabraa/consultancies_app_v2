<?php

namespace App\Services;

use App\Exceptions\AuthenticationException;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        if ($data['is_expert']){
            Expert::create([
                'user_id'=>$user->id
            ]);
        }
        return $user;
    }

    public function login(array $data)
    {
        if (!auth()->validate($data)) {
            throw new AuthenticationException('The provided credentials are incorrect.');
        }

        $user = User::where('email', $data['email'])->first();
        if (is_null($user->email_verified_at)) {
            throw new AuthenticationException('The provided credentials are incorrect.');
        }
        return $user;
    }

    public function resetPassword($user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password)
        ]);
        $user->save();
    }
}
