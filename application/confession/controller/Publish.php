<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/18
 * Time: 16:13
 */

namespace app\confession\controller;


use app\confession\model\ConfessionImage;
use think\Controller;
use think\Request;

class Publish extends Controller
{


    /**
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function upload(Request $request)
    {
        if ($request->isPost()) {
            $Upload = new ConfessionImage();
            $files = $request->file('file');
            $phone = $request->param('phone');
            $content = $request->param('content');
            $token = $request->param('token');
            return $Upload->uploadArticle($files, $content, $token, $phone);
        }
        return [config('PARAMS_ERROR')];

    }
}