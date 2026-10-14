<?php

// ─── Error Logger ────────────────────────────────────────────────────────────
function logRouteError($type, $detail, $extra = '') {
    $logFile = __DIR__ . '/logs/route_errors.log';
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0755, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    $ip        = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $line      = "[$timestamp] [$type] $detail | IP: $ip" . ($extra ? " | $extra" : '') . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

// ─── PHP error handler — catches fatal/notice/warning from included files ────
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $types = [E_ERROR=>'ERROR', E_WARNING=>'WARNING', E_NOTICE=>'NOTICE', E_PARSE=>'PARSE'];
    $label = $types[$errno] ?? "ERR($errno)";
    logRouteError("PHP_$label", $errstr, "$errfile:$errline");
    return false; // Let PHP also handle it normally
});

register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        logRouteError('PHP_FATAL', $err['message'], $err['file'] . ':' . $err['line']);
    }
});

// ─── Helper: include a file and verify it exists first ───────────────────────
function safeInclude($file, $req = '') {
    $path = __DIR__ . '/' . $file;
    if (!file_exists($path)) {
        logRouteError('MISSING_FILE', "File not found: $file", "req=$req");
        include __DIR__ . '/404.php';
        return;
    }
    include $path;
}

// ─── Route resolution ────────────────────────────────────────────────────────
$request = $_SERVER['REQUEST_URI'];
// Strip query string
$request = strtok($request, '?');
$request = trim($request, '/');

$dynamicRoutes = [
    'signin'         => 'signin.php',
    'dashboard'      => 'dashboard.php',
    'web'            => 'web.php',
    'support'        => 'support.php',
    'withdraw'       => 'withdraw.php',
    'history'        => 'history.php',
    'admin_deposits' => 'admin_deposits.php',
];

$matched = false;

// ─── Dynamic routes ──────────────────────────────────────────────────────────
foreach ($dynamicRoutes as $prefix => $file) {
    if (preg_match('/^' . preg_quote($prefix, '/') . '\/([a-zA-Z0-9\$\.,`>\?_-]+)$/', $request, $matches)) {
        $req     = $matches[1];
        $matched = true;
        logRouteError('ROUTE_HIT', "Dynamic: /$prefix/$req → $file"); // optional: remove in prod
        safeInclude($file, $req);
        break;
    }
}

// ─── Static routes ───────────────────────────────────────────────────────────
if (!$matched) {
    $staticRoutes = [
        ''                => 'home.html',
        'signup'          => 'signup.php',
        'smart'           => 'smartseo.html',
        'admin'           => 'admin.php',
        'messenger'       => 'messenger.php',
        'profits'         => 'profits.php',
        'user_management' => 'user_management.php',
        'about'           => 'about.php',
        'service'         => 'service.php',
        'contact'         => 'contact.php',
    ];

    if (array_key_exists($request, $staticRoutes)) {
        $matched = true;
        safeInclude($staticRoutes[$request], $request);
    }
}

// ─── 404 fallback ─────────────────────────────────────────────────────────────
if (!$matched) {
    logRouteError('404', "No route matched: /$request");
    http_response_code(404);
    safeInclude('404.php', $request);
}