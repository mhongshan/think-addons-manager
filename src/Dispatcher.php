<?php
declare(strict_types=1);

namespace mhs\think\addons\manager;

use mhs\think\addons\manager\controller\Index;

class Dispatcher
{
    public static function exec($action = null)
    {
        if (empty($action)) {
            $url = explode('/', app()->request->pathinfo());
            $action = array_pop($url);
        }
        $call = [Index::class, $action];

        return invoke($call, []);
    }
}