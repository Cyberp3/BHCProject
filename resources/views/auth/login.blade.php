<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Personnel Portal Login</h2>
        <p class="text-xs text-slate-500 mt-0.5">Enter your health-center credentials to access authorized modules.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Health Center Email</label>
            <input id="email" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" type="email" name="email" value="{{ old('email', 'admin@tacunan.gov.ph') }}" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
            <input id="password" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" type="password" name="password" value="password123" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-600 shadow-2xs focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-slate-600">Remember session</span>
            </label>
        </div>

        <div>
            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow-md shadow-emerald-900/10 transition">
                Sign In to Health Center
            </button>
        </div>
    </form>

    <!-- Quick Demo Accounts Helper -->
    <div class="mt-6 pt-5 border-t border-slate-100">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">School Project Demo Accounts (Password: password123)</span>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 text-[11px]">
            <button type="button" onclick="document.getElementById('email').value='admin@tacunan.gov.ph'; document.getElementById('password').value='password123';" class="p-1.5 rounded bg-slate-50 hover:bg-slate-100 text-slate-700 text-left border border-slate-200 transition">
                <strong class="block text-slate-900">Admin</strong>
                admin@tacunan...
            </button>
            <button type="button" onclick="document.getElementById('email').value='nurse@tacunan.gov.ph'; document.getElementById('password').value='password123';" class="p-1.5 rounded bg-slate-50 hover:bg-slate-100 text-slate-700 text-left border border-slate-200 transition">
                <strong class="block text-slate-900">Nurse</strong>
                nurse@tacunan...
            </button>
            <button type="button" onclick="document.getElementById('email').value='midwife@tacunan.gov.ph'; document.getElementById('password').value='password123';" class="p-1.5 rounded bg-slate-50 hover:bg-slate-100 text-slate-700 text-left border border-slate-200 transition">
                <strong class="block text-slate-900">Midwife</strong>
                midwife@tacunan...
            </button>
            <button type="button" onclick="document.getElementById('email').value='bns@tacunan.gov.ph'; document.getElementById('password').value='password123';" class="p-1.5 rounded bg-slate-50 hover:bg-slate-100 text-slate-700 text-left border border-slate-200 transition">
                <strong class="block text-slate-900">BNS</strong>
                bns@tacunan...
            </button>
            <button type="button" onclick="document.getElementById('email').value='bhw@tacunan.gov.ph'; document.getElementById('password').value='password123';" class="p-1.5 rounded bg-slate-50 hover:bg-slate-100 text-slate-700 text-left border border-slate-200 transition">
                <strong class="block text-slate-900">BHW</strong>
                bhw@tacunan...
            </button>
            <button type="button" onclick="document.getElementById('email').value='bhv@tacunan.gov.ph'; document.getElementById('password').value='password123';" class="p-1.5 rounded bg-slate-50 hover:bg-slate-100 text-slate-700 text-left border border-slate-200 transition">
                <strong class="block text-slate-900">BHV</strong>
                bhv@tacunan...
            </button>
        </div>
    </div>
</x-guest-layout>
