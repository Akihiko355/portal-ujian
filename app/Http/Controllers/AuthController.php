<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\FailedLoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function isAdmin(): bool
    {
        return request()->is('admin/*');
    }

    public function showLogin()
    {
        $view = $this->isAdmin() ? 'admin.auth.login' : 'user.auth.login';
        return view($view);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $guard = $this->isAdmin() ? 'admin' : 'web';
        $model = $this->isAdmin() ? \App\Models\Admin::class : \App\Models\User::class;

        // Cek apakah email ada di database
        $emailExists = $model::where('email', $credentials['email'])->exists();

        if (!$emailExists) {
            FailedLoginAttempt::create([
                'email' => $credentials['email'],
                'ip_address' => $request->ip(),
                'attempted_at' => now(),
                'guard_type' => $this->isAdmin() ? 'admin_failed' : 'web_failed',
            ]);
            return back()->withErrors(['email' => 'Email tidak ditemukan.'])->onlyInput('email');
        }

        // Regenerate session BEFORE attempt to ensure guard data persists in new session
        if ($this->isAdmin()) {
            $request->session()->regenerate();
        }

        if (!Auth::guard($guard)->attempt($credentials, $request->boolean('remember'))) {
            FailedLoginAttempt::create([
                'email' => $credentials['email'],
                'ip_address' => $request->ip(),
                'attempted_at' => now(),
                'guard_type' => $this->isAdmin() ? 'admin_failed' : 'web_failed',
            ]);
            return back()->withErrors(['password' => 'Password salah.'])->onlyInput('email');
        }

        $user = Auth::guard($guard)->user();

        if (!$user->is_active) {
            Auth::guard($guard)->logout();
            return back()->withErrors(['email' => 'Akun tidak aktif.']);
        }

        if ($this->isAdmin()) {
            $user->update(['last_login_at' => now()]);
            ActivityLog::create([
                'admin_id' => $user->id,
                'guard_type' => 'admin',
                'action' => 'login',
                'model_label' => "Admin: {$user->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        // Session already regenerated before admin attempt above
        return redirect()->intended(route($this->isAdmin() ? 'admin.dashboard' : 'user.dashboard'));
    }

    public function showRegister()
    {
        $departments = Department::all();
        return view('user.auth.register', compact('departments'));
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $user = User::create($validated);
        Auth::guard('web')->login($user);

        return redirect()->route('user.dashboard');
    }

    public function logout(Request $request)
    {
        $guard = $this->isAdmin() ? 'admin' : 'web';
        $user = Auth::guard($guard)->user();

        if ($user && $this->isAdmin()) {
            ActivityLog::create([
                'admin_id' => $user->id,
                'guard_type' => 'admin',
                'action' => 'logout',
                'model_label' => "Admin: {$user->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        Auth::guard($guard)->logout();

        return redirect()->route($this->isAdmin() ? 'admin.login' : 'user.login');
    }
}
