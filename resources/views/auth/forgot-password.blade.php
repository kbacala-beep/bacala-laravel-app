<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | BrgyCIRS</title>
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

        .left-content h1 span { color: var(--red-hover); }

        .left-content p {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.7;
            max-width: 340px;
            margin-bottom: 32px;
        }

        /* Steps hint */
        .hint-steps { display: flex; flex-direction: column; gap: 16px; }

        .hint-step {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .hint-step-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(198,40,40,0.15);
            border: 1px solid rgba(198,40,40,0.3);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--red-hover);
        }

        .hint-step-text {
            font-size: 0.87rem;
            color: var(--muted);
            line-height: 1.5;
            padding-top: 4px;
        }

        .hint-step-text strong {
            display: block;
            color: var(--text);
            font-weight: 500;
            margin-bottom: 2px;
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

        /* Success status */
        .status-box {
            background: rgba(46,125,50,0.12);
            border: 1px solid rgba(46,125,50,0.3);
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .status-box i { color: #81C784; margin-top: 1px; flex-shrink: 0; }
        .status-box span { color: #A5D6A7; font-size: 0.87rem; line-height: 1.5; }

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

        .field-group { margin-bottom: 24px; }

        .field-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: #AEAEB2;
            letter-spacing: 0.03em;
            margin-bottom: 8px;
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
            <h1>Reset your<br><span>password.</span></h1>
            <p>Follow the steps below to regain access to your BrgyCIRS account securely.</p>

            <div class="hint-steps">
                <div class="hint-step">
                    <div class="hint-step-num">1</div>
                    <div class="hint-step-text">
                        <strong>Enter your email</strong>
                        Provide the email address linked to your account.
                    </div>
                </div>
                <div class="hint-step">
                    <div class="hint-step-num">2</div>
                    <div class="hint-step-text">
                        <strong>Check your inbox</strong>
                        We'll send you a secure password reset link.
                    </div>
                </div>
                <div class="hint-step">
                    <div class="hint-step-num">3</div>
                    <div class="hint-step-text">
                        <strong>Set a new password</strong>
                        Click the link and choose a new password to get back in.
                    </div>
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
                <i class="fa fa-lock"></i>
            </div>
            <h2>Forgot password?</h2>
            <p>No worries. Enter your email and we'll send you a reset link to get back into your account.</p>
        </div>

        @if (session('status'))
            <div class="status-box">
                <i class="fa fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="error-list">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
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

            <button type="submit" class="btn-submit">
                <i class="fa fa-paper-plane" style="margin-right:8px;"></i> Send Reset Link
            </button>
        </form>

        <div class="form-footer">
            Remembered your password? <a href="{{ route('login') }}">Back to Sign In</a>
        </div>
    </div>

</body>
</html>