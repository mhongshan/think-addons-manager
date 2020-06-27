<?php
declare(strict_types=1);

namespace mhs\think\addons\manager\controller;

use mhs\think\addons\manager\services\ManagerService;
use think\Request;
use think\response\Json;

class Index
{
    /**
     * @param Request $request
     * @param ManagerService $service
     * @return Json
     */
    public function index(Request $request, ManagerService $service)
    {
        $params = $request->get();
        $result = $service->lists($params);

        return $this->resp($result);
    }

    /**
     * @param $result
     * @return Json
     */
    protected function resp($result)
    {
        $resp = [
            'code' => $result['result'] ? 200 : -1,
            'msg' => $result['msg'],
            'data' => $result['data'] ?? []
        ];

        return json($resp);
    }

    /**
     * @param Request $request
     * @param ManagerService $service
     * @return Json
     */
    public function info(Request $request, ManagerService $service)
    {
        $params = $request->get();
        $result = $service->info($params);

        return $this->resp($result);
    }

    /**
     * @param Request $request
     * @param ManagerService $service
     * @return Json
     */
    public function install(Request $request, ManagerService $service)
    {
        $params = $request->post();
        $result = $service->install($params);

        return $this->resp($result);
    }

    /**
     * @param Request $request
     * @param ManagerService $service
     * @return Json
     */
    public function uninstall(Request $request, ManagerService $service)
    {
        $params = $request->post();
        $result = $service->uninstall($params);

        return $this->resp($result);
    }

    /**
     * @param Request $request
     * @param ManagerService $service
     * @return Json
     */
    public function enable(Request $request, ManagerService $service)
    {
        $params = $request->post();
        $result = $service->enable($params);

        return $this->resp($result);
    }

    /**
     * @param Request $request
     * @param ManagerService $service
     * @return Json
     */
    public function disable(Request $request, ManagerService $service)
    {
        $params = $request->post();
        $result = $service->disable($params);

        return $this->resp($result);
    }

    /**
     * @param Request $request
     * @param ManagerService $service
     * @return Json
     */
    public function upgrade(Request $request, ManagerService $service)
    {
        $params = $request->post();
        $result = $service->upgrade($params);

        return $this->resp($result);
    }
}