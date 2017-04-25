<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/4/7 15:44
 */
namespace axios\tpr\service;
use think\Config;
use stdClass;
class RestService {
    private $AccountSid;
    private $AccountToken;
    private $AppId;
    private $SubAccountSid;
    private $SubAccountToken;
    private $VoIPAccount;
    private $VoIPPassword;
    private $ServerIP;
    private $ServerPort;
    private $SoftVersion;
    private $Batch;  //时间sh
    private $BodyType = "xml";//包体格式，可填值：json 、xml
    private $enabeLog = true; //日志开关。可填值：true、
    private $Filename="../log.txt"; //日志文件
    private $Handle;
    private $config;

    function __construct()
    {
        $this->config = Config::get('rest');

        $this->Batch = date("YmdHis");
        $this->ServerIP = $this->config['serverIP'];
        $this->ServerPort = $this->config['serverPort'];
        $this->SoftVersion = $this->config['softVersion'];
        $this->Handle = fopen($this->Filename, 'a');
        $this->setAccount($this->config['accountSid'],$this->config['accountToken']);
        $this->setAppId($this->config['appId']);
    }

    /**
     * 设置主帐号
     *
     * @param string $AccountSid 主帐号
     * @param string $AccountToken 主帐号Token
     */
    function setAccount($AccountSid,$AccountToken){
        $this->AccountSid = $AccountSid;
        $this->AccountToken = $AccountToken;
    }

    /**
     * 设置子帐号
     *
     * @param string $SubAccountSid 子帐号
     * @param string $SubAccountToken 子帐号Token
     * @param string $VoIPAccount VoIP帐号
     * @param string $VoIPPassword VoIP密码
     */
    function setSubAccount($SubAccountSid,$SubAccountToken,$VoIPAccount,$VoIPPassword){
        $this->SubAccountSid = $SubAccountSid;
        $this->SubAccountToken = $SubAccountToken;
        $this->VoIPAccount = $VoIPAccount;
        $this->VoIPPassword = $VoIPPassword;
    }

    /**
     * 设置应用ID
     *
     * @param string $AppId 应用ID
     */
    function setAppId($AppId){
        $this->AppId = $AppId;
    }

    /**
     * 打印日志
     *
     * @param string $log 日志内容
     */
    function showlog($log){
        if($this->enabeLog){
            fwrite($this->Handle,$log."\n");
        }
    }

    /**
     * 发起HTTPS请求
     *
     * @param $url
     * @param $data
     * @param $header
     * @param int $post
     * @return mixed|string
     */
    function curl_post($url,$data,$header,$post=1)
    {
        //初始化curl
        $ch = curl_init();
        //参数设置
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $post);
        if($post)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $result = curl_exec ($ch);
        //连接失败
        if($result == FALSE){
            if($this->BodyType=='json'){
                $result = "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
            } else {
                $result = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><Response><statusCode>172001</statusCode><statusMsg>网络错误</statusMsg></Response>";
            }
        }

