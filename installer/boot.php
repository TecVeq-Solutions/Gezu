<?php

/**
 * GEZUX Hostinger Web Installer Bootstrap & Router
 * Zero external dependencies. Dispatches steps, AJAX endpoints, and UI views.
 */

namespace Gezux\Installer;

require_once __DIR__ . '/PreflightChecker.php';
require_once __DIR__ . '/InstallerManager.php';

// Centralized Single-Root Path Resolution (public_html root)
$appPath = $appPath ?? realpath(__DIR__ . '/..');
$publicPath = $publicPath ?? realpath(__DIR__ . '/..');

$manager = new InstallerManager($appPath, $publicPath);
$preflightChecker = new PreflightChecker($appPath, $publicPath);


// If already installed and locked, exit gracefully
if ($manager->isInstalled()) {
    header('Location: /');
    exit;
}

$csrfToken = $manager->startSession();

// AJAX Action Routing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start();
    header('Content-Type: application/json; charset=utf-8');

    try {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        $token = $_POST['csrf_token'] ?? '';

        // Test DB action can be run during step 2
        if ($action === 'test_db') {
            $result = $manager->testDatabaseConnection($_POST);
            ob_end_clean();
            echo json_encode($result);
            exit;
        }

        if (!$manager->validateCsrf($token)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'CSRF verification failed. Please refresh the page.']);
            exit;
        }

        if ($action === 'save_env') {
            $saveResult = $manager->saveEnvValues($_POST);
            if ($saveResult['success']) {
                ob_end_clean();
                header('Location: ?step=database');
                exit;
            } else {
                ob_end_clean();
                echo json_encode($saveResult);
                exit;
            }
        }

        if ($action === 'migrate') {
            $result = $manager->runMigrations();
            ob_end_clean();
            echo json_encode($result);
            exit;
        }

        if ($action === 'seed_setup') {
            $result = $manager->runSeedAndSetup($_POST);
            ob_end_clean();
            echo json_encode($result);
            exit;
        }

        if ($action === 'finalize') {
            $manager->optimizeCaches();
            $lockResult = $manager->lockInstallation();
            ob_end_clean();
            echo json_encode(array_merge($lockResult, ['redirect_url' => '/']));
            exit;
        }

        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unknown installation action.']);
        exit;
    } catch (Throwable $e) {
        $buffered = ob_get_clean();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage() . ($buffered ? ' (' . substr(trim($buffered), 0, 300) . ')' : ''),
            'trace' => $e->getTraceAsString(),
        ]);
        exit;
    }
}


// GET Step Router
$currentStep = $_GET['step'] ?? 'preflight';
$stepMap = [
    'preflight' => 1,
    'environment' => 2,
    'database' => 3,
    'setup' => 4,
    'storage' => 5,
    'health' => 6,
];
$stepIndex = $stepMap[$currentStep] ?? 1;

$preflight = $preflightChecker->checkAll();
$env = $manager->getEnvValues();
$storageStatus = [];
$optimizationStatus = [];
$healthCheck = [];
$cronInfo = [];

if ($currentStep === 'storage') {
    $storageStatus = $manager->setupStorage();
    $optimizationStatus = $manager->optimizeCaches();
}

if ($currentStep === 'health') {
    $healthCheck = $manager->runHealthCheck();
    $cronInfo = $manager->getCronCommand();
}

// Render Main Template View
require __DIR__ . '/views/layout.php';
