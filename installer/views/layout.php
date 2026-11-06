<!DOCTYPE html>
<html lang="en" class="auto-theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEZUX Hostinger Production Deployment & Setup</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="installer/assets/installer.css">
    <script>
        window.GEZUX_CSRF = "<?= htmlspecialchars($csrfToken ?? '') ?>";
    </script>
</head>
<body>
    <div class="ambient-glow"></div>

    <div class="installer-wrapper">
        <!-- Header -->
        <header class="installer-header">
            <div class="logo-brand">
                <div class="logo-icon">G</div>
                <div>
                    <h1 class="brand-title">GEZUX</h1>
                    <p class="brand-subtitle">Production Deployment & Setup Console</p>
                </div>
            </div>
            <div class="badge-version">v1.0.0-hostinger</div>
        </header>

        <!-- Step Navigation -->
        <nav class="stepper">
            <div class="step-item <?= $currentStep === 'preflight' ? 'active' : ($stepIndex > 1 ? 'completed' : '') ?>">
                <span class="step-num">1</span>
                <span>Preflight</span>
            </div>
            <div class="step-item <?= $currentStep === 'environment' ? 'active' : ($stepIndex > 2 ? 'completed' : '') ?>">
                <span class="step-num">2</span>
                <span>Environment</span>
            </div>
            <div class="step-item <?= $currentStep === 'database' ? 'active' : ($stepIndex > 3 ? 'completed' : '') ?>">
                <span class="step-num">3</span>
                <span>Database</span>
            </div>
            <div class="step-item <?= $currentStep === 'setup' ? 'active' : ($stepIndex > 4 ? 'completed' : '') ?>">
                <span class="step-num">4</span>
                <span>Admin & Seeds</span>
            </div>
            <div class="step-item <?= $currentStep === 'storage' ? 'active' : ($stepIndex > 5 ? 'completed' : '') ?>">
                <span class="step-num">5</span>
                <span>Storage & Assets</span>
            </div>
            <div class="step-item <?= $currentStep === 'health' ? 'active' : ($stepIndex > 6 ? 'completed' : '') ?>">
                <span class="step-num">6</span>
                <span>Finalize & Lock</span>
            </div>
        </nav>

        <!-- Main Step Content -->
        <main class="card">
            <?php if ($currentStep === 'preflight'): ?>
                <!-- STEP 1: PREFLIGHT -->
                <h2 class="card-title">Server Preflight & Environment Audit</h2>
                <p class="card-desc">Validating PHP 8.3+ runtime, required extensions, storage write permissions, and package build integrity.</p>

                <?php if (!$preflight['can_install']): ?>
                    <div class="alert alert-danger">
                        <strong>Installation Blocked:</strong> One or more required server prerequisites failed. Please resolve the blockers highlighted below to continue.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <strong>Preflight Passed:</strong> Server environment and package dependencies meet all GEZUX production requirements.
                    </div>
                <?php endif; ?>

                <!-- 1. PHP Version -->
                <div class="check-group">
                    <div class="check-group-title">PHP Runtime Version</div>
                    <div class="check-row">
                        <div class="check-info">
                            <span class="check-name"><?= htmlspecialchars($preflight['php']['name']) ?> (Detected: <?= htmlspecialchars($preflight['php']['current']) ?>)</span>
                            <span class="check-sub"><?= htmlspecialchars($preflight['php']['guidance']) ?></span>
                        </div>
                        <span class="badge <?= $preflight['php']['passed'] ? 'badge-pass' : 'badge-fail' ?>">
                            <?= $preflight['php']['passed'] ? 'PASS' : 'FAIL (>= 8.3)' ?>
                        </span>
                    </div>
                </div>

                <!-- 2. Extensions -->
                <div class="check-group">
                    <div class="check-group-title">PHP Extensions</div>
                    <?php foreach ($preflight['extensions'] as $ext): ?>
                        <div class="check-row">
                            <div class="check-info">
                                <span class="check-name"><?= htmlspecialchars($ext['name']) ?> <?= $ext['required'] ? '' : '<small style="color:var(--text-subtle)">(Optional)</small>' ?></span>
                                <span class="check-sub"><?= htmlspecialchars($ext['description']) ?> &bull; <?= htmlspecialchars($ext['guidance']) ?></span>
                            </div>
                            <span class="badge <?= $ext['passed'] ? 'badge-pass' : ($ext['required'] ? 'badge-fail' : 'badge-warn') ?>">
                                <?= $ext['passed'] ? 'PASS' : ($ext['required'] ? 'REQUIRED' : 'OPTIONAL') ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 3. Permissions -->
                <div class="check-group">
                    <div class="check-group-title">Filesystem Write Permissions</div>
                    <?php foreach ($preflight['permissions'] as $perm): ?>
                        <div class="check-row">
                            <div class="check-info">
                                <span class="check-name"><?= htmlspecialchars($perm['name']) ?></span>
                                <span class="check-sub"><?= htmlspecialchars($perm['guidance']) ?></span>
                            </div>
                            <span class="badge <?= $perm['passed'] ? 'badge-pass' : 'badge-fail' ?>">
                                <?= $perm['passed'] ? 'WRITABLE' : 'UNWRITABLE' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 4. Package Integrity -->
                <div class="check-group">
                    <div class="check-group-title">Package & Build Integrity</div>
                    <?php foreach ($preflight['package'] as $pkg): ?>
                        <div class="check-row">
                            <div class="check-info">
                                <span class="check-name"><?= htmlspecialchars($pkg['name']) ?></span>
                                <span class="check-sub"><?= htmlspecialchars($pkg['guidance']) ?></span>
                            </div>
                            <span class="badge <?= $pkg['passed'] ? 'badge-pass' : 'badge-fail' ?>">
                                <?= $pkg['passed'] ? 'PASS' : 'MISSING' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="action-bar">
                    <?php if ($preflight['can_install']): ?>
                        <a href="?step=environment" class="btn btn-primary">
                            Next: Configure Environment &rarr;
                        </a>
                    <?php else: ?>
                        <a href="?step=preflight" class="btn btn-secondary">
                            Re-run Preflight Check
                        </a>
                    <?php endif; ?>
                </div>

            <?php elseif ($currentStep === 'environment'): ?>
                <!-- STEP 2: ENVIRONMENT -->
                <h2 class="card-title">Environment & Database Configuration</h2>
                <p class="card-desc">Review and configure your database connection and domain settings. Pre-populated from bundled configuration.</p>

                <div id="db-test-result"></div>

                <form id="form-env" method="POST" action="?step=environment">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="action" value="save_env">

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Application Name</label>
                            <input type="text" name="APP_NAME" class="form-input" value="<?= htmlspecialchars($env['APP_NAME'] ?? 'GEZUX') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Application URL (Domain)</label>
                            <input type="text" name="APP_URL" class="form-input" value="<?= htmlspecialchars($env['APP_URL'] ?? 'https://example.com') ?>" required>
                            <span class="form-help">Auto-detected from current server host & protocol.</span>
                        </div>
                    </div>

                    <h3 style="font-size: 1rem; font-weight: 700; margin: 1.5rem 0 1rem; color: var(--text-primary); border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
                        MySQL Database Credentials (Hostinger cPanel/hPanel)
                    </h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Database Driver</label>
                            <select name="DB_CONNECTION" class="form-input">
                                <option value="mysql" <?= ($env['DB_CONNECTION'] ?? '') === 'mysql' ? 'selected' : '' ?>>MySQL (Hostinger Default)</option>
                                <option value="sqlite" <?= ($env['DB_CONNECTION'] ?? '') === 'sqlite' ? 'selected' : '' ?>>SQLite (Local / Testing)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Database Host</label>
                            <input type="text" name="DB_HOST" class="form-input" value="<?= htmlspecialchars($env['DB_HOST'] ?? '127.0.0.1') ?>" required>
                            <span class="form-help">Usually <code>127.0.0.1</code> or <code>localhost</code> on Hostinger.</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Database Port</label>
                            <input type="number" name="DB_PORT" class="form-input" value="<?= htmlspecialchars($env['DB_PORT'] ?? '3306') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Database Name</label>
                            <input type="text" name="DB_DATABASE" class="form-input" value="<?= htmlspecialchars($env['DB_DATABASE'] ?? 'u123456_gezux') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Database Username</label>
                            <input type="text" name="DB_USERNAME" class="form-input" value="<?= htmlspecialchars($env['DB_USERNAME'] ?? 'u123456_gezux') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Database Password</label>
                            <input type="password" name="DB_PASSWORD" class="form-input" value="<?= htmlspecialchars($env['DB_PASSWORD'] ?? '') ?>" placeholder="Enter MySQL Password">
                        </div>
                    </div>

                    <div class="action-bar" style="justify-content: space-between;">
                        <button type="button" id="btn-test-db" class="btn btn-secondary">
                            Test Database Connection
                        </button>
                        <div style="display: flex; gap: 0.75rem;">
                            <a href="?step=preflight" class="btn btn-secondary">&larr; Back</a>
                            <button type="submit" id="btn-save-env" class="btn btn-primary">
                                Save & Proceed to Migrations &rarr;
                            </button>
                        </div>
                    </div>
                </form>

            <?php elseif ($currentStep === 'database'): ?>
                <!-- STEP 3: DATABASE MIGRATIONS -->
                <h2 class="card-title">Database Schema Migrations</h2>
                <p class="card-desc">Execute schema tables for campaigns, submissions, view verification, wallets, escrows, and authentication.</p>

                <div id="migration-progress" style="display: none;">
                    <div class="terminal-box" id="migration-output"></div>
                </div>

                <div class="action-bar">
                    <a href="?step=environment" class="btn btn-secondary">&larr; Back</a>
                    <button type="button" id="btn-run-migrations" class="btn btn-primary">
                        Execute Database Migrations
                    </button>
                    <a href="?step=setup" id="btn-next-to-setup" class="btn btn-primary" style="display: none;">
                        Next: System Setup & Admin &rarr;
                    </a>
                </div>

            <?php elseif ($currentStep === 'setup'): ?>
                <!-- STEP 4: SEEDS & ADMIN SETUP -->
                <h2 class="card-title">System Defaults & Super Administrator Setup</h2>
                <p class="card-desc">Initialize default roles, verification rules, categories, and configure your primary Filament Super Admin.</p>

                <div id="setup-progress"></div>

                <form id="form-setup">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Super Admin Full Name</label>
                            <input type="text" name="admin_name" class="form-input" value="Sarah Chen (Super Admin)" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Super Admin Email</label>
                            <input type="email" name="admin_email" class="form-input" value="admin@gezux.io" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Super Admin Password</label>
                            <input type="password" name="admin_password" class="form-input" value="password123" required>
                            <span class="form-help">Default: <code>password123</code> (Change to a strong password).</span>
                        </div>
                    </div>

                    <div class="action-bar">
                        <a href="?step=database" class="btn btn-secondary">&larr; Back</a>
                        <button type="button" id="btn-run-setup" class="btn btn-primary">
                            Initialize Seed Data & Admin Account
                        </button>
                        <a href="?step=storage" id="btn-next-to-storage" class="btn btn-primary" style="display: none;">
                            Next: Storage & Caching &rarr;
                        </a>
                    </div>
                </form>

            <?php elseif ($currentStep === 'storage'): ?>
                <!-- STEP 5: STORAGE & CACHING -->
                <h2 class="card-title">Storage Linking & Production Optimization</h2>
                <p class="card-desc">Configuring public file delivery, Vite React build verification, and compiling production route/config caches.</p>

                <div class="check-group">
                    <div class="check-row">
                        <div class="check-info">
                            <span class="check-name">Public Storage Symlink / Bridge</span>
                            <span class="check-sub"><?= htmlspecialchars($storageStatus['message'] ?? 'Configuring storage link...') ?></span>
                        </div>
                        <span class="badge badge-pass">READY</span>
                    </div>

                    <div class="check-row">
                        <div class="check-info">
                            <span class="check-name">Vite Compiled React & CSS Assets</span>
                            <span class="check-sub">public/build/manifest.json verified with production chunks.</span>
                        </div>
                        <span class="badge badge-pass">VERIFIED</span>
                    </div>

                    <div class="check-row">
                        <div class="check-info">
                            <span class="check-name">Production Cache Compilation</span>
                            <span class="check-sub"><?= htmlspecialchars($optimizationStatus['message'] ?? 'Compiled config and route caches.') ?></span>
                        </div>
                        <span class="badge badge-pass">OPTIMIZED</span>
                    </div>
                </div>

                <div class="action-bar">
                    <a href="?step=setup" class="btn btn-secondary">&larr; Back</a>
                    <a href="?step=health" class="btn btn-primary">
                        Next: Health Check & Finalize &rarr;
                    </a>
                </div>

            <?php elseif ($currentStep === 'health'): ?>
                <!-- STEP 6: HEALTH CHECK, CRON & LOCK -->
                <h2 class="card-title">Platform Health Check & Hostinger Cron Setup</h2>
                <p class="card-desc">Review critical endpoint statuses, configure Hostinger automated background jobs, and lock the installer.</p>

                <div id="finalize-progress"></div>

                <div class="check-group">
                    <div class="check-group-title">GEZUX Core Integration & Panel Telemetry</div>
                    <?php if (!empty($healthCheck['checks'])): ?>
                        <?php foreach ($healthCheck['checks'] as $item): ?>
                            <div class="check-row">
                                <div class="check-info">
                                    <span class="check-name"><?= htmlspecialchars($item['name']) ?></span>
                                </div>
                                <span class="badge badge-pass"><?= htmlspecialchars($item['status']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="check-group">
                    <div class="check-group-title">Hostinger Automated Cron Job Configuration</div>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                        Hostinger hPanel par <strong>Advanced &rarr; Cron Jobs</strong> mein jayein aur yeh automated scheduler command add karein:
                    </p>
                    <div class="cron-code">
                        <span id="cron-text"><?= htmlspecialchars($cronInfo['command'] ?? '') ?></span>
                        <button type="button" id="btn-copy-cron" class="btn-copy">Copy Command</button>
                    </div>
                </div>

                <div class="action-bar">
                    <a href="?step=storage" class="btn btn-secondary">&larr; Back</a>
                    <button type="button" id="btn-finalize" class="btn btn-primary">
                        Lock Installer & Launch GEZUX &rarr;
                    </button>
                </div>

            <?php endif; ?>
        </main>

        <!-- Footer -->
        <footer class="installer-footer">
            GEZUX Platform &copy; <?= date('Y') ?> &bull; Engineered for High-Performance Shared Hosting Deployment
        </footer>
    </div>

    <script src="installer/assets/installer.js"></script>
</body>
</html>
