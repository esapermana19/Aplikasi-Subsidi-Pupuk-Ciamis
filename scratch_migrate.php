<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Rolling back superadmin...\n";
$kernel->call('migrate:rollback', ['--step' => 1]);
echo $kernel->output();

echo "\nRunning migrations...\n";
$kernel->call('migrate');
echo $kernel->output();
