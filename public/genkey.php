<?php
use Illuminate\Support\Facades\Crypt;
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$customKey = 'Aabmn@!0171#Asha@Bizli#RRBC1234';
$value = 'ThisisanencryptedpublickeyforRedRoseWebApplication';

// Use Laravel default encryption (APP_KEY based)
$encrypted = Crypt::encryptString($value);
echo $encrypted;
