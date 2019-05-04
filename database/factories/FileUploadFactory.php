<?php

/* @var $factory Factory */

use App\Models\FileUpload;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(FileUpload::class, function (Faker $faker) {
    return [
        'uuid' => $faker->uuid,
        'path' => "{$faker->uuid}.{$faker->fileExtension}",
        'expires_at' => Carbon::now()->addHour(),
    ];
});

$factory->state(FileUpload::class, 'expired', function () {
    return [
        'expires_at' => Carbon::now()->subMinute(),
    ];
});
