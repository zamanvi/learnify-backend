<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

$newPassword = 'Admin@1234';
$hashed = Hash::make($newPassword);

DB::table('users')->where('user_type', '1')->update(['password' => $hashed]);

$admins = DB::table('users')->where('user_type', '1')->get(['id','name','email']);
echo json_encode(['new_password' => $newPassword, 'admins_updated' => $admins]);
