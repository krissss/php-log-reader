<?php

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Kriss\LogReader\LogReader;

class LogReaderServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton('log-reader', function (Application $app) {
            $config = $app['config']['log_reader'];
            $logPath = $config['logPath'];
            unset($config['logPath']);
            return new LogReader($logPath, $config);
        });

        $this->app->alias('log-reader', LogReader::class);
    }

    public function provides()
    {
        return [
            'log-reader',
            LogReader::class,
        ];
    }
}