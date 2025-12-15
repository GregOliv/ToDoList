<?php

// Fix for Vercel: Force script name to root index.php
// This prevents Laravel from stripping '/api' from the request URI
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Redirect to public folder
require __DIR__ . '/../public/index.php';
