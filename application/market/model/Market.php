<?php

/**
 * Created by jiangjun on 2019/3/18 21:20
 */

namespace app\market\model;

use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\Model;

class Market extends Model
{
    const COUNT_OF_PAGE = 10;

    /**
     * 注册超市
     * @param $market
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function regMarket($market)
    {
        if (!checkToken($market->token, $market->phone)) {
            return config('NOT_SUPPORTED');
        }
        $user_id = $market->user_id;
        if (Db::table('ym_market')->where(['user_id' => $user_id])->select() != null) {
            $status = Db::table('ym_market')->where(['user_id' => $user_id])
                ->update(['user_id' => $user_id,
                    'market_name' => $market->market_name,
                    'market_school' => $market->market_school,
                    'dorm_tower' => $market->dorm_tower,
                    'dorm_num' => $market->dorm_num,
                    'type' => $market->type,
                    'add_date' => date('Y-m-d H:i:s', time())]);
            if ($status > 0) {
                return ['status' => 200, 'msg' => '更新成功！！'];
            }
            return ['status' => 400, 'msg' => '更新失败！！'];
        }
        if ($this->save(['user_id' => $user_id,
                'market_name' => $market->market_name,
                'market_school' => $market->market_school,
                'dorm_tower' => $market->dorm_tower,
                'dorm_num' => $market->dorm_num,
                'type' => $market->type,
                'add_date' => date('Y-m-d H:i:s', time())]
        )) {
            return ['status' => 200, 'msg' => '注册成功！！'];
        }
        return ['status' => 400, 'msg' => '注册失败！！'];
    }

    /**
     * 通过手机号获取是否为商家
     * @param $id
     * @return array
     */
    public function findOfPhone($id)
    {
        $merchant = Db::table('ym_user')->where(['user_id' => $id])->value('merchant');
        $market_id = Db::table('ym_market')->where(['user_id' => $id])->value('market_id');
        if ($merchant >= 0) {
            return ['status' => 200, 'msg' => '查询成功！！', 'merchant' => $merchant, 'market_id' => $market_id];
        }
        return ['status' => 400, 'msg' => '查询失败！！', 'merchant' => $merchant, 'market_id' => $market_id];
    }

    /**
     * @param $page
     * @param $order
     * @param $type
     * @param $sale_volume
     * @param $market_school
     * @return array
     */
    public function getMarketList($page, $order, $type, $sale_volume, $market_school)
    {
        if ($sale_volume == 1) {
            $sale_volume = 'DESC';
        } else {
            $sale_volume = 'ASC';
        }
        if ($type == "全部") {
            $totalPages = ceil(Db::table('ym_market')
                    ->where(['market_school' => $market_school, 'status' => 1])
                    ->count('*') / Market::COUNT_OF_PAGE);
        } else {
            $totalPages = ceil(Db::table('ym_market')
                    ->where(['market_school' => $market_school, 'type' => $type, 'status' => 1])
                    ->count('*') / Market::COUNT_OF_PAGE);
        }
        try {
            switch ($order) {
                case "综合":
                    if ($type == "全部") {
                        $marketList = Db::table('ym_market')
                            ->field('*,star_level+reading_volume as num')
                            ->where(['market_school' => $market_school, 'status' => 1])
                            ->order('num', 'DESC')
                            ->order('sale_volume', $sale_volume)
                            ->page($page, 10)
                            ->select();
                        for ($i = 0; $i < count($marketList); $i++) {
                            $marketList[$i]["notice"] = explode("|", $marketList[$i]["notice"]);
                        }

                    } else {
                        $marketList = Db::table('ym_market')
                            ->field('*,star_level+reading_volume as num')
                            ->where(['market_school' => $market_school, 'type' => $type, 'status' => 1])
                            ->order('num', 'DESC')
                            ->order('sale_volume', $sale_volume)
                            ->page($page, 10)
                            ->select();
                        for ($i = 0; $i < count($marketList); $i++) {
                            $marketList[$i]["notice"] = explode("|", $marketList[$i]["notice"]);
                        }
                    }
                    return ['status' => 200, 'msg' => '查询成功！！',
                        'marketList' => $marketList, 'totalPages' => $totalPages];
                case "星级":
                    if ($type == "全部") {
                        $marketList = Db::table('ym_market')
                            ->where(['market_school' => $market_school, 'status' => 1])
                            ->order('star_level', 'DESC')
                            ->order('sale_volume', $sale_volume)
                            ->page($page, 10)
                            ->select();
                        for ($i = 0; $i < count($marketList); $i++) {
                            $marketList[$i]["notice"] = explode("|", $marketList[$i]["notice"]);
                        }
                    } else {
                        $marketList = Db::table('ym_market')
                            ->where(['market_school' => $market_school, 'type' => $type, 'status' => 1])
                            ->order('star_level', 'DESC')
                            ->order('sale_volume', $sale_volume)
                            ->page($page, 10)
                            ->select();
                        for ($i = 0; $i < count($marketList); $i++) {
                            $marketList[$i]["notice"] = explode("|", $marketList[$i]["notice"]);
                        }
                    }
                    return ['status' => 200, 'msg' => '查询成功！！',
                        'marketList' => $marketList, 'totalPages' => $totalPages];
                case "人气":
                    if ($type == "全部") {
                        $marketList = Db::table('ym_market')
                            ->where(['market_school' => $market_school, 'status' => 1])
                            ->order('reading_volume', 'DESC')
                            ->order('sale_volume', $sale_volume)
                            ->page($page, 10)
                            ->select();
                        for ($i = 0; $i < count($marketList); $i++) {
                            $marketList[$i]["notice"] = explode("|", $marketList[$i]["notice"]);
                        }
                    } else {
                        $marketList = Db::table('ym_market')
                            ->where(['market_school' => $market_school, 'type' => $type, 'status' => 1])
                            ->order('reading_volume', 'DESC')
                            ->order('sale_volume', $sale_volume)
                            ->page($page, 10)
                            ->select();
                        for ($i = 0; $i < count($marketList); $i++) {
                            $marketList[$i]["notice"] = explode("|", $marketList[$i]["notice"]);
                        }
                    }
                    return ['status' => 200, 'msg' => '查询成功！！',
                        'marketList' => $marketList, 'totalPages' => $totalPages];
                default:
                    break;
            }
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
        return ['status' => 400, 'msg' => '查询失败！！'];
    }
}