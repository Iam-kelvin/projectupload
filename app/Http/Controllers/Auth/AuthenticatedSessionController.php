<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request)
    {
        $this->rememberSafeRedirect($request);

        return view('auth.login');
    }

    public function store(Request $request)
    {
        $this->rememberSafeRedirect($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function rememberSafeRedirect(Request $request): void
    {
        $redirect = $request->input('redirect', $request->query('redirect'));

        if (! is_string($redirect) || $redirect === '') {
            return;
        }

        $host = $request->getSchemeAndHttpHost();

        if (Str::startsWith($redirect, $host) || Str::startsWith($redirect, '/')) {
            $request->session()->put('url.intended', $redirect);
        }
    }
}
