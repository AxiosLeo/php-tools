<?php
// +----------------------------------------------------------------------
// | TPR [ Design For Api Develop ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2017 http://hanxv.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axios <axioscros@aliyun.com>
// +----------------------------------------------------------------------

namespace axios\tpr\service;

class ToolService {
    public static function uuid($salt=''){
        return md5($salt.uniqid(md5(microtime(true)),true));
    }

    public static function uuidAddFlavour($salt='',$cut=8,$flavour='-',$isUpper=false){
        $str = self::uuid($salt);
        $len = strlen($str);$length = $len;$uuid='';
        if(is_array($cut)){
            while ($length>0){
                $uuid .= substr($str,$len-$length,array_rand($cut)).$flavour;
                $length -=$cut;
            }
        }else if(is_int($cut)){
            $step = 0;
            while ($length>0){
                $temp = substr($str,$len-$length,$cut);
                $uuid .= $step!=0 ? $flavour.$temp:$temp;
                $length -=$cut;
                $step++;
            }
        }
        return $isUpper?strtoupper($uuid):$uuid;
    }

    public static function token($salt=''){
        $str = md5($salt.uniqid(md5(microtime(true)),true));
        return $str;
    }

    public static function identity($identity=0){
        $shm = ftok(__FILE__, 'h');
        $shm_id = shmop_open($shm,'c',0644,1);
        if($identity===0){
            return shmop_read($shm_id,0,1);
        }
        shmop_write($shm_id,$identity,0);
        return $identity;
    }

    /**
     * 获取客户端IP地址
     * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public static function getClientIp($type = 0, $adv = false) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * 数组排序
     * @param $array
     * @param string $sortRule
     * @param string $order
     * @return mixed
     */
    public static function arraySort($array,$sortRule="",$order="asc"){
        /**
         * $array = [
         *              ["book"=>10,"version"=>10],
         *              ["book"=>19,"version"=>30],
         *              ["book"=>10,"version"=>30],
         *              ["book"=>19,"version"=>10],
         *              ["book"=>10,"version"=>20],
         *              ["book"=>19,"version"=>20]
         *      ];
         */
        if(is_array($sortRule)){
            /**
             * $sortRule = ['book'=>"asc",'version'=>"asc"];
             */
            usort($array, function ($a, $b) use ($sortRule) {
                foreach($sortRule as $sortKey => $order){
                    if($a[$sortKey] == $b[$sortKey]){continue;}
                    return (($order == 'desc')?-1:1) * (($a[$sortKey] < $b[$sortKey]) ? -1 : 1);
                }
                return 0;
            });
        }else if(is_string($sortRule) && !empty($sortRule)){
            /**
             * $sortRule = "book";
             * $order = "asc";
             */
            usort($array,function ($a,$b) use ($sortRule,$order){
                if($a[$sortRule] == $b[$sortRule]){
                    return 0;
                }
                return (($order == 'desc')?-1:1) * (($a[$sortRule] < $b[$sortRule]) ? -1 : 1);
            });
        }else{
            usort($array,function ($a,$b) use ($order){
                if($a== $b){
                    return 0;
                }
                return (($order == 'desc')?-1:1) * (($a < $b) ? -1 : 1);
            });
        }
        return $array;
    }

    /**
     * 无限层级的生成树方法
     * @param $data
     * @param string $parent_index
     * @param string $data_index
     * @param string $child_name
     * @return array|bool
     */
    public static function infiniteTree($data,$parent_index='parent_id',$data_index='id',$child_name='child'){
        $items = [];
        foreach ($data as $d){
            $items[$d[$data_index]] = $d;
            if(!isset($d[$parent_index]) || !isset($d[$data_index]) || isset($d[$child_name])){
                return false;
            }
        }
        $tree = [];$n=0;
        foreach($items as $item){
            if(isset($items[$item[$parent_index]])){
                $items[$item[$parent_index]][$child_name][] = &$items[$item[$data_index]];
            }else{
                $tree[$n++] = &$items[$item[$data_index]];
            }
        }
        return $tree;
    }

    /**
     * 遍历生成树，生成节点列表
     * @param $tree
     * @param array $data
     * @param int $layer
     * @param string $layer_name
     * @param string $child_name
     */
     public static function traversalTree2nodeList($tree,&$data=[],$layer=0,$layer_name = 'layer',$child_name = 'child'){
        foreach ($tree as $t){
            $node = $t;
            unset($node[$child_name]);
            $node[$layer_name]=$layer;
            $data[] = $node;
            if(isset($t[$child_name]) && !empty($t[$child_name])){
                self::traversalTree2nodeList($t[$child_name],$data,$layer+1);
            }
        }
    }

    public static function getMonthBeginEndDay($year,$month,$format='timestamp'){
        $month = sprintf('%02d',$month);
        $ymd = $year."-".$month."-01";
        $begin = strtotime($ymd." 00:00:00");
        $end   = strtotime("$ymd +1 month -1 seconds");
        if($format=='timestamp'){
            return [
                'begin'=>$begin,
                'end'=>$end
            ];
        }else{
            return [
                'begin'=>date($format,$begin),
                'end'=>date($format,$end),
            ];
        }
    }

    public static function getDayBeginEndTime($date,$format='timestamp'){
        $begin = strtotime($date." 00:00:00");
        $end   = strtotime("$date +1 day -1 seconds");
        if($format=='timestamp'){
            return [
                'begin'=>$begin,
                'end'=>$end
            ];
        }else{
            return [
                'begin'=>date($format,$begin),
                'end'=>date($format,$end),
            ];
        }
    }

    public static function getHourBeginEndTime($date ,$hour,$format='timestamp'){
        $hour = sprintf('%02d',$hour);
        $begin = strtotime($date." ".$hour.":00:00");
        $end   = strtotime($date." ".$hour.":00:00 +1 hour -1 seconds");
        if($format=='timestamp'){
            return [
                'begin'=>$begin,
                'end'=>$end
            ];
        }else{
            return [
                'begin'=>date($format,$begin),
                'end'=>date($format,$end),
            ];
        }
    }
}