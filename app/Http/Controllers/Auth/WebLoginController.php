<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class WebLoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if ($user->role === UserRole::Customer) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json(['role' => UserRole::Customer->value]);
            }

            return redirect()->intended(route('customer.restaurants'));
        }

        if (! $request->expectsJson()) {
            return redirect()->route('customer.restaurants', ['login' => 1])
                ->withErrors(['email' => 'Please sign in using the DashBite sign-in dialog.']);
        }

        $token = $user->createToken('auth')->plainTextToken;
        $request->session()->regenerate();

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'account_status' => $user->account_status->value,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.restaurants');
    }
}
