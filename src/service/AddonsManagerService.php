<?php
declare(strict_types=1);

namespace mhs\think\addons\manager\service;

use mhs\think\addons\manager\command\Init;
use think\Service;

class AddonsManagerService extends Service
{
    public function register()
    {
        $file = $this->app->getRootPath().'route/addons-manager.php';
        if (!file_exists($file)) {
            copy(__DIR__.'/../route.php', $file);
        }
    }

    public function boot()
    {
        $this->commands([
            Init::class
        ]);
    }
}