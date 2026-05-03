<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | BrgyCIRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:       #C62828;
            --red-hover: #EF5350;
            --red-glow:  rgba(198,40,40,0.25);
            --bg:        #111113;
            --surface:   #18181C;
            --border:    rgba(255,255,255,0.07);
            --text:      #E8E8E8;
            --muted:     #6E6E73;
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

        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(198,40,40,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 300px; height: 300px;
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
            width: 40px; height: 40px;
            background: var(--red);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1rem;
            box-shadow: 0 4px 16px var(--red-glow);
        }

        .brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 1.2rem;
            color: var(--text);
        }

        .left-content { position: relative; z-index: 1; }

        .left-content h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 2.8rem;
            line-height: 1.15;
            color: var(--text);
            margin-bottom: 16px;
        }

        .left-content h1 span { color: var(--red-hover); }

        .left-content p {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.7;
            max-width: 340px;
            margin-bottom: 32px;
        }

        /* Password tips */
        .password-tips { display: flex; flex-direction: column; gap: 12px; }

        .tip-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .tip-item i {
            width: 20px;
            color: var(--red-hover);
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .grid-lines {
            position: absolute;
            bottom: 60px; right: 40px;
            display: grid;
            grid-template-columns: repeat(6, 1px);
            gap: 18px;
            height: 120px;
            z-index: 0;
            opacity: 0.15;
        }

        .grid-lines span { display: block; width: 1px; height: 100%; background: var(--red-hover); }

        .left-footer {
            position: relative; z-index: 1;
            color: var(--muted); font-size: 0.78rem;
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

        .form-header { margin-bottom: 32px; }

        .form-header .icon-wrap {
            width: 52px; height: 52px;
            background: rgba(198,40,40,0.12);
            border: 1px solid rgba(198,40,40,0.25);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }

        .form-header .icon-wrap i { color: var(--red-hover); font-size: 1.2rem; }

        .form-header h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.8rem;
            color: var(--text);
            margin-bottom: 8px;
        }

        .form-header p { color: var(--muted); font-size: 0.87rem; line-height: 1.6; }

        /* Error */
        .error-list {
            background: rgba(198,40,40,0.1);
            border: 1px solid rgba(198,40,40,0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
        }

        .error-list ul { margin: 0; padding-left: 16px; }
        .error-list li { color: #EF9A9A; font-size: 0.83rem; }

        .field-group { margin-bottom: 20px; }

        .field-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: #AEAEB2;
            letter-spacing: 0.03em;
            margin-bottom: 8px;
        }

        .field-wrap { position: relative; }

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
        .field-wrap input.has-toggle { padding-right: 44px; }

        .eye-toggle {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            cursor: pointer;
            background: none; border: none;
            padding: 0; font-size: 0.9rem;
            transition: color 0.2s;
        }

        .eye-toggle:hover { color: var(--text); }

        /* Password strength bar */
        .strength-bar {
            margin-top: 8px;
            height: 3px;
            border-radius: 3px;
            background: rgba(255,255,255,0.08);
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            border-radius: 3px;
            width: 0%;
            transition: width 0.3s ease, background 0.3s ease;
        }

        .strength-label {
            font-size: 0.75rem;
            margin-top: 5px;
            color: var(--muted);
        }

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
            margin-top: 8px;
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

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .right-panel > * { animation: fadeUp 0.4s ease both; }
        .right-panel > *:nth-child(1) { animation-delay: 0.05s; }
        .right-panel > *:nth-child(2) { animation-delay: 0.1s; }
        .right-panel > *:nth-child(3) { animation-delay: 0.15s; }

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
            <h1>Choose a <br><span>new password.</span></h1>
            <p>Make sure your new password is strong and something you haven't used before.</p>

            <div class="password-tips">
                <div class="tip-item">
                    <i class="fa fa-check-circle"></i>
                    At least 8 characters long
                </div>
                <div class="tip-item">
                    <i class="fa fa-check-circle"></i>
                    Mix of uppercase and lowercase letters
                </div>
                <div class="tip-item">
                    <i class="fa fa-check-circle"></i>
                    Include numbers or symbols
                </div>
                <div class="tip-item">
                    <i class="fa fa-check-circle"></i>
                    Don't reuse a previous password
                </div>
            </div>
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
            <div class="icon-wrap">
                <i class="fa fa-key"></i>
            </div>
            <h2>Set new password</h2>
            <p>Enter a new password for your account. Make it strong and memorable.</p>
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

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="field-group">
                <label for="email">Email Address</label>
                <div class="field-wrap">
                    <input type="email" id="email" name="email"
                           placeholder="you@example.com"
                           value="{{ old('email', $request->email) }}"
                           required autofocus autocomplete="username">
                </div>
            </div>

            <div class="field-group">
                <label for="password">New Password</label>
                <div class="field-wrap">
                    <input type="password" id="password" name="password"
                           class="has-toggle"
                           placeholder="Min. 8 characters"
                           required autocomplete="new-password"
                           oninput="checkStrength(this.value)">
                    <button type="button" class="eye-toggle" onclick="togglePass('password', this)">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                <div class="strength-label" id="strengthLabel"></div>
            </div>

            <div class="field-group">
                <label for="password_confirmation">Confirm New Password</label>
                <div class="field-wrap">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="has-toggle"
                           placeholder="Repeat your password"
                           required autocomplete="new-password">
                    <button type="button" class="eye-toggle" onclick="togglePass('password_confirmation', this)">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa fa-check" style="margin-right:8px;"></i> Reset Password
            </button>
        </form>

        <div class="form-footer">
            Remembered it? <a href="{{ route('login') }}">Back to Sign In</a>
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

        function checkStrength(val) {
            const fill  = document.getElementById('strengthFill');
            const label = document.getElementById('strengthLabel');

            let score = 0;
            if (val.length >= 8)             score++;
            if (/[A-Z]/.test(val))           score++;
            if (/[0-9]/.test(val))           score++;
            if (/[^A-Za-z0-9]/.test(val))   score++;

            const levels = [
                { pct: '0%',   color: 'transparent',          text: '' },
                { pct: '25%',  color: '#C62828',               text: 'Weak' },
                { pct: '50%',  color: '#F57F17',               text: 'Fair' },
                { pct: '75%',  color: '#FBC02D',               text: 'Good' },
                { pct: '100%', color: '#2E7D32',               text: 'Strong' },
            ];

            fill.style.width      = levels[score].pct;
            fill.style.background = levels[score].color;
            label.textContent     = val.length ? levels[score].text : '';
            label.style.color     = levels[score].color;
        }
    </script>
</body>
</html>