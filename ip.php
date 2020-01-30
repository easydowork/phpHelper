<?php
date_default_timezone_set("GMT");

//绑定 ip 到域名
$subDomain = "www.baidu.com";

$recordId = Ali::getInstance()->getDomainRecordId($subDomain);

$rr = explode('.',$subDomain)[0]??'';

Ali::getInstance()->UpdateDomainRecord($recordId,$rr);

/**
 * 更换阿里云域名ip地址
 * Class Ali
 * @property $accessKeyId
 * @property $accessSecret
 */
class Ali
{
    /**
     * @var string
     */
    private $accessKeyId = "";

    /**
     * @var string
     */
    private $accessSecret = "";

    /**
     * @var self
     */
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取域名记录id
     * getDomainRecordId
     * @param $subDomain
     * @param string $type
     * @return string
     */
    public function getDomainRecordId($subDomain,$type='A')
    {
        $tmpFile = __DIR__.'/'.$subDomain.'.log';
        $recordId = @file_get_contents($tmpFile);

        if(empty($recordId)){
            $domainInfo = $this->DescribeDomainRecords($subDomain,$type);
            $recordId = $domainInfo['DomainRecords']['Record'][0]['RecordId']??'';
            @file_put_contents($tmpFile,$recordId);
        }

        return $recordId;

    }

    /**
     * DescribeDomainRecords
     * @param $subDomain
     * @param string $type
     * @return array
     */
    private function DescribeDomainRecords($subDomain,$type='A')
    {
        $result = $this->requestAli([
            "Action" => "DescribeSubDomainRecords",
            "SubDomain" => $subDomain,
            "Type" => $type
        ]);

        return json_decode($result,1);
    }

    /**
     * 更新 ip
     * UpdateDomainRecord
     * @param $recordId
     * @param $rr
     * @param string $type
     * @param string $action
     */
    public function UpdateDomainRecord($recordId,$rr,$type='A',$action='UpdateDomainRecord')
    {
        $ip = $this->getIpAddress();

        $tmpFile = __DIR__.'/ip.log';

        $tmpIp = @file_get_contents($tmpFile);

        if(!empty($ip) && $ip != $tmpIp){
            $requestParams = [
                "Action" => $action,
                "RecordId" => $recordId,
                "RR" => $rr,
                "Type" => $type,
                "Value" => $ip,
            ];

            $result = $this->requestAli($requestParams);

            @file_put_contents($tmpFile,$ip);

            $this->outLog($result . ",修改ip记录成功($ip)");
        }
    }

    /**
     * requestAli
     * @param $requestParams
     * @return bool|string
     */
    private function requestAli($requestParams)
    {
        $publicParams = [
            "Format" => "JSON",
            "Version" => "2015-01-09",
            "AccessKeyId" => $this->accessKeyId,
            "Timestamp" => date("Y-m-d\TH:i:s\Z"),
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureVersion" => "1.0",
            "SignatureNonce" => substr(md5(rand(1, 99999999)), rand(1, 9), 14),
        ];

        $params = array_merge($publicParams, $requestParams);
        $params['Signature'] = $this->sign($params, $this->accessSecret);
        $uri = http_build_query($params);
        $url = 'http://alidns.aliyuncs.com/?' . $uri;
        return $this->curl($url);
    }

    /**
     * 获取ip地址
     * getIpAddress
     * @return string
     */
    private function getIpAddress()
    {
        $ip = $this->curl("http://ip.taobao.com/service/getIpInfo.php?ip=myip");
        $ip = json_decode($ip, true);
        return $ip['data']['ip']??'';
    }

    /**
     * sign
     * @param $params
     * @param $accessSecret
     * @param string $method
     * @return string
     */
    private function sign($params, $accessSecret, $method = "GET")
    {
        ksort($params);

        $stringToSign = strtoupper($method) . '&' . $this->percentEncode('/') . '&';

        $tmp = "";
        foreach ($params as $key => $val) {
            $tmp .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($val);
        }
        $tmp = trim($tmp, '&');
        $stringToSign = $stringToSign . $this->percentEncode($tmp);

        $key = $accessSecret . '&';
        $hmac = hash_hmac("sha1", $stringToSign, $key, true);

        return base64_encode($hmac);
    }

    private function percentEncode($value = null)
    {
        $en = urlencode($value);
        $en = str_replace("+", "%20", $en);
        $en = str_replace("*", "%2A", $en);
        $en = str_replace("%7E", "~", $en);
        return $en;
    }

    private function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if(curl_exec($ch) === false){
            $this->outLog('Curl error: ' . curl_error($ch));
            exit();
        }
        curl_close($ch);
        return $result;
    }

    private function outLog($msg)
    {
        echo date("Y-m-d H:i:s") . "  " . $msg . PHP_EOL;
    }
}
