<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | BrgyCIRS</title>
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

        /* Feature bullets */
        .features { display: flex; flex-direction: column; gap: 14px; }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .feature-dot {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(198,40,40,0.15);
            border: 1px solid rgba(198,40,40,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .feature-dot i { color: var(--red-hover); font-size: 0.75rem; }

        .feature-text strong {
            display: block;
            font-size: 0.87rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 2px;
        }

        .feature-text span {
            font-size: 0.8rem;
            color: var(--muted);
        }

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

        .grid-lines span { display: block; width: 1px; height: 100%; background: var(--red-hover); }

        .left-footer {
            position: relative;
            z-index: 1;
            color: var(--muted);
            font-size: 0.78rem;
        }

        /* ── Right panel ─────────────────────────────── */
        .right-panel {
            width: 520px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 48px;
            background: var(--bg);
            overflow-y: auto;
        }

        .form-header {
            margin-bottom: 28px;
        }

        .form-header h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.8rem;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-header p { color: var(--muted); font-size: 0.87rem; }

        /* Two-column grid for some fields */
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .field-group { margin-bottom: 18px; }

        .field-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: #AEAEB2;
            letter-spacing: 0.03em;
            margin-bottom: 7px;
        }

        .field-wrap { position: relative; }

        .field-wrap input,
        .field-wrap select {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 16px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.88rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
        }

        .field-wrap select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236E6E73' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        .field-wrap input:focus,
        .field-wrap select:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px var(--red-glow);
        }

        .field-wrap input::placeholder { color: var(--muted); }
        .field-wrap select option { background: #222228; }

        /* File input */
        .file-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .file-preview {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--border);
            flex-shrink: 0;
            background: var(--surface-2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .file-preview img { width: 100%; height: 100%; object-fit: cover; }
        .file-preview i { color: var(--muted); font-size: 1rem; }

        .file-input-btn {
            flex: 1;
            background: var(--surface);
            border: 1px dashed rgba(255,255,255,0.15);
            border-radius: 10px;
            padding: 10px 14px;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.83rem;
            cursor: pointer;
            text-align: left;
            transition: border-color 0.2s, color 0.2s;
            overflow: hidden;
        }

        .file-input-btn:hover { border-color: var(--red); color: var(--text); }

        #profile_photo_real {
            display: none;
        }

        .eye-toggle {
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

        .eye-toggle:hover { color: var(--text); }
        .has-toggle { padding-right: 44px; }

        /* Error */
        .error-list {
            background: rgba(198,40,40,0.1);
            border: 1px solid rgba(198,40,40,0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .error-list ul { margin: 0; padding-left: 16px; }
        .error-list li { color: #EF9A9A; font-size: 0.83rem; }

        /* Submit */
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
            margin-top: 4px;
        }

        .btn-submit:hover {
            background: #B71C1C;
            transform: translateY(-1px);
            box-shadow: 0 6px 24px var(--red-glow);
        }

        .btn-submit:active { transform: translateY(0); }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .form-footer a { color: var(--red-hover); text-decoration: none; font-weight: 500; }
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
            .field-row { grid-template-columns: 1fr; }
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
            <h1>Join your<br><span>community system.</span></h1>
            <p>Create an account to start submitting and tracking barangay incident reports directly to local officials.</p>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-dot"><i class="fa fa-file-alt"></i></div>
                    <div class="feature-text">
                        <strong>Submit Reports</strong>
                        <span>File incident reports with photo evidence</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-dot"><i class="fa fa-clock"></i></div>
                    <div class="feature-text">
                        <strong>Track Status</strong>
                        <span>Monitor your reports from pending to resolved</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-dot"><i class="fa fa-shield-alt"></i></div>
                    <div class="feature-text">
                        <strong>Secure & Private</strong>
                        <span>Your information is protected at all times</span>
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
            <h2>Create account</h2>
            <p>Fill in the details below to get started</p>
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

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <div class="field-row">
                <div class="field-group">
                    <label for="name">Full Name</label>
                    <div class="field-wrap">
                        <input type="text" id="name" name="name"
                               placeholder="Juan dela Cruz"
                               value="{{ old('name') }}"
                               required autofocus>
                    </div>
                </div>
                <div class="field-group">
                    <label for="role">Role</label>
                    <div class="field-wrap">
                        <select id="role" name="role" required>
                            <option value="Resident" {{ old('role') === 'Resident' ? 'selected' : '' }}>Resident</option>
                            <option value="Admin"    {{ old('role') === 'Admin'    ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field-group">
                <label>Profile Photo <span style="color:var(--muted); font-weight:400;">(optional)</span></label>
                <div class="file-input-wrap">
                    <div class="file-preview" id="photoPreview">
                        <i class="fa fa-user"></i>
                    </div>
                    <button type="button" class="file-input-btn" id="fileLabel"
                            onclick="document.getElementById('profile_photo_real').click()">
                        <i class="fa fa-upload" style="margin-right:6px;"></i>
                        <span id="fileLabelText">Choose a photo...</span>
                    </button>
                    <input type="file" id="profile_photo_real" name="profile_photo"
                           accept="image/*" onchange="handleFile(this)">
                </div>
            </div>

            <div class="field-group">
                <label for="email">Email Address</label>
                <div class="field-wrap">
                    <input type="email" id="email" name="email"
                           placeholder="you@example.com"
                           value="{{ old('email') }}"
                           required>
                </div>
            </div>

            <div class="field-row">
                <div class="field-group">
                    <label for="password">Password</label>
                    <div class="field-wrap">
                        <input type="password" id="password" name="password"
                               class="has-toggle"
                               placeholder="Min. 8 characters"
                               required>
                        <button type="button" class="eye-toggle" onclick="togglePass('password', this)">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="field-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="field-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="has-toggle"
                               placeholder="Repeat password"
                               required>
                        <button type="button" class="eye-toggle" onclick="togglePass('password_confirmation', this)">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">Create Account</button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
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

        function handleFile(input) {
            const file = input.files[0];
            if (!file) return;

            // Update label text
            const name = file.name.length > 28 ? file.name.substring(0, 25) + '...' : file.name;
            document.getElementById('fileLabelText').textContent = name;

            // Show preview
            const reader = new FileReader();
            reader.onload = e => {
                const preview = document.getElementById('photoPreview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        }
    </script>
</body>
</html>