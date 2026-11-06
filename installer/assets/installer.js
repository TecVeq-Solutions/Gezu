/**
 * GEZUX Hostinger Web Installer Client
 * Lightweight step navigation, async execution, connection testing, and error resilience.
 */

document.addEventListener('DOMContentLoaded', () => {
  const formEnv = document.getElementById('form-env');
  const btnTestDb = document.getElementById('btn-test-db');
  const dbTestResult = document.getElementById('db-test-result');
  const btnRunMigrations = document.getElementById('btn-run-migrations');
  const migrationProgress = document.getElementById('migration-progress');
  const migrationOutput = document.getElementById('migration-output');
  const btnRunSetup = document.getElementById('btn-run-setup');
  const setupProgress = document.getElementById('setup-progress');
  const btnFinalize = document.getElementById('btn-finalize');
  const finalizeProgress = document.getElementById('finalize-progress');

  // Copy Cron Command Handler
  const btnCopyCron = document.getElementById('btn-copy-cron');
  if (btnCopyCron) {
    btnCopyCron.addEventListener('click', () => {
      const cronText = document.getElementById('cron-text').innerText;
      navigator.clipboard.writeText(cronText).then(() => {
        btnCopyCron.innerText = 'Copied! ✓';
        setTimeout(() => { btnCopyCron.innerText = 'Copy Command'; }, 2000);
      });
    });
  }

  // Safe JSON Parse Helper
  async function parseResponse(res) {
    const raw = await res.text();
    try {
      return JSON.parse(raw);
    } catch (e) {
      const temp = document.createElement('div');
      temp.innerHTML = raw;
      const text = temp.textContent || temp.innerText || raw;
      return {
        success: false,
        message: text.trim().substring(0, 1500) || 'Server returned non-JSON response.',
      };
    }
  }

  // 1. Test Database Connection
  if (btnTestDb) {
    btnTestDb.addEventListener('click', async () => {
      btnTestDb.disabled = true;
      btnTestDb.innerText = 'Testing Connection...';
      dbTestResult.innerHTML = '<div class="alert alert-info">Connecting to MySQL server...</div>';

      const formData = new FormData(formEnv);
      formData.append('action', 'test_db');

      try {
        const res = await fetch('?action=test_db', {
          method: 'POST',
          body: formData,
        });
        const data = await parseResponse(res);

        if (data.success) {
          dbTestResult.innerHTML = `<div class="alert alert-success">✓ ${data.message}</div>`;
          document.getElementById('btn-save-env').disabled = false;
        } else {
          dbTestResult.innerHTML = `
            <div class="alert alert-danger">
              <div>
                <strong>${data.category || 'Connection Error'}:</strong> ${data.message}
                ${data.remediation ? `<div style="margin-top: 0.35rem; font-size: 0.775rem;"><strong>Remediation:</strong> ${data.remediation}</div>` : ''}
              </div>
            </div>
          `;
        }
      } catch (err) {
        dbTestResult.innerHTML = `<div class="alert alert-danger">Server communication error: ${err.message}</div>`;
      } finally {
        btnTestDb.disabled = false;
        btnTestDb.innerText = 'Test Database Connection';
      }
    });
  }

  // 2. Run Migrations Async
  if (btnRunMigrations) {
    btnRunMigrations.addEventListener('click', async () => {
      btnRunMigrations.disabled = true;
      btnRunMigrations.innerText = 'Migrating Database Schema...';
      migrationProgress.style.display = 'block';
      migrationOutput.innerText = 'Booting Laravel kernel and preparing migration transactions...\n';

      const formData = new FormData();
      formData.append('action', 'migrate');
      formData.append('csrf_token', window.GEZUX_CSRF || '');

      try {
        const res = await fetch('?action=migrate', {
          method: 'POST',
          body: formData,
        });
        const data = await parseResponse(res);

        if (data.success) {
          migrationOutput.innerText += (data.output || 'All tables created successfully.') + '\n✓ Migration Complete!\n';
          document.getElementById('btn-next-to-setup').style.display = 'inline-flex';
          btnRunMigrations.style.display = 'none';
        } else {
          migrationOutput.innerText += '\n❌ ERROR: ' + data.message + '\n' + (data.trace || '');
          btnRunMigrations.disabled = false;
          btnRunMigrations.innerText = 'Retry Migrations';
        }
      } catch (err) {
        migrationOutput.innerText += '\n❌ Fetch Error: ' + err.message;
        btnRunMigrations.disabled = false;
        btnRunMigrations.innerText = 'Retry Migrations';
      }
    });
  }

  // 3. Run System Setup & Seed
  if (btnRunSetup) {
    btnRunSetup.addEventListener('click', async () => {
      btnRunSetup.disabled = true;
      btnRunSetup.innerText = 'Initializing System Defaults...';
      setupProgress.style.display = 'block';

      const formSetup = document.getElementById('form-setup');
      const formData = new FormData(formSetup);
      formData.append('action', 'seed_setup');
      formData.append('csrf_token', window.GEZUX_CSRF || '');

      try {
        const res = await fetch('?action=seed_setup', {
          method: 'POST',
          body: formData,
        });
        const data = await parseResponse(res);

        if (data.success) {
          setupProgress.innerHTML = `<div class="alert alert-success">✓ ${data.message}</div>`;
          document.getElementById('btn-next-to-storage').style.display = 'inline-flex';
          btnRunSetup.style.display = 'none';
        } else {
          setupProgress.innerHTML = `<div class="alert alert-danger">❌ ${data.message}</div>`;
          btnRunSetup.disabled = false;
          btnRunSetup.innerText = 'Retry Setup';
        }
      } catch (err) {
        setupProgress.innerHTML = `<div class="alert alert-danger">❌ Fetch Error: ${err.message}</div>`;
        btnRunSetup.disabled = false;
        btnRunSetup.innerText = 'Retry Setup';
      }
    });
  }

  // 4. Finalize & Lock Installation
  if (btnFinalize) {
    btnFinalize.addEventListener('click', async () => {
      btnFinalize.disabled = true;
      btnFinalize.innerText = 'Locking & Finalizing Platform...';
      finalizeProgress.style.display = 'block';
      finalizeProgress.innerHTML = '<div class="alert alert-info">Clearing temporary setup artifacts and compiling production caches...</div>';

      const formData = new FormData();
      formData.append('action', 'finalize');
      formData.append('csrf_token', window.GEZUX_CSRF || '');

      try {
        const res = await fetch('?action=finalize', {
          method: 'POST',
          body: formData,
        });
        const data = await parseResponse(res);

        if (data.success) {
          finalizeProgress.innerHTML = `<div class="alert alert-success">✓ ${data.message} Redirecting to live platform...</div>`;
          setTimeout(() => {
            window.location.href = data.redirect_url || '/';
          }, 1500);
        } else {
          finalizeProgress.innerHTML = `<div class="alert alert-danger">❌ ${data.message}</div>`;
          btnFinalize.disabled = false;
          btnFinalize.innerText = 'Retry Finalize';
        }
      } catch (err) {
        finalizeProgress.innerHTML = `<div class="alert alert-danger">❌ Fetch Error: ${err.message}</div>`;
        btnFinalize.disabled = false;
        btnFinalize.innerText = 'Retry Finalize';
      }
    });
  }
});
