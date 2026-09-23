<?php
// Session Security Hardening
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

define('BASE_URL', 'http://localhost/05092026/SmartWarehouse');
define('APP_NAME', 'SmartWarehouse');
define('GEMINI_API_KEY', 'AIzaSyDWYFOl70bJti-3SCHXd7tSBCqP4sx54Mw');