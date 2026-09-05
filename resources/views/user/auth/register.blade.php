<x-layouts.auth title="Daftar" subtitle="Buat akun baru">
    <form method="POST" action="{{ route('user.register.submit') }}" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="form-input" placeholder="Masukkan nama lengkap">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="form-input" placeholder="email@email.com">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="form-label">Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                           class="form-input" placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <div>
                    <label for="department_id" class="form-label">Departemen</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="institution_address" class="form-label">Alamat Institusi</label>
                    <input type="text" name="institution_address" id="institution_address" value="{{ old('institution_address') }}"
                           class="form-input" placeholder="Masukkan alamat">
                    @error('institution_address')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" required
                           class="form-input" placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Konfirmasi Password (full width) -->
        <div>
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                   class="form-input" placeholder="Ulangi password">
        </div>

        <button type="submit" class="btn-primary w-full py-3">
            Daftar Sekarang
        </button>
    </form>
    <div class="mt-5 pt-4 border-t border-slate-100 text-center">
        <p class="text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('user.login') }}" class="text-slate-900 font-semibold hover:underline">Login</a>
        </p>
    </div>
</x-layouts.auth>
