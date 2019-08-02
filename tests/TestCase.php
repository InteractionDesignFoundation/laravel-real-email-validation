<?php

namespace IDF\RealEmailValidation\Tests;

use IDF\RealEmailValidation\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Contracts\Foundation\Application;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders(Application $app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
