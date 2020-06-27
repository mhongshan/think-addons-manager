<?php
declare(strict_types=1);

namespace mhs\think\addons\manager\services;

use mhs\think\Addons;
use mhs\think\libs\Path;
use think\Exception;
use think\helper\Str;
use think\paginator\driver\Bootstrap;

class ManagerService
{
    /**
     * @var Addons
     */
    protected $addons;

    public function __construct()
    {
        /** @var Addons $path */
        $this->addons = app()->addons;
    }

    public function lists($params = [])
    {
        /** @var Path $path */
        $path = $this->addons->getPath();
        $addons = glob($path->getAddonsPath().'*/*', GLOB_ONLYDIR);
        $lists = [];
        foreach($addons as $type => $addon) {
            if (!empty($params['type']) && $params['type'] != $type) {
                continue;
            }
            if (!empty($params['name']) && !Str::contains($addon, $params['name'])) {
                continue;
            }
            $info = $addon.'/info.php';
            if (!file_exists($info)) {
                continue;
            }
            $info = include "$info";
            $info['addon_type'] = $type;
            $lists[] = $info;
        }
        $pageSize = empty($params['page_size']) ? 10 : max((int)$params['page_zie'], 10);
        $page = empty($params['page']) ? 1 : max(1, (int)$params['page']);
        $index = $page-1;
        $lists = array_chunk($lists, $pageSize);
        if (!isset($lists[$index])) {
            $result = new Bootstrap([], $pageSize, $page, 0);
        } else {
            $result = new Bootstrap($lists[$index], $pageSize, $page, count($lists));
        }

        return $this->success($result, '查询成功');
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function info($params)
    {
        $type = $params['type'];
        $name = $params['name'];
        $path = $this->getAddonPath($type, $name);
        $info = $path . 'info.php';
        if (!file_exists($info)) {
            return $this->failed('插件不存在');
        }
        $info = include($info);
        $info['addons_type'] = $type;

        return $this->success($info);
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function install($params)
    {
        $info = $this->info($params);
        if (!$info['result']) {
            return $info;
        }
        $info = $info['data'];
        if ($info['status'] != 0) {
            return $this->failed('插件已安装，请勿重复安装');
        }
        if (!$class = get_addons_instance($params['name'], $params['type'])) {
            return $this->failed('插件不完整,缺少Plugin文件');
        }
        // 执行安装
        if (!$class->install()) { // 安装失败
            return $this->failed('插件安装失败');
        }

        return $this->success('插件安装成功');
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function uninstall($params)
    {
        $info = $this->info($params);
        if (!$info['result']) {
            return $info;
        }
        $info = $info['data'];
        if ($info['status'] == 0) {
            return $this->failed('插件未安装');
        }
        if (!$class = get_addons_instance($params['name'], $params['type'])) {
            return $this->failed('插件不完整,缺少Plugin文件');
        }
        // 执行卸载
        if (!$class->uninstall()) { // 卸载失败
            return $this->failed('插件卸载失败');
        }

        return $this->success('插件卸载成功');
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function enable($params)
    {
        $info = $this->info($params);
        if (!$info['result']) {
            return $info;
        }
        $info = $info['data'];
        if ($info['status'] != 2) {
            return $this->failed('插件未安装或已启用');
        }
        if (!$class = get_addons_instance($params['name'], $params['type'])) {
            return $this->failed('插件不完整,缺少Plugin文件');
        }
        // 执行启用
        if (!$class->enable()) { // 启用失败
            return $this->failed('插件启用失败');
        }

        return $this->success('插件启用成功');
    }

    public function disable($params)
    {
        $info = $this->info($params);
        if (!$info['result']) {
            return $info;
        }
        $info = $info['data'];
        if ($info['status'] != 1) {
            return $this->failed('插件未安装或未启用');
        }
        if (!$class = get_addons_instance($params['name'], $params['type'])) {
            return $this->failed('插件不完整,缺少Plugin文件');
        }
        // 执行禁用
        if (!$class->disable()) { // 禁用失败
            return $this->failed('插件禁用失败');
        }

        return $this->success('插件禁用成功');
    }

    /**
     * @param $params
     * @return array
     */
    public function upgrade($params)
    {
        return $this->failed('暂未实现');
    }

    /**
     * @param $type
     * @param $addon
     * @return string
     * @throws Exception
     */
    protected function getAddonPath($type, $addon)
    {
        switch ($type) {
            case 'apps':
                $path = $this->addons->getPath()->getAppsPath();
                break;
            case 'plugins':
                $path = $this->addons->getPath()->getPluginsPath();
                break;
            default:
                throw new Exception('插件类型错误');
                break;
        }

        return $path . strtr($addon, '_', '-') . '/';
    }

    protected function failed($msg = 'failed')
    {
        return [
            'result' => false,
            'msg' => $msg,
            'data' => [],
        ];
    }
    protected function success($data = [], $msg = 'success')
    {
        return [
            'result' => true,
            'msg' => $msg,
            'data' => $data,
        ];
    }

}