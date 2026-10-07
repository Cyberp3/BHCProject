<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Personnel Portal Login</h2>
        <p class="text-xs text-slate-500 mt-0.5">Enter your health-center credentials to access authorized modules.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2 shadow-2xs">
            <svg class="w-4 h-4 text-rose-600 shrink-0 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Health Center Email</label>
            <input id="email" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" type="email" name="email" value="{{ old('email') }}" placeholder="name@tacunan.gov.ph" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
            <div class="relative">
                <input id="password"
                    class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 pr-10"
                    :type="showPassword ? 'text' : 'password'"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password" />
                <button type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                    tabindex="-1"
                    :title="showPassword ? 'Hide password' : 'Show password'"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'">
                    <!-- Eye Icon (Hidden) -->
                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye Slash Icon (Visible) -->
                    <svg x-show="showPassword" style="display: none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Show Password Checkbox -->
        <div class="flex items-center justify-between text-xs">
            <label for="show_password" class="inline-flex items-center cursor-pointer select-none">
                <input id="show_password"
                    type="checkbox"
                    x-model="showPassword"
                    class="rounded border-slate-300 text-emerald-600 shadow-2xs focus:ring-emerald-500">
                <span class="ms-2 text-slate-600 font-medium">Show password</span>
            </label>
        </div>

        <div>
            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow-md shadow-emerald-900/10 transition">
                Sign In to Health Center
            </button>
        </div>
    </form>
</x-guest-layout>
