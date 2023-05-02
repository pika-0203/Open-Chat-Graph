<?php

namespace App\Services\Crawler;

trait TraitRandomUserAgent
{
    private function getRandomAndroidUserAgent() :string
    {
        $ua = 'Mozilla/5.0 (Linux; Android 11';
        $brands = ['Samsung', 'Google', 'Xiaomi', 'OnePlus'];
        $rand_brand = $brands[array_rand($brands)];
        $models = ['SM-G970', 'Pixel 5', 'Mi 10T', 'ONEPLUS A6013'];
        $rand_model = $models[array_rand($models)];
        $ua .= "; {$rand_brand} {$rand_model}) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36";
        return $ua;
    }
}