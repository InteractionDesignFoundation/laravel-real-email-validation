<?php

namespace IDF\RealEmailValidation\Tests;

use IDF\RealEmailValidation\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders(Application $app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
