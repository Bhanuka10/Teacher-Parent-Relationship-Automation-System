<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — School System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            background: #fff;
        }

        /* ── Left hero panel ── */
        .hero-panel {
            display: none;
            flex: 0 0 48%;
            background: linear-gradient(145deg, #e8440a 0%, #c03000 100%);
            position: relative;
            overflow: hidden;
            border-radius: 0 24px 24px 0;
        }

        @media (min-width: 768px) {
            .hero-panel { display: flex; align-items: flex-end; justify-content: center; }
        }

        /* Decorative arc lines */
        .hero-panel::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 720px;
            height: 720px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.12);
            box-shadow:
                0 0 0 60px rgba(255,255,255,0.06),
                0 0 0 120px rgba(255,255,255,0.04),
                0 0 0 180px rgba(255,255,255,0.03);
        }

        /* White circle platform at bottom */
        .hero-panel::after {
            content: '';
            position: absolute;
            bottom: -140px;
            left: 50%;
            transform: translateX(-50%);
            width: 380px;
            height: 380px;
            background: #fff;
            border-radius: 50%;
            opacity: 0.12;
        }

        .hero-image {
            position: relative;
            z-index: 2;
            width: 90%;
            max-width: 420px;
            object-fit: contain;
            object-position: bottom;
            margin-bottom: 0;
        }

        /* ── Right form panel ── */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .form-box {
            width: 100%;
            max-width: 420px;
        }

        /* Logo */
        .logo-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 36px;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: #e8440a;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg { width: 22px; height: 22px; fill: #fff; }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: -0.3px;
        }

        .logo-text span {
            color: #e8440a;
            font-weight: 400;
        }

        /* Headings */
        .form-title {
            font-size: 1.85rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .form-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 32px;
        }

        /* Form fields */
        .field-group {
            margin-bottom: 18px;
        }

        .field-label {
            display: block;
            font-size: 0.825rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            color: #1a1a2e;
            background: #fafafa;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .field-input::placeholder { color: #9ca3af; }

        .field-input:focus {
            border-color: #e8440a;
            box-shadow: 0 0 0 3px rgba(232, 68, 10, 0.1);
            background: #fff;
        }

        .field-input.error { border-color: #ef4444; }

        .field-error {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 4px;
        }

        /* Password wrapper */
        .password-wrap {
            position: relative;
        }

        .password-wrap .field-input {
            padding-right: 44px;
        }

        .pw-toggle {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            display: flex;
            align-items: center;
            padding: 0;
            transition: color 0.15s;
        }

        .pw-toggle:hover { color: #6b7280; }
        .pw-toggle svg { width: 18px; height: 18px; }

        /* Remember / forgot row */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.825rem;
            color: #4b5563;
            cursor: pointer;
        }

        .remember-checkbox {
            width: 16px;
            height: 16px;
            border: 1.5px solid #d1d5db;
            border-radius: 4px;
            accent-color: #e8440a;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.825rem;
            font-weight: 600;
            color: #e8440a;
            text-decoration: none;
            transition: color 0.15s;
        }

        .forgot-link:hover { color: #c03000; text-decoration: underline; }

        /* Buttons */
        .btn-primary {
            width: 100%;
            background: #e8440a;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 0.925rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            letter-spacing: 0.2px;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(232, 68, 10, 0.35);
        }

        .btn-primary:hover {
            background: #c03000;
            box-shadow: 0 6px 20px rgba(232, 68, 10, 0.4);
            transform: translateY(-1px);
        }

        .btn-primary:active { transform: translateY(0); }

        .btn-google {
            width: 100%;
            background: #fff;
            color: #374151;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            margin-bottom: 24px;
        }

        .btn-google:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        /* Alert */
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.875rem;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.875rem;
            margin-bottom: 20px;
        }

        /* Footer note */
        .signup-note {
            text-align: center;
            font-size: 0.825rem;
            color: #6b7280;
        }

        .signup-note a {
            color: #e8440a;
            font-weight: 600;
            text-decoration: none;
        }

        .signup-note a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    {{-- Left hero panel --}}
    <div class="hero-panel">
        <img src="{{ asset('login-hero.png') }}" alt="Student reading" class="hero-image">
    </div>

    {{-- Right form panel --}}
    <div class="form-panel">
        <div class="form-box">

            {{-- Logo --}}
            <div class="logo-row">
                <div class="logo-icon">
                    {{-- Graduation cap icon --}}
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18V17l7 4 7-4v-3.82L12 17l-7-3.82z"/>
                    </svg>
                </div>
                <span class="logo-text">School <span>System</span></span>
            </div>

            {{-- Heading --}}
            <h1 class="form-title">Login</h1>
            <p class="form-subtitle">Enter your credentials to login to your account</p>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="field-group">
                    <label class="field-label" for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autofocus
                        placeholder="example.educationpro@gmail.com"
                        class="field-input @error('email') error @enderror"
                        autocomplete="email">
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <label class="field-label" for="password">Password</label>
                    <div class="password-wrap">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="••••••••••••••••"
                            class="field-input @error('password') error @enderror"
                            autocomplete="current-password">
                        <button type="button" class="pw-toggle" id="pw-toggle-btn" aria-label="Toggle password visibility">
                            <svg id="pw-eye-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/>
                                <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                            </svg>
                            <svg id="pw-eye-hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 7.5 9.75 7.5c1.57 0 3.03-.325 4.354-.88M6.53 6.53C8.264 5.49 10.164 4.5 12 4.5c6 0 9.75 7.5 9.75 7.5a19.67 19.67 0 0 1-3.023 4.08M9.88 9.88a3 3 0 0 0 4.24 4.24"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me + Forgot password --}}
                <div class="meta-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" class="remember-checkbox" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                {{-- Sign In --}}
                <button type="submit" class="btn-primary">Sign In</button>

            </form>

            {{-- Sign in with Google (UI only) --}}
            <button type="button" class="btn-google">
                <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/>
                    <path d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/>
                    <path d="M3.964 10.707A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.707V4.961H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.039l3.007-2.332z" fill="#FBBC05"/>
                    <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.961L3.964 7.293C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
                </svg>
                Sign in with google
            </button>

            {{-- Sign up note --}}
            <p class="signup-note">Don't have an account? <a href="#">Sign Up</a></p>

        </div>
    </div>

    <script>
        // Password toggle
        const btn = document.getElementById('pw-toggle-btn');
        const pwInput = document.getElementById('password');
        const eyeShow = document.getElementById('pw-eye-show');
        const eyeHide = document.getElementById('pw-eye-hide');

        if (btn) {
            btn.addEventListener('click', function () {
                const isPassword = pwInput.type === 'password';
                pwInput.type = isPassword ? 'text' : 'password';
                eyeShow.style.display = isPassword ? 'none' : 'block';
                eyeHide.style.display = isPassword ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>