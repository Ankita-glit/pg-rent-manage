@extends('layouts.app')

@section('title', 'Login - Nice Stay Girls PG Management')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 glass-panel p-8 rounded-3xl border border-slate-800 shadow-2xl relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-pink-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center relative">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-pink-600 via-rose-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-pink-500/30 mb-4">
                <i data-lucide="heart-handshake" class="w-8 h-8"></i>
            </div>
            <h2 class="font-heading text-3xl font-extrabold text-white">Nice Stay Girls PG</h2>
            <p class="mt-2 text-sm text-slate-400">Sign in to manage room rent, payments & delay requests</p>
        </div>

        <!-- Demo Account Quick Fill Buttons -->
        <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700/60 space-y-2">
            <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider text-center">⚡ Quick Demo Login Shortcuts</p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" class="px-3 py-2 rounded-xl bg-pink-600/20 hover:bg-pink-600/30 border border-pink-500/40 text-pink-300 text-xs font-bold transition-colors text-center">
                    👑 Owner/Admin Login
                </button>
                <button type="button" class="px-3 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/40 text-emerald-300 text-xs font-bold transition-colors text-center">
                    🏠 Resident Login
                </button>
            </div>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase text-slate-400 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input id="email" name="email" type="email"
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm transition-all"
                            placeholder="name@example.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase text-slate-400 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input id="password" name="password" type="password"
                            class="w-full pl-10 pr-12 py-3 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm transition-all"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePasswordVisibility('password', 'eyeIcon')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white" title="Toggle password visibility">
                            <i id="eyeIcon" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded bg-slate-900 border-slate-700 text-pink-600 focus:ring-pink-500">
                    <label for="remember" class="ml-2 block text-xs text-slate-400">Remember me</label>
                </div>
            </div>

            <div>
                <button type="submit" id="btn-login-submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-pink-600 via-rose-600 to-indigo-600 hover:from-pink-500 hover:to-indigo-500 text-white font-bold text-sm shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 transition-all flex items-center justify-center space-x-2">
                    <span>Sign In to Portal</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }

    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>
@endsection
