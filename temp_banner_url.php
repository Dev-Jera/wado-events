<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$path = 'hero-banners/01KQ6QQ4A0ZDXSQJNEM1Q7YC82.jpg';

echo 'public_path_exists=' . (file_exists(public_path('storage/' . $path)) ? 'yes' : 'no') . PHP_EOL;
echo 'storage_path_exists=' . (file_exists(storage_path('app/public/' . $path)) ? 'yes' : 'no') . PHP_EOL;
echo 'storage_url=' . Illuminate\Support\Facades\Storage::disk('public')->url($path) . PHP_EOL;
echo 'asset_url=' . asset('storage/' . $path) . PHP_EOL;
