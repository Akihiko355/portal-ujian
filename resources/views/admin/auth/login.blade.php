<x-layouts.auth title="Admin Login" subtitle="Masuk ke panel administrasi">
    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                   class="form-input" placeholder="admin@email.com">
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" required
                   class="form-input" placeholder="Masukkan password">
            @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-slate-300 text-slate-900 cursor-pointer">
            <label for="remember" class="text-sm text-slate-600 cursor-pointer">Ingat saya</label>
        </div>
        <button type="submit" class="btn-primary w-full py-3 mt-2">
            Masuk
        </button>
    </form>
</x-layouts.auth>
