<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$newPassword = password_hash('Admin@1234', PASSWORD_BCRYPT);
DB::table('users')->where('email', 'admin@admin.com')->update(['password' => $newPassword]);
DB::table('users')->where('email', 'redrose.helpdesk21@gmail.com')->update(['password' => $newPassword]);

echo json_encode(['status' => 'done', 'password' => 'Admin@1234']);
