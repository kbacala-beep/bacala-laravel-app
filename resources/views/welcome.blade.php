<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrgyCIRS — Barangay Community Incident Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:       #C62828;
            --red-hover: #EF5350;
            --red-glow:  rgba(198,40,40,0.25);
            --bg:        #0E0E10;
            --surface:   #18181C;
            --surface-2: #1F1F24;
            --border:    rgba(255,255,255,0.07);
            --text:      #E8E8E8;
            --muted:     #6E6E73;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── Navbar ───────────────────────────────── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 60px;
            background: rgba(14,14,16,0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            transition: padding 0.3s;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-icon {
            width: 34px;
            height: 34px;
            background: var(--red);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.85rem;
            box-shadow: 0 2px 12px var(--red-glow);
        }

        .nav-logo-text {
            font-family: 'DM Serif Display', serif;
            font-size: 1.1rem;
            color: var(--text);
        }

        .nav-actions { display: flex; align-items: center; gap: 10px; }

        .btn-ghost {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 20px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .btn-ghost:hover {
            border-color: rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
            color: var(--text);
        }

        .btn-cta {
            background: var(--red);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 2px 12px var(--red-glow);
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }

        .btn-cta:hover {
            background: #B71C1C;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 20px var(--red-glow);
        }

        /* ── Hero ─────────────────────────────────── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 24px 80px;
            position: relative;
            overflow: hidden;
        }

        /* Background radial glow */
        .hero::before {
            content: '';
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 600px;
            background: radial-gradient(ellipse, rgba(198,40,40,0.1) 0%, transparent 65%);
            pointer-events: none;
        }

        /* Subtle dot grid */
        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 0%, transparent 100%);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 720px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(198,40,40,0.1);
            border: 1px solid rgba(198,40,40,0.3);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--red-hover);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 28px;
        }

        .hero-badge i { font-size: 0.7rem; }

        .hero h1 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(2.8rem, 6vw, 4.5rem);
            line-height: 1.1;
            color: var(--text);
            margin-bottom: 20px;
        }

        .hero h1 em {
            font-style: italic;
            color: var(--red-hover);
        }

        .hero p {
            font-size: 1.05rem;
            color: var(--muted);
            line-height: 1.7;
            max-width: 520px;
            margin: 0 auto 40px;
        }

        .hero-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: var(--red);
            border: none;
            border-radius: 10px;
            padding: 14px 32px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 24px var(--red-glow);
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }

        .btn-hero-primary:hover {
            background: #B71C1C;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 32px var(--red-glow);
        }

        .btn-hero-secondary {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 32px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .btn-hero-secondary:hover {
            border-color: rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.04);
            color: var(--text);
        }

        /* Scroll hint */
        .scroll-hint {
            position: absolute;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: var(--muted);
            font-size: 0.75rem;
            z-index: 1;
        }

        .scroll-hint i {
            animation: bounce 1.6s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(5px); }
        }

        /* ── Stats strip ──────────────────────────── */
        .stats-strip {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 40px 60px;
            display: flex;
            justify-content: center;
            gap: 80px;
            flex-wrap: wrap;
        }

        .stat-item { text-align: center; }

        .stat-item .num {
            font-family: 'DM Serif Display', serif;
            font-size: 2.4rem;
            color: var(--text);
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-item .num span { color: var(--red-hover); }

        .stat-item .label {
            font-size: 0.83rem;
            color: var(--muted);
        }

        /* ── Features ─────────────────────────────── */
        .features-section {
            padding: 100px 60px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-label {
            text-align: center;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--red-hover);
            margin-bottom: 14px;
        }

        .section-title {
            text-align: center;
            font-family: 'DM Serif Display', serif;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            color: var(--text);
            margin-bottom: 12px;
        }

        .section-sub {
            text-align: center;
            color: var(--muted);
            font-size: 0.95rem;
            max-width: 500px;
            margin: 0 auto 60px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px;
            transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
        }

        .feature-card:hover {
            border-color: rgba(198,40,40,0.35);
            transform: translateY(-3px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            background: rgba(198,40,40,0.12);
            border: 1px solid rgba(198,40,40,0.25);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }

        .feature-icon i { color: var(--red-hover); font-size: 1rem; }

        .feature-card h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .feature-card p {
            font-size: 0.87rem;
            color: var(--muted);
            line-height: 1.65;
        }

        /* ── How it works ─────────────────────────── */
        .steps-section {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 100px 60px;
        }

        .steps-inner {
            max-width: 900px;
            margin: 0 auto;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 60px;
            position: relative;
        }

        .steps-grid::before {
            content: '';
            position: absolute;
            top: 22px;
            left: calc(16.66% + 16px);
            right: calc(16.66% + 16px);
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), var(--border), transparent);
        }

        .step-item { text-align: center; }

        .step-num {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--red);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            margin: 0 auto 20px;
            box-shadow: 0 4px 16px var(--red-glow);
            position: relative;
            z-index: 1;
        }

        .step-item h4 {
            font-size: 0.97rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .step-item p {
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.6;
        }

        /* ── CTA section ──────────────────────────── */
        .cta-section {
            padding: 100px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 400px;
            background: radial-gradient(ellipse, rgba(198,40,40,0.1) 0%, transparent 65%);
            pointer-events: none;
        }

        .cta-section h2 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(2rem, 4vw, 3rem);
            color: var(--text);
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .cta-section p {
            color: var(--muted);
            font-size: 0.97rem;
            margin-bottom: 36px;
            position: relative;
            z-index: 1;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        /* ── Footer ───────────────────────────────── */
        footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 28px 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        footer .copy {
            font-size: 0.82rem;
            color: var(--muted);
        }

        footer .copy a {
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        footer .copy a:hover { color: var(--red-hover); }

        /* Entrance animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .animate {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .animate.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .hero-content { animation: fadeUp 0.7s ease both; }

        /* Responsive */
        @media (max-width: 900px) {
            nav { padding: 16px 24px; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .steps-grid::before { display: none; }
            .stats-strip { gap: 40px; padding: 40px 24px; }
            .features-section, .steps-section { padding: 80px 24px; }
        }

        @media (max-width: 600px) {
            .features-grid { grid-template-columns: 1fr; }
            footer { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav>
        <a href="#" class="nav-brand">
            <div class="nav-icon"><i class="fa fa-shield-alt"></i></div>
            <span class="nav-logo-text">BrgyCIRS</span>
        </a>
        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn-ghost">Sign In</a>
            <a href="{{ route('register') }}" class="btn-cta">Get Started</a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fa fa-map-marker-alt"></i>
                Barangay Incident Reporting
            </div>
            <h1>Your voice,<br><em>heard and acted on.</em></h1>
            <p>BrgyCIRS connects barangay residents with local officials — making it easy to report incidents, track their progress, and see them resolved.</p>
            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn-hero-primary">
                    <i class="fa fa-user-plus" style="margin-right:8px;"></i> Create Account
                </a>
                <a href="{{ route('login') }}" class="btn-hero-secondary">
                    Sign In
                </a>
            </div>
        </div>

        <div class="scroll-hint">
            <span>Scroll to learn more</span>
            <i class="fa fa-chevron-down"></i>
        </div>
    </section>

    <!-- Stats -->
    <div class="stats-strip animate">
        <div class="stat-item">
            <div class="num">Fast<span>.</span></div>
            <div class="label">Submit in under 2 minutes</div>
        </div>
        <div class="stat-item">
            <div class="num">3<span>+</span></div>
            <div class="label">Report status stages</div>
        </div>
        <div class="stat-item">
            <div class="num">24/7<span>.</span></div>
            <div class="label">Always accessible</div>
        </div>
        <div class="stat-item">
            <div class="num">Secure<span>.</span></div>
            <div class="label">Your data is protected</div>
        </div>
    </div>

    <!-- Features -->
    <section class="features-section">
        <p class="section-label">What you can do</p>
        <h2 class="section-title">Everything your barangay needs</h2>
        <p class="section-sub">A focused set of tools to report, manage, and resolve community incidents efficiently.</p>

        <div class="features-grid">
            <div class="feature-card animate">
                <div class="feature-icon"><i class="fa fa-file-alt"></i></div>
                <h3>Submit Reports</h3>
                <p>File detailed incident reports with descriptions, resident information, and photo evidence in minutes.</p>
            </div>
            <div class="feature-card animate" style="transition-delay:0.1s">
                <div class="feature-icon"><i class="fa fa-tasks"></i></div>
                <h3>Track Status</h3>
                <p>Follow your reports through Pending, In Progress, and Resolved stages with full visibility at every step.</p>
            </div>
            <div class="feature-card animate" style="transition-delay:0.2s">
                <div class="feature-icon"><i class="fa fa-images"></i></div>
                <h3>Attach Evidence</h3>
                <p>Upload photos and documents directly with your report so officials have everything they need to act.</p>
            </div>
            <div class="feature-card animate" style="transition-delay:0.1s">
                <div class="feature-icon"><i class="fa fa-shield-alt"></i></div>
                <h3>Role-Based Access</h3>
                <p>Residents submit and manage their own reports. Admins oversee all reports and update statuses.</p>
            </div>
            <div class="feature-card animate" style="transition-delay:0.2s">
                <div class="feature-icon"><i class="fa fa-archive"></i></div>
                <h3>Archived Records</h3>
                <p>Deleted reports are archived — never permanently lost — giving admins a full audit trail at all times.</p>
            </div>
            <div class="feature-card animate" style="transition-delay:0.3s">
                <div class="feature-icon"><i class="fa fa-user-circle"></i></div>
                <h3>Profile Management</h3>
                <p>Update your name, email, profile photo, and password from your personal account settings page.</p>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="steps-section">
        <div class="steps-inner">
            <p class="section-label">How it works</p>
            <h2 class="section-title">Three steps to resolution</h2>
            <p class="section-sub">The process is simple and straightforward for both residents and barangay officials.</p>

            <div class="steps-grid">
                <div class="step-item animate">
                    <div class="step-num">1</div>
                    <h4>Create an Account</h4>
                    <p>Register as a resident to get access to the reporting system. Only a name, email, and password required.</p>
                </div>
                <div class="step-item animate" style="transition-delay:0.15s">
                    <div class="step-num">2</div>
                    <h4>Submit Your Report</h4>
                    <p>Describe the incident, attach photos if needed, and submit. Your report is immediately visible to admins.</p>
                </div>
                <div class="step-item animate" style="transition-delay:0.3s">
                    <div class="step-num">3</div>
                    <h4>Track Until Resolved</h4>
                    <p>Monitor your report's status as barangay officials update it from Pending through to Resolved.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <h2>Ready to get started?</h2>
        <p>Join your barangay community on BrgyCIRS today.</p>
        <div class="cta-buttons">
            <a href="{{ route('register') }}" class="btn-hero-primary">
                <i class="fa fa-user-plus" style="margin-right:8px;"></i> Create Account
            </a>
            <a href="{{ route('login') }}" class="btn-hero-secondary">Sign In Instead</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="copy">
            &copy; {{ date('Y') }} BrgyCIRS. All rights reserved.
        </div>
        <div class="copy">
            Barangay Community Incident Reporting System
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll-triggered animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.animate').forEach(el => observer.observe(el));
    </script>
</body>
</html>