<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BrgyCIRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:        #C62828;
            --red-hover:  #EF5350;
            --red-glow:   rgba(198,40,40,0.25);
            --bg:         #111113;
            --surface:    #18181C;
            --surface-2:  #222228;
            --border:     rgba(255,255,255,0.07);
            --text:       #E8E8E8;
            --muted:      #6E6E73;
        }

        html, body { height: 100%; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Left panel ─────────────────────────────── */
        .left-panel {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 56px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            overflow: hidden;
        }

        /* Geometric background accent */
        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -120px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(198,40,40,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(198,40,40,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--red);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
            box-shadow: 0 4px 16px var(--red-glow);
        }

        .brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 1.2rem;
            color: var(--text);
            letter-spacing: 0.02em;
        }

        .left-content {
            position: relative;
            z-index: 1;
        }

        .left-content h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 2.8rem;
            line-height: 1.15;
            color: var(--text);
            margin-bottom: 16px;
        }

        .left-content h1 span {
            color: var(--red-hover);
        }

        .left-content p {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.7;
            max-width: 340px;
        }

        /* Decorative grid lines */
        .grid-lines {
            position: absolute;
            bottom: 60px;
            right: 40px;
            display: grid;
            grid-template-columns: repeat(6, 1px);
            gap: 18px;
            height: 120px;
            z-index: 0;
            opacity: 0.15;
        }

        .grid-lines span {
            display: block;
            width: 1px;
            height: 100%;
            background: var(--red-hover);
        }

        .left-footer {
            position: relative;
            z-index: 1;
            color: var(--muted);
            font-size: 0.78rem;
        }

        /* ── Right panel ─────────────────────────────── */
        .right-panel {
            width: 480px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 56px 48px;
            background: var(--bg);
        }

        .form-header {
            margin-bottom: 36px;
        }

        .form-header h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.8rem;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-header p {
            color: var(--muted);
            font-size: 0.87rem;
        }

        /* Form elements */
        .field-group {
            margin-bottom: 20px;
        }

        .field-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: #AEAEB2;
            letter-spacing: 0.03em;
            margin-bottom: 8px;
        }

        .field-wrap {
            position: relative;
        }

        .field-wrap input {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field-wrap input:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px var(--red-glow);
        }

        .field-wrap input::placeholder { color: var(--muted); }

        .field-wrap .eye-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .field-wrap .eye-toggle:hover { color: var(--text); }

        .field-wrap input.has-toggle { padding-right: 44px; }

        /* Error state */
        .field-wrap input.is-error { border-color: var(--red); }

        .error-list {
            background: rgba(198,40,40,0.1);
            border: 1px solid rgba(198,40,40,0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
        }

        .error-list ul {
            margin: 0;
            padding-left: 16px;
        }

        .error-list li {
            color: #EF9A9A;
            font-size: 0.83rem;
        }

        /* Row for remember + forgot */
        .form-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.83rem;
            color: var(--muted);
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--red);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.83rem;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: var(--red-hover); }

        /* Submit button */
        .btn-submit {
            width: 100%;
            background: var(--red);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.92rem;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.02em;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 16px var(--red-glow);
        }

        .btn-submit:hover {
            background: #B71C1C;
            transform: translateY(-1px);
            box-shadow: 0 6px 24px var(--red-glow);
        }

        .btn-submit:active { transform: translateY(0); }

        .form-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .form-footer a {
            color: var(--red-hover);
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover { color: #FF6B6B; }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider span {
            font-size: 0.78rem;
            color: var(--muted);
        }

        /* Fade-in animation */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .right-panel > * {
            animation: fadeUp 0.4s ease both;
        }

        .right-panel > *:nth-child(1) { animation-delay: 0.05s; }
        .right-panel > *:nth-child(2) { animation-delay: 0.1s; }
        .right-panel > *:nth-child(3) { animation-delay: 0.15s; }

        /* Responsive */
        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 40px 28px; }
        }
    </style>
</head>
<body>

    <!-- Left Panel -->
    <div class="left-panel">
        <div class="brand">
            <div class="brand-icon"><i class="fa fa-shield-alt"></i></div>
            <span class="brand-name">BrgyCIRS</span>
        </div>

        <div class="left-content">
            <h1>Your community,<br><span>reported & resolved.</span></h1>
            <p>BrgyCIRS is the Barangay Community Incident Reporting System — connecting residents with local officials to address concerns efficiently.</p>
        </div>

        <div class="grid-lines">
            <span></span><span></span><span></span>
            <span></span><span></span><span></span>
        </div>

        <div class="left-footer">
            &copy; {{ date('Y') }} BrgyCIRS. All rights reserved.
        </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
        <div class="form-header">
            <h2>Welcome back</h2>
            <p>Sign in to your BrgyCIRS account</p>
        </div>

        @if ($errors->any())
            <div class="error-list">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field-group">
                <label for="email">Email Address</label>
                <div class="field-wrap">
                    <input type="email" id="email" name="email"
                           placeholder="you@example.com"
                           value="{{ old('email') }}"
                           required autofocus>
                </div>
            </div>

            <div class="field-group">
                <label for="password">Password</label>
                <div class="field-wrap">
                    <input type="password" id="password" name="password"
                           class="has-toggle"
                           placeholder="••••••••"
                           required>
                    <button type="button" class="eye-toggle" onclick="togglePass('password', this)">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-meta">
                <label class="remember-label">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-submit">Sign In</button>
        </form>

        <div class="form-footer">
            Don't have an account? <a href="{{ route('register') }}">Create one</a>
        </div>
    </div>

    <script>
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            const icon  = btn.querySelector('i');
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            icon.className = isPass ? 'fa fa-eye-slash' : 'fa fa-eye';
        }
    </script>
</body>
</html>