<?php

use think\facade\Route;

Route::group('addons/manger', function(){
    $execute = '\\mhs\\think\\addons\\manager\\Dispatcher@exec';
    Route::get('index', $execute); // 插件列表页
    Route::get('info', $execute); // 插件详情页
    Route::post('install', $execute); // 插件安装
    Route::post('uninstall', $execute); // 插件卸载
    Route::post('enable', $execute); // 插件启用
    Route::post('disable', $execute); // 插件禁用
    Route::post('upgrade', $execute); // 插件升级
});