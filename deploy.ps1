# =============================================================================
#  deploy.ps1 - Laravel Deployment Automation Script
#  Author  : Karl Joshua Bacala
#  Project : C:\Users\Administrator\Bacala
# =============================================================================

$PROJECT_PATH = "C:\Users\Administrator\Bacala"
$LOG_FILE     = Join-Path $PROJECT_PATH "deploy_log.txt"

# ─────────────────────────────────────────────────────────────────────────────
# HELPER: LOGGING
# ─────────────────────────────────────────────────────────────────────────────
function Write-Log {
    param(
        [string]$Message,
        [string]$Status = "INFO"   # INFO | SUCCESS | ERROR | WARNING
    )
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $line      = "[$timestamp] [$Status] $Message"

    # Append to log file
    Add-Content -Path $LOG_FILE -Value $line

    # Colour-coded console output
    switch ($Status) {
        "SUCCESS" { Write-Host $line -ForegroundColor Green  }
        "ERROR"   { Write-Host $line -ForegroundColor Red    }
        "WARNING" { Write-Host $line -ForegroundColor Yellow }
        default   { Write-Host $line -ForegroundColor Cyan   }
    }
}

# ─────────────────────────────────────────────────────────────────────────────
# HELPER: SECTION BANNER
# ─────────────────────────────────────────────────────────────────────────────
function Show-Banner {
    param([string]$Title)
    $line = "=" * 60
    Write-Host ""
    Write-Host $line                   -ForegroundColor Magenta
    Write-Host "  $Title"              -ForegroundColor Magenta
    Write-Host $line                   -ForegroundColor Magenta
    Write-Host ""
}

# ─────────────────────────────────────────────────────────────────────────────
# HELPER: INIT LOG SESSION
# ─────────────────────────────────────────────────────────────────────────────
function Initialize-Log {
    $separator = "=" * 60
    $header    = @"
$separator
  Laravel Deploy Log
  Project : $PROJECT_PATH
  Started : $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
$separator
"@
    Add-Content -Path $LOG_FILE -Value $header
}

# ─────────────────────────────────────────────────────────────────────────────
# TASK 1 — ENVIRONMENT VALIDATION
# ─────────────────────────────────────────────────────────────────────────────
function Test-Environment {
    Show-Banner "TASK 1 — Environment Validation"
    Write-Log "Starting environment validation..."

    $allGood = $true

    # Check PHP
    try {
        $phpVersion = php -r "echo PHP_VERSION;" 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Log "PHP found — version $phpVersion" "SUCCESS"
        } else {
            throw "PHP returned non-zero exit code"
        }
    } catch {
        Write-Log "PHP is NOT installed or not in PATH. Please install PHP and add it to your PATH." "ERROR"
        $allGood = $false
    }

    # Check Composer
    try {
        $composerVersion = composer --version 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Log "Composer found — $composerVersion" "SUCCESS"
        } else {
            throw "Composer returned non-zero exit code"
        }
    } catch {
        Write-Log "Composer is NOT installed or not in PATH. Please install Composer from https://getcomposer.org" "ERROR"
        $allGood = $false
    }

    # Check .env — auto-copy from .env.example if missing (BONUS)
    $envPath     = Join-Path $PROJECT_PATH ".env"
    $envExample  = Join-Path $PROJECT_PATH ".env.example"

    if (Test-Path $envPath) {
        Write-Log ".env file found." "SUCCESS"
    } elseif (Test-Path $envExample) {
        Write-Log ".env not found — copying from .env.example (bonus auto-copy)..." "WARNING"
        try {
            Copy-Item $envExample $envPath -ErrorAction Stop
            Write-Log ".env created from .env.example successfully." "SUCCESS"
        } catch {
            Write-Log "Failed to copy .env.example to .env: $_" "ERROR"
            $allGood = $false
        }
    } else {
        Write-Log ".env file is missing and no .env.example found. Create a .env file before deploying." "ERROR"
        $allGood = $false
    }

    if (-not $allGood) {
        Write-Log "Environment validation FAILED. Fix the issues above and re-run." "ERROR"
        throw "Environment validation failed — critical requirements missing."
    }

    Write-Log "Environment validation PASSED." "SUCCESS"
}

# ─────────────────────────────────────────────────────────────────────────────
# TASK 2 — DEPENDENCY INSTALLATION
# ─────────────────────────────────────────────────────────────────────────────
function Install-Dependencies {
    Show-Banner "TASK 2 — Dependency Installation"
    Write-Log "Running: composer install --no-interaction --prefer-dist --optimize-autoloader"

    try {
        Set-Location $PROJECT_PATH
        composer install --no-interaction --prefer-dist --optimize-autoloader
        if ($LASTEXITCODE -ne 0) { throw "Composer install exited with code $LASTEXITCODE" }
        Write-Log "Dependencies installed successfully." "SUCCESS"
    } catch {
        Write-Log "Dependency installation FAILED: $_" "ERROR"
        throw
    }
}

# ─────────────────────────────────────────────────────────────────────────────
# TASK 3 — APPLICATION SETUP
# ─────────────────────────────────────────────────────────────────────────────
function Set-ApplicationSetup {
    Show-Banner "TASK 3 — Application Setup"

    # Generate app key
    Write-Log "Generating application key..."
    try {
        Set-Location $PROJECT_PATH
        php artisan key:generate --force
        if ($LASTEXITCODE -ne 0) { throw "key:generate failed with code $LASTEXITCODE" }
        Write-Log "Application key generated successfully." "SUCCESS"
    } catch {
        Write-Log "Failed to generate application key: $_" "ERROR"
        throw
    }

    # Run migrations
    Write-Log "Running database migrations..."
    try {
        php artisan migrate --force
        if ($LASTEXITCODE -ne 0) { throw "migrate failed with code $LASTEXITCODE" }
        Write-Log "Database migrations completed successfully." "SUCCESS"
    } catch {
        Write-Log "Database migration FAILED: $_" "ERROR"
        throw
    }
}

# ─────────────────────────────────────────────────────────────────────────────
# TASK 4 — OPTIMIZATION
# ─────────────────────────────────────────────────────────────────────────────
function Invoke-Optimization {
    Show-Banner "TASK 4 — Optimization"
    Set-Location $PROJECT_PATH

    # Cache config
    Write-Log "Caching configuration..."
    try {
        php artisan config:cache
        if ($LASTEXITCODE -ne 0) { throw "config:cache failed" }
        Write-Log "Configuration cached successfully." "SUCCESS"
    } catch {
        Write-Log "config:cache FAILED: $_" "ERROR"
        throw
    }

    # Cache routes
    Write-Log "Caching routes..."
    try {
        php artisan route:cache
        if ($LASTEXITCODE -ne 0) { throw "route:cache failed" }
        Write-Log "Routes cached successfully." "SUCCESS"
    } catch {
        Write-Log "route:cache FAILED: $_" "ERROR"
        throw
    }

    # Cache views
    Write-Log "Caching views..."
    try {
        php artisan view:cache
        if ($LASTEXITCODE -ne 0) { throw "view:cache failed" }
        Write-Log "Views cached successfully." "SUCCESS"
    } catch {
        Write-Log "view:cache FAILED: $_" "ERROR"
        throw
    }
}

# ─────────────────────────────────────────────────────────────────────────────
# TASK 5 — START DEV SERVER
# ─────────────────────────────────────────────────────────────────────────────
function Start-DevServer {
    Show-Banner "TASK 5 — Starting Development Server"
    Write-Log "Starting Laravel development server on http://127.0.0.1:8000"

    try {
        Set-Location $PROJECT_PATH
        Write-Host ""
        Write-Host "  Server is starting at http://127.0.0.1:8000" -ForegroundColor Green
        Write-Host "  Press Ctrl+C to stop the server." -ForegroundColor Yellow
        Write-Host ""
        Write-Log "Server started. Access at http://127.0.0.1:8000" "SUCCESS"
        php artisan serve
    } catch {
        Write-Log "Failed to start development server: $_" "ERROR"
        throw
    }
}

# ─────────────────────────────────────────────────────────────────────────────
# BONUS — DATABASE RESET
# ─────────────────────────────────────────────────────────────────────────────
function Reset-Database {
    Show-Banner "BONUS — Database Reset"
    Write-Host "  WARNING: This will wipe all data and re-run migrations!" -ForegroundColor Red
    $confirm = Read-Host "  Are you sure? Type YES to confirm"

    if ($confirm -ne "YES") {
        Write-Log "Database reset cancelled by user." "WARNING"
        Write-Host "  Reset cancelled." -ForegroundColor Yellow
        return
    }

    Write-Log "Running: php artisan migrate:fresh --seed --force"
    try {
        Set-Location $PROJECT_PATH
        php artisan migrate:fresh --seed --force
        if ($LASTEXITCODE -ne 0) { throw "migrate:fresh failed with code $LASTEXITCODE" }
        Write-Log "Database reset and re-seeded successfully." "SUCCESS"
    } catch {
        Write-Log "Database reset FAILED: $_" "ERROR"
        throw
    }
}

# ─────────────────────────────────────────────────────────────────────────────
# FULL DEPLOYMENT PIPELINE
# ─────────────────────────────────────────────────────────────────────────────
function Invoke-FullDeployment {
    Show-Banner "FULL DEPLOYMENT PIPELINE"
    Write-Log "====== Full Deployment Started ======"

    try {
        Test-Environment
        Install-Dependencies
        Set-ApplicationSetup
        Invoke-Optimization
        Write-Log "====== Full Deployment Completed Successfully ======" "SUCCESS"
        Write-Host ""
        Write-Host "  Deployment complete! Starting dev server..." -ForegroundColor Green
        Start-Sleep -Seconds 1
        Start-DevServer
    } catch {
        Write-Log "====== Full Deployment ABORTED: $_ ======" "ERROR"
        Write-Host ""
        Write-Host "  Deployment aborted. Check deploy_log.txt for details." -ForegroundColor Red
    }
}

# ─────────────────────────────────────────────────────────────────────────────
# MENU-DRIVEN INTERFACE (BONUS)
# ─────────────────────────────────────────────────────────────────────────────
function Show-Menu {
    Clear-Host
    Write-Host ""
    Write-Host "  ╔══════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "  ║       Laravel Deploy Manager             ║" -ForegroundColor Cyan
    Write-Host "  ║       Project: Bacala                    ║" -ForegroundColor Cyan
    Write-Host "  ╠══════════════════════════════════════════╣" -ForegroundColor Cyan
    Write-Host "  ║  [1]  Full Deployment                    ║" -ForegroundColor White
    Write-Host "  ║  [2]  Install Dependencies Only          ║" -ForegroundColor White
    Write-Host "  ║  [3]  Run Migrations Only                ║" -ForegroundColor White
    Write-Host "  ║  [4]  Optimize (Cache Config/Routes/     ║" -ForegroundColor White
    Write-Host "  ║       Views)                             ║" -ForegroundColor White
    Write-Host "  ║  [5]  Reset Database                     ║" -ForegroundColor White
    Write-Host "  ║  [6]  Start Development Server           ║" -ForegroundColor White
    Write-Host "  ║  [7]  Validate Environment Only          ║" -ForegroundColor White
    Write-Host "  ║  [8]  Exit                               ║" -ForegroundColor White
    Write-Host "  ╚══════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
}

# ─────────────────────────────────────────────────────────────────────────────
# ENTRY POINT
# ─────────────────────────────────────────────────────────────────────────────

# Ensure project path exists
if (-not (Test-Path $PROJECT_PATH)) {
    Write-Host "[ERROR] Project path not found: $PROJECT_PATH" -ForegroundColor Red
    exit 1
}

# Ensure log file exists
if (-not (Test-Path $LOG_FILE)) {
    New-Item -ItemType File -Path $LOG_FILE -Force | Out-Null
}

Initialize-Log

# Main loop
do {
    Show-Menu
    $choice = Read-Host "  Enter your choice (1-8)"

    switch ($choice) {
        "1" { Invoke-FullDeployment }
        "2" {
            try   { Test-Environment; Install-Dependencies }
            catch { Write-Log "Step failed: $_" "ERROR" }
        }
        "3" {
            try {
                Set-Location $PROJECT_PATH
                Write-Log "Running migrations only..."
                php artisan migrate --force
                if ($LASTEXITCODE -ne 0) { throw "migrate failed" }
                Write-Log "Migrations completed." "SUCCESS"
            } catch {
                Write-Log "Migration failed: $_" "ERROR"
            }
        }
        "4" {
            try   { Invoke-Optimization }
            catch { Write-Log "Optimization failed: $_" "ERROR" }
        }
        "5" {
            try   { Reset-Database }
            catch { Write-Log "DB reset failed: $_" "ERROR" }
        }
        "6" {
            try   { Start-DevServer }
            catch { Write-Log "Server failed to start: $_" "ERROR" }
        }
        "7" {
            try   { Test-Environment }
            catch { Write-Log "Validation failed: $_" "ERROR" }
        }
        "8" {
            Write-Log "Script exited by user." "INFO"
            Write-Host "  Goodbye!" -ForegroundColor Cyan
            break
        }
        default {
            Write-Host "  Invalid choice. Please enter a number between 1 and 8." -ForegroundColor Yellow
        }
    }

    if ($choice -ne "8") {
        Write-Host ""
        Write-Host "  Press Enter to return to the menu..." -ForegroundColor Gray
        Read-Host | Out-Null
    }

} while ($choice -ne "8")
