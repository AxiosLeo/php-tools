<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/4/21 9:39
 */
namespace axios\composer\tpr\service;
/**
 * Class UmengService
 * @package app\common\service
 * @doc_url http://dev.umeng.com/push/ios/api-doc#4
 */
class UmengService {
    // The host
    protected static $host = "http://msg.umeng.com";

    // The upload path
    protected static $uploadPath = "/upload";

    // The post path
    protected static $postPath = "/api/send";

    private static $appKey = NULL;
    // The app master secret
    private static $appSecret = NULL;

    public static $data = array(
        "appkey"           => NULL,
        "timestamp"        => NULL,
        "type"             => NULL,
        "production_mode"  => "true",
    );

    private static $data_keys    = [
        "appkey", "timestamp", "type", "device_tokens", "alias", "alias_type", "file_id", "filter", "production_mode", "feedback", "description", "thirdparty_id","payload","policy"
    ];

    public $result = NULL;

    public $http_code =NULL;

    public $curlErrNo = NULL;

    public $curlErr = NULL;

    public $errorInfo = NULL;

    public static function option($app_key,$app_secret,$debug=false){
        self::$appKey = $app_key;
        self::$appSecret = $app_secret;
        self::$data['appkey']=$app_key;
        self::$data['timestamp'] = time();
        self::$data['production_mode']=!$debug?"true":"false";
        return new self();
    }

    public function setData($data = []){
        /**
         * broadcast
         * filecast , file_id=Null
         * groupcast , filter=Null
         * listcast , device_tokens=Null
         * unicast , device_tokens=Null
         * customizedcast , alias_type=Null
         */
        foreach ($data as $key=>$d){
            if(in_array($key,self::$data_keys)){
                self::$data[$key] = $d;
            }
        }
        return $this;
    }

    public function getData(){
        return self::$data;
    }

    public function checkData($array=[]){
        if(empty($array)){
            $array = self::$data;
        }
        foreach ($array as $k=>$v) {
            if(is_null($v)){
                $this->errorInfo = $k." is null";
                return 1;
            }else if(is_array($v)){
                $this->checkData($v);
            }
        }
        return 0;
    }

    public function send(){
        if($this->checkData(self::$data)){
            return $this->errorInfo;
        }
        $url = self::$host . self::$postPath;
        $postBody = json_encode(self::$data);
        $sign = md5("POST" . $url . $postBody . self::$appSecret);
        $url = $url . "?sign=" . $sign;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody );
        $this->result = curl_exec($ch);
        $this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->curlErrNo = curl_errno($ch);
        $this->curlErr = curl_error($ch);
        curl_close($ch);
        if ($this->http_code != "200") {
            return false;
        }
        return true;
    }

    public function getError(){
        $error = [
            'curlErrNo'=>$this->curlErrNo,
            'curlErr'=>$this->curlErr
        ];
        return $error;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->http_code;
    }

    public function __invoke($app_key,$app_secret)
    {
        // TODO: Implement __invoke() method.
        self::$appKey = $app_key;
        self::$appSecret = $app_secret;
        self::$data['appkey']=$app_key;
        return new self();
    }
}
