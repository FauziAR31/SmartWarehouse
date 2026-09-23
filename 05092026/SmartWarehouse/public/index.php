<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../helpers/SecurityHelper.php';
require_once '../routes/web.php';

// Initialize core router
$app = new Router();
