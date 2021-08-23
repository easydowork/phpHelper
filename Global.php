<?php
/**
 * Created by PhpStorm.
 * User: lyl
 * Date: 2019/01/02
 * Time: 13:52
 */

/**
 * 数组转yaconf配置文件
 * arr2ini
 * @param array  $config
 * @param string $parent
 * @param int    $level
 * @return string
 */
function arr2ini(array $config, $parent = '',$level=0){
    $out = '';
    foreach ($config as $key => $value) {
        if (is_array($value)) {
            $level++;
            if($level==0){
                $out .= '[' . $key . ']' . PHP_EOL;
                $sec='';
            }else{
                if(empty($parent)){
                    $sec = $key.'.';
                }else{
                    $sec = $parent.$key.'.';
                }
            }
            $out .= arr2ini($value, $sec,$level);
        } else {
            if(is_numeric($value)){
                $out .= $parent."$key=$value" . PHP_EOL;
            }elseif(is_bool($value)){
                $out .= $parent."$key=".($value?1:0) . PHP_EOL;
            }else{
                $out .= $parent."$key='$value'" . PHP_EOL;
            }
            $level=0;
        }
    }
    return $out;
}

/**
 * 判断是不是手机
 * @param string $phone
 * @return false|int
 */
function isPhone($phone){
    return preg_match("/^1[345789]\d{9}$/",$phone);
}

/**
 * 隐藏手机号
 * hidePhone
 * @param string $phone
 * @param int $start
 * @param int $length
 * @return mixed
 */
function hidePhone($phone, $start=3, $length=4){
    return substr_replace($phone, '****', $start, $length);
}

/**
 * 判断是不是手机访问
 * @return bool
 */
function isMobile() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi",
        "android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio",
        "au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu",
        "cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ",
        "fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi",
        "htc","huawei","hutchison","inno","ipad","ipaq","iphone","ipod","jbrowser","kddi",
        "kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo",
        "mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-",
        "moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia",
        "nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-",
        "playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo",
        "samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank",
        "sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit",
        "tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin",
        "vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce",
        "wireless","xda","xde","zte");
    $is_mobile = false;
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
            break;
        }
    }
    return $is_mobile;
}