        curl_close($ch);
        return $result;
    }

    /**
     * 创建子帐号
     *
     * @param string $friendlyName 子帐号名称
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function createSubAccount($friendlyName)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $body= "{'appId':'$this->AppId','friendlyName':'$friendlyName'}";
        }else{
            $body="<SubAccount>
                    <appId>$this->AppId</appId>
                    <friendlyName>$friendlyName</friendlyName>
                  </SubAccount>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/SubAccounts?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐号Id + 英文冒号 + 时间戳
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
    }

    /**
     * 获取子帐号
     *
     * @param string $startNo 开始的序号，默认从0开始
     * @param string $offset 一次查询的最大条数，最小是1条，最大是100条
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function getSubAccounts($startNo,$offset)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $body= "{'appId':'$this->AppId','startNo':'$startNo','offset':'$offset'}";
        }else{
            $body="
            <SubAccount>
              <appId>$this->AppId</appId>
              <startNo>$startNo</startNo>  
              <offset>$offset</offset>
            </SubAccount>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/GetSubAccounts?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }

    /**
     * 子帐号信息查询
     *
     * @param string $friendlyName 子帐号名称
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function querySubAccount($friendlyName)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体

        if($this->BodyType=="json"){
            $body= "{'appId':'$this->AppId','friendlyName':'$friendlyName'}";
        }else{
            $body="
            <SubAccount>
              <appId>$this->AppId</appId>
              <friendlyName>$friendlyName</friendlyName>
            </SubAccount>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/QuerySubAccountByName?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }

    /**
     * 发送模板短信
     *
     * @param string $to 短信接收彿手机号码集合,用英文逗号分开
     * @param array $datas 内容数据
     * @param string $tempId 模板Id
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function sendTemplateSMS($to,$datas,$tempId)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $data="";
            for($i=0;$i<count($datas);$i++){
                $data = $data. "'".$datas[$i]."',";
            }
            $body= "{'to':'$to','templateId':'$tempId','appId':'$this->AppId','datas':[".$data."]}";
        }else{
            $data="";
            for($i=0;$i<count($datas);$i++){
                $data = $data. "<data>".$datas[$i]."</data>";
            }
            $body="<TemplateSMS>
                    <to>$to</to> 
                    <appId>$this->AppId</appId>
                    <templateId>$tempId</templateId>
                    <datas>".$data."</datas>
                  </TemplateSMS>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/SMS/TemplateSMS?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        //重新装填数据
        if($datas->statusCode==0){
            if($this->BodyType=="json"){
                $datas->TemplateSMS =$datas->templateSMS;
                unset($datas->templateSMS);
            }
        }

        return $datas;
    }

    /**
     * 双向回呼
     * @param string $from 主叫电话号码
     * @param string $to 被叫电话号码
     * @param string $customerSerNum 被叫侧显示的客服号码
     * @param string $fromSerNum 主叫侧显示的号码
     * @param string $promptTone 自定义回拨提示音
     * @param string $userData 第三方私有数据
     * @param string $maxCallTime 最大通话时长
     * @param string $hangupCdrUrl 实时话单通知地址
     * @param string $alwaysPlay 是否一直播放提示音
     * @param string $terminalDtmf 用于终止播放promptTone参数定义的提示音
     * @param string $needBothCdr 是否给主被叫发送话单
     * @param string $needRecord 是否录音
     * @param string $countDownTime 设置倒计时时间
     * @param string $countDownPrompt 倒计时时间到后播放的提示音
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function callBack($from,$to,$customerSerNum,$fromSerNum,$promptTone,$alwaysPlay,$terminalDtmf,$userData,$maxCallTime,$hangupCdrUrl,$needBothCdr,$needRecord,$countDownTime,$countDownPrompt)
    {
        //子帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->subAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $body= "{'from':'$from','to':'$to','customerSerNum':'$customerSerNum','fromSerNum':'$fromSerNum','promptTone':'$promptTone','userData':'$userData','maxCallTime':'$maxCallTime','hangupCdrUrl':'$hangupCdrUrl',
           'alwaysPlay':'$alwaysPlay','terminalDtmf':'$terminalDtmf','needBothCdr':'$needBothCdr',
           'needRecord':'$needRecord','countDownTime':'$$countDownTime','countDownPrompt':'$countDownPrompt'}";
        }else{
            $body= "<CallBack>
                     <from>$from</from>
                     <to>$to</to>
                     <customerSerNum>$customerSerNum</customerSerNum>
                     <fromSerNum>$fromSerNum</fromSerNum>
                     <promptTone>$promptTone</promptTone>
					           <userData>$userData</userData>
					           <maxCallTime>$maxCallTime</maxCallTime>
					           <hangupCdrUrl>$hangupCdrUrl</hangupCdrUrl>
                     <alwaysPlay>$alwaysPlay</alwaysPlay>
                     <terminalDtmf>$terminalDtmf</terminalDtmf>
                     <needBothCdr>$needBothCdr</needBothCdr>
                     <needRecord>$needRecord</needRecord>
                     <countDownTime>$countDownTime</countDownTime>
                     <countDownPrompt>$countDownPrompt</countDownPrompt>
                   </CallBack>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->SubAccountSid . $this->SubAccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/SubAccounts/$this->SubAccountSid/Calls/Callback?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：子帐号Id + 英文冒号 + 时间戳
        $authen=base64_encode($this->SubAccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
    }



    /**
     * 外呼通知
     *
     * @param string $to 被叫号码
     * @param string $mediaName 语音文件名称，格式 wav。与mediaTxt不能同时为空。当不为空时mediaTxt属性失效。
     * @param string $mediaTxt 文本内容
     * @param string $displayNum 显示的主叫号码
     * @param string $playTimes 循环播放次数，1－3次，默认播放1次。
     * @param string $respUrl 外呼通知状态通知回调地址，云通讯平台将向该Url地址发送呼叫结果通知。
     * @param string $userData 用户私有数据
     * @param string $maxCallTime 最大通话时长
     * @param string $speed 发音速度
     * @param string $volume 音量
     * @param string $pitch 音调
     * @param string $bgsound 背景音编号
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function landingCall($to,$mediaName,$mediaTxt,$displayNum,$playTimes,$respUrl,$userData,$maxCallTime,$speed,$volume,$pitch,$bgsound)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $body= "{'playTimes':'$playTimes','mediaTxt':'$mediaTxt','mediaName':'$mediaName','to':'$to','appId':'$this->AppId','displayNum':'$displayNum','respUrl':'$respUrl',
           'userData':'$userData','maxCallTime':'$maxCallTime','speed':'$speed','volume':'$volume','pitch':'$pitch','bgsound':'$bgsound'}";
        }else{
            $body="<LandingCall>
                    <to>$to</to>
                    <mediaName>$mediaName</mediaName>
                    <mediaTxt>$mediaTxt</mediaTxt> 
                    <appId>$this->AppId</appId>
                    <displayNum>$displayNum</displayNum>
                    <playTimes>$playTimes</playTimes>
                    <respUrl>$respUrl</respUrl>
                    <userData>$userData</userData>
                    <maxCallTime>$maxCallTime</maxCallTime>
                    <speed>$speed</speed>
                    <volume>$volume</volume>
                    <pitch>$pitch</pitch>
                    <bgsound>$bgsound</bgsound>
                  </LandingCall>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/Calls/LandingCalls?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
    }

    /**
     * 语音验证码
     * @param string $verifyCode 验证码内容，为数字和英文字母，不区分大小写，长度4-8位
     * @param string $playTimes 播放次数，1－3次
     * @param string $to 接收号码
     * @param string $displayNum 显示的主叫号码
     * @param string $respUrl 语音验证码状态通知回调地址，云通讯平台将向该Url地址发送呼叫结果通知
     * @param string $lang 语言类型
     * @param string $userData 第三方私有数据
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function voiceVerify($verifyCode,$playTimes,$to,$displayNum,$respUrl,$lang,$userData)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $body= "{'appId':'$this->AppId','verifyCode':'$verifyCode','playTimes':'$playTimes','to':'$to','respUrl':'$respUrl','displayNum':'$displayNum',
           'lang':'$lang','userData':'$userData'}";
        }else{
            $body="<VoiceVerify>
                    <appId>$this->AppId</appId>
                    <verifyCode>$verifyCode</verifyCode>
                    <playTimes>$playTimes</playTimes>
                    <to>$to</to>
                    <respUrl>$respUrl</respUrl>
                    <displayNum>$displayNum</displayNum>
                    <lang>$lang</lang>
                    <userData>$userData</userData>
                  </VoiceVerify>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/Calls/VoiceVerify?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }

        return $datas;
    }

    /**
     * IVR外呼
     *
     * @param string $number   待呼叫号码，为Dial节点的属性
     * @param string $userdata 用户数据，在<startservice>通知中返回，只允许填写数字字符，为Dial节点的属性
     * @param string $record   是否录音，可填项为true和false，默认值为false不录音，为Dial节点的属性
     * @return mixed stdClass|\SimpleXMLElement
     */
    function ivrDial($number,$userdata,$record)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        $body=" <Request>
                  <Appid>$this->AppId</Appid>
                  <Dial number='$number'  userdata='$userdata' record='$record'></Dial>
                </Request>";
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/ivr/dial?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/xml","Content-Type:application/xml;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        $datas = simplexml_load_string(trim($result," \t\n\r"));
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }

    /**
     * 话单下载
     * @param string $date     day 代表前一天的数据（从00:00 – 23:59）
     * @param string $keywords   客户的查询条件，由客户自行定义并提供给云通讯平台。默认不填忽略此参数
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function billRecords($date,$keywords)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $body= "{'appId':'$this->AppId','date':'$date','keywords':'$keywords'}";
        }else{
            $body="<BillRecords>
                    <appId>$this->AppId</appId>
                    <date>$date</date>
                    <keywords>$keywords</keywords>
                  </BillRecords>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/BillRecords?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }

    /**
     * 主帐号信息查询
     */
    function queryAccountInfo()
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/AccountInfo?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,"",$header,0);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }

    /**
     * 短信模板查询
     * @param string $templateId 模板ID
     * @param $templateId
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function QuerySMSTemplate($templateId)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $body= "{'appId':'$this->AppId','templateId':'$templateId'}";
        }else{
            $body="<Request>
                    <appId>$this->AppId</appId>
                    <templateId>$templateId</templateId>  
                  </Request>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/SMS/QuerySMSTemplate?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }


    /**
     * 取消回拨
     * @param string $callSid          一个由32个字符组成的电话唯一标识符
     * @param int $type   0： 任意时间都可以挂断电话；1 ：被叫应答前可以挂断电话，其他时段返回错误代码；2： 主叫应答前可以挂断电话，其他时段返回错误代码；默认值为0。
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function CallCancel($callSid,$type)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->subAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $body= "{'appId':'$this->AppId','callSid':'$callSid','type':'$type'}";
        }else{
            $body="<CallCancel>
                    <appId>$this->AppId</appId>
                    <callSid>$callSid</callSid>
                    <type>$type</type>
                  </CallCancel>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->SubAccountSid . $this->SubAccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/SubAccounts/$this->SubAccountSid/Calls/CallCancel?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->SubAccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }

    /**
     * 呼叫状态查询
     * @param string $callid     呼叫Id
     * @param string $action   查询结果通知的回调url地址
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function QueryCallState($callid,$action)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->BodyType=="json"){
            $body= "{'Appid':'$this->AppId','QueryCallState':{'callid':'$callid','action':'$action'}}";
        }else{
            $body="<Request>
                    <Appid>$this->AppId</Appid>
                    <QueryCallState callid ='$callid' action='$action'/>
                  </Request>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/ivr/call?sig=$sig&callid=$callid";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }

    /**
     * 呼叫结果查询
     *
     * @param string $callSid 呼叫Id
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function CallResult($callSid)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/CallResult?sig=$sig&callsid=$callSid";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/$this->BodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,"",$header,0);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }

    /**
     * 语音文件上传
     *
     * @param string $filename 文件名
     * @param string $body 二进制串
     * @return mixed stdClass|mixed|\SimpleXMLElement
     */
    function MediaFileUpload($filename,$body)
    {
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体

        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/Calls/MediaFileUpload?sig=$sig&appid=$this->AppId&filename=$filename";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = array("Accept:application/$this->BodyType","Content-Type:application/octet-stream","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->BodyType=="json"){//JSON格式
            $datas=json_decode($result);
        }else{ //xml格式
            $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误';
//        }
        return $datas;
    }

    /**
     * 子帐号鉴权
     * @return bool|stdClass
     */
    function subAuth()
    {
        if($this->ServerIP==""){
            $data = new stdClass();
            $data->statusCode = '172004';
            $data->statusMsg = 'IP为空';
            return $data;
        }
        if($this->ServerPort<=0){
            $data = new stdClass();
            $data->statusCode = '172005';
            $data->statusMsg = '端口错误（小于等于0）';
            return $data;
        }
        if($this->SoftVersion==""){
            $data = new stdClass();
            $data->statusCode = '172013';
            $data->statusMsg = '版本号为空';
            return $data;
        }
        if($this->SubAccountSid==""){
            $data = new stdClass();
            $data->statusCode = '172008';
            $data->statusMsg = '子帐号为空';
            return $data;
        }
        if($this->SubAccountToken==""){
            $data = new stdClass();
            $data->statusCode = '172009';
            $data->statusMsg = '子帐号令牌为空';
            return $data;
        }
        if($this->AppId==""){
            $data = new stdClass();
            $data->statusCode = '172012';
            $data->statusMsg = '应用ID为空';
            return $data;
        }
        return false;
    }

    /**
     * 主帐号鉴权
     * @return bool|stdClass
     */
    function accAuth()
    {
        if($this->ServerIP==""){
            $data = new stdClass();
            $data->statusCode = '172004';
            $data->statusMsg = 'IP为空';
            return $data;
        }
        if($this->ServerPort<=0){
            $data = new stdClass();
            $data->statusCode = '172005';
            $data->statusMsg = '端口错误（小于等于0）';
            return $data;
        }
        if($this->SoftVersion==""){
            $data = new stdClass();
            $data->statusCode = '172013';
            $data->statusMsg = '版本号为空';
            return $data;
        }
        if($this->AccountSid==""){
            $data = new stdClass();
            $data->statusCode = '172006';
            $data->statusMsg = '主帐号为空';
            return $data;
        }
        if($this->AccountToken==""){
            $data = new stdClass();
            $data->statusCode = '172007';
            $data->statusMsg = '主帐号令牌为空';
            return $data;
        }
        if($this->AppId==""){
            $data = new stdClass();
            $data->statusCode = '172012';
            $data->statusMsg = '应用ID为空';
            return $data;
        }
        return false;
    }
}