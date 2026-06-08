<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background-color: #eef0f5;
        }

        .input-field {
            transition: all 0.2s ease;
            background-color: #f9fafb;
        }

        .input-field:focus {
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .input-field.is-invalid {
            border-color: #ef4444;
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
            width: 0%;
        }

        .password-strength.weak {
            background-color: #ef4444;
            width: 33%;
        }

        .password-strength.medium {
            background-color: #f59e0b;
            width: 66%;
        }

        .password-strength.strong {
            background-color: #10b981;
            width: 100%;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 15px;
            font-weight: 600;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: #9ca3af;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #e5e7eb;
        }

        .form-label {
            display: block;
            font-size: 13.5px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            outline: none;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .form-input:focus {
            border-color: #3b82f6;
            ring: 1px #3b82f6;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .error-msg {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }

        a.link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        a.link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 32px 16px;">
        <div style="width: 100%; max-width: 420px;">

            <!-- Card -->
            <div class="card" style="padding: 36px 32px;">

                <!-- Logo -->
                <div style="text-align: center; margin-bottom: 28px;">
                    <div style="font-size: 26px; font-weight: 700; color: #2563eb; letter-spacing: -0.5px;">
                        {{ config('app.name') }}
                    </div>
                    <p style="color: #6b7280; font-size: 14px; margin-top: 6px;">Buat akun baru Anda</p>
                </div>

                {{-- Error global --}}
                @if ($errors->has('general'))
                    <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:8px; padding:12px 14px; margin-bottom:16px; font-size:13px;">
                        {{ $errors->first('general') }}
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Nama lengkap Anda" required
                            class="input-field form-input @error('name') is-invalid @enderror">
                        @error('name')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Toko -->
                    <div class="form-group">
                        <label for="store_name" class="form-label">Nama Toko</label>
                        <input type="text" id="store_name" name="store_name" value="{{ old('store_name') }}"
                            placeholder="Nama toko Anda" required
                            class="input-field form-input @error('store_name') is-invalid @enderror">
                        @error('store_name')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="nama@contoh.com" required
                            class="input-field form-input @error('email') is-invalid @enderror">
                        @error('email')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="form-group">
                        <label for="phone" class="form-label">
                            Nomor Telepon
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                            placeholder="+62 812-XXXX-XXXX"
                            class="input-field form-input" required>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="passwordInput" class="form-label">Password</label>
                        <input type="password" id="passwordInput" name="password"
                            placeholder="Minimal 8 karakter" required
                            class="input-field form-input @error('password') is-invalid @enderror">
                        <!-- Strength bar -->
                        <div style="margin-top:8px; background:#f3f4f6; border-radius:4px; overflow:hidden; height:4px;">
                            <div class="password-strength" id="strengthBar"></div>
                        </div>
                        <p id="strengthLabel" style="font-size:12px; color:#9ca3af; margin-top:4px;">
                            Gunakan kombinasi huruf besar, kecil, dan angka
                        </p>
                        @error('password')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Ketik ulang password Anda" required
                            class="input-field form-input">
                    </div>

                    <!-- Terms -->
                    <div style="display:flex; align-items:flex-start; gap:8px; margin-bottom:16px; margin-top:4px;">
                        <input type="checkbox" name="terms" id="terms" required
                            style="width:16px; height:16px; margin-top:2px; accent-color:#2563eb; flex-shrink:0;">
                        <label for="terms" style="font-size:13px; color:#374151; cursor:pointer; line-height:1.5;">
                            Saya setuju dengan
                            <a href="#" class="link">Syarat &amp; Ketentuan</a>
                            dan
                            <a href="#" class="link">Kebijakan Privasi</a>
                        </label>
                    </div>

                    <!-- Trial info -->
                    <div style="background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; padding:10px 14px; font-size:12.5px; color:#1d4ed8; margin-bottom:16px;">
                        ✓ Gratis 3 hari trial, tidak perlu kartu kredit. Bisa upgrade kapan saja.
                    </div>

                    <!-- Submit -->
                    <button type="submit" id="submitBtn" class="btn-primary">
                        Daftar Sekarang
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider">atau</div>

                <!-- Login Link -->
                <div style="text-align:center;">
                    <p style="font-size:14px; color:#6b7280;">
                        Sudah punya akun?
                        <a href="/admin/login" class="link">Masuk di sini</a>
                    </p>
                </div>

                <!-- Terms note (like login page) -->
                <div style="text-align:center; margin-top:20px; padding-top:20px; border-top:1px solid #f3f4f6;">
                    <p style="font-size:12px; color:#9ca3af;">
                        Dengan mendaftar, Anda menyetujui
                        <a href="#" class="link" style="font-size:12px;">Syarat &amp; Ketentuan</a>
                    </p>
                </div>

            </div>

            <!-- Back to Home -->
            <div style="text-align:center; margin-top:20px;">
                <a href="/" class="link" style="font-size:14px;">← Kembali ke Beranda</a>
            </div>

        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('passwordInput');
        const strengthBar = document.getElementById('strengthBar');
        const strengthLabel = document.getElementById('strengthLabel');

        const labels = {
            '': 'Gunakan kombinasi huruf besar, kecil, dan angka',
            weak: 'Keamanan password: Lemah',
            medium: 'Keamanan password: Sedang',
            strong: 'Keamanan password: Kuat ✓',
        };

        const labelColors = {
            weak: '#ef4444',
            medium: '#f59e0b',
            strong: '#10b981',
        };

        passwordInput.addEventListener('input', function () {
            const val = this.value;
            let level = '';

            if (val.length > 0) {
                const hasLower = /[a-z]/.test(val);
                const hasUpper = /[A-Z]/.test(val);
                const hasDigit = /\d/.test(val);
                const longEnough = val.length >= 8;

                if (longEnough && hasLower && hasUpper && hasDigit) {
                    level = 'strong';
                } else if (val.length >= 6 && hasLower && (hasUpper || hasDigit)) {
                    level = 'medium';
                } else {
                    level = 'weak';
                }
            }

            strengthBar.className = 'password-strength' + (level ? ' ' + level : '');
            strengthLabel.textContent = labels[level] ?? labels[''];
            strengthLabel.style.color = labelColors[level] ?? '#9ca3af';
        });

        document.querySelector('form').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
        });
    </script>
</body>

</html>
