<?php

namespace App\Services;

use App\Exceptions\AuthenticationException;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $guard;
    protected $model;

    public function __construct(string $guard, string $model)
    {
        $this->guard = $guard;
        $this->model = $model;
    }

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

    public function login(array $data): Model
    {
        if (!Auth::guard($this->guard)->validate($data)) {
            throw new AuthenticationException('The provided credentials are incorrect.');
        }

        $user = $this->model::where('email', $data['email'])->first();
        if (is_null($user->email_verified_at)) {
            throw new AuthenticationException('The provided credentials are incorrect.');
        }
        return $user;
    }

    public function resetPassword(Model $user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password)
        ]);
        $user->save();
    }
}
