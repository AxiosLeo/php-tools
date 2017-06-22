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

if (!function_exists('middleware')) {
    /**
     * 实例化验证器
     * @param string    $name 验证器名称
     * @param string    $layer 业务层名称
     * @param bool      $appendSuffix 是否添加类名后缀
     * @param string    $common
     * @return \think\Validate
     */
    function middleware($name = '', $layer = 'middleware', $appendSuffix = false,$common="common")
    {
        return \think\Loader::validate($name, $layer, $appendSuffix,$common);
    }
}
if(!function_exists('get_client_ip')){
    function get_client_ip($type = 0, $adv = false) {
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
}

if(!function_exists('array_sort')){
    function array_sort($array,$sortRule="",$order="asc"){
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
}

if(!function_exists('tpr_infinite_tree')){
    /**
     * 无限层级的生成树方法
     * @param $data
     * @param string $parent_index
     * @param string $data_index
     * @param string $child_name
     * @return array|bool
     */
    function tpr_infinite_tree($data,$parent_index='parent_id',$data_index='id',$child_name='child'){
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
}

if(!function_exists('')){
    /**
     * 遍历生成树，生成节点列表
     * @param $tree
     * @param array $data
     * @param int $layer
     * @param string $layer_name
     * @param string $child_name
     */
    function traversal_tree_to_node_list($tree,&$data=[],$layer=0,$layer_name = 'layer',$child_name = 'child'){
        foreach ($tree as $t){
            $node = $t;
            unset($node[$child_name]);
            $node[$layer_name]=$layer;
            $data[] = $node;
            if(isset($t[$child_name]) && !empty($t[$child_name])){
                traversal_tree_to_node_list($t[$child_name],$data,$layer+1);
            }
        }
    }
}

if(!function_exists('check_data_to_string')){
    function check_data_to_string(&$array=[]){
        if(is_array($array)){
            foreach ($array as &$a){
                if(is_array($a)){
                    $a = check_data_to_string($a);
                }
                if(is_int($a)){
                    $a = strval($a);
                }
                if(is_null($a)){
                    $a = "";
                }
            }
        }else if(is_int($array)){
            $array = strval($array);
        }else if(is_null($array)){
            $array = "";
        }
        return $array;
    }
}

if(!function_exists('object_to_array')){
    function object_to_array($object) {
        $object =  json_decode( json_encode( $object),true);
        return  $object;
    }
}

if(!function_exists('check_sign')){
    function check_sign($post_timestamp,$post_sign){
        $sign = md5($post_timestamp."tpr");
        return $post_sign!=$sign?$sign:true;
    }
}
