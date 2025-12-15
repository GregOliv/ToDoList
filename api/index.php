<?php

// Fix for Vercel routing where URI segments are stripped relative to script location
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Redirect to public folder
require __DIR__ . '/../public/index.php';
