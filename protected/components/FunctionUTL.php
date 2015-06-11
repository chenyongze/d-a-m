<?php
/**
 * 
 * @info :全局函数
 * @author yongze
 *
 */

class FunctionUTL 
{
    //test
    static public function Test()
    {
        echo('test');
    }
    
    static public  function Debug($var, $color = 'black')
    {
       echo "<pre style=\"font-family:Consolas,Calibri,'Microsoft Yahei','微软雅黑',Tahoma,Arial,Helvetica,STHeiti;color:$color;font-size:14px;\">";
	   if(empty($var))
	   {
	       var_dump($var);
	   }else 
	   {
	       print_r($var);
	   }
        echo "<br/><br/>";
    	echo "</pre>";
    }
    
    /**
     * 数据编码成json格式，添加原因 兼容多个文件中存在多个 _toJson
     * @param unknown $data
     * @return string
     */
    static function ToJson($data)
    {
       // $data = self::FormatGBKToUTF8($data);
        return json_encode($data);
    }
    
    /**
     * @info:debug log
     * @param unknown $msg
     * @param string $file
     */
    static public function DebugLog($msg, $file='lottery')
    {
        if (defined('ENABLE_DEBUGLOG') && TRUE === ENABLE_DEBUGLOG)
        {
            //@TODO:上线前去掉
            $data = $msg;
            if (is_array($msg))
            {
                //self::FormatGBKToUTF8($msg);
                $data = json_encode($msg);
            }
            $msg = date("Y-m-d H:i:s")."\t".$data;
            	
//             $ram = FunctionUTL::GetRequestParam('ram','');
//             if ($ram)
//             {
//                 $msg .= "\t".$ram;
//             }
            $msg .= "\r\n";
            file_put_contents('/tmp/'.date("Y_m_d")."_{$file}.log", $msg, FILE_APPEND);
        }
    }
    
    
    /**
     * 从$_GET,$_POST中获取HTTP传递数据,并进行类型过滤
     *
     * @param constant HTTP_GET|HTTP_POST|HTTP_REQUEST $method
     * @param string $name 参数名称
     * @param constant FILTER_STRING|FILTER_NUMBER $type 过滤类型
     * @return string|int
     */
    function GetRequestParam($name , $type=FILTER_STRING, $method = HTTP_REQUEST)
    {
        $tmp = '';
        if($method == HTTP_GET)
        {
            $tmp = array_key_exists($name, $_GET) ? trim($_GET[$name]) : '';
        }
        else if($method == HTTP_POST)
        {
            $tmp = array_key_exists($name, $_POST) ? trim($_POST[$name]) : '';
        }
        else if($method == HTTP_REQUEST)
        {
            if(array_key_exists($name,$_GET))
            {
                $tmp = trim($_GET[$name]);
            }
            else if(array_key_exists($name,$_POST))
            {
                $tmp = trim($_POST[$name]);
            }
            else
            {
                $tmp = '';
            }
        }
    
        if(empty($tmp)) {
            return '';
        }
        switch($type)
        {
            case FILTER_FLOAT:
                $tmp = floatval($tmp);
                if (!ValidatorUTL::IsFloat($tmp))
                {
                    $tmp = '';
                }
                break;
            case FILTER_STRING:
                $tmp = (string)$tmp;
                break;
            case FILTER_NUMBER:
                if (!ValidatorUTL::IsPositiveInt($tmp))
                {
                    $tmp = '';
                }
                $tmp = intval($tmp);
                break;
        }
        return $tmp;
    }
    
    /**
     * 递归格式化数组中数据GBK-->UTF8
     * @param  $data mixed 数据
     * @return $data mixed 返回数据
     */
    static public function FormatGBKToUTF8(&$data)
    {
        switch(gettype($data))
        {
            case "string":
                //				if (!self::IsUTF8($data, 'UTF-8'))
                    //				{
                $data = @iconv('GBK', 'UTF-8//IGNORE', $data);
                //				}
                break;
                        case "NULL":
                            $data = '';
                            break;
                        case "array":
                            foreach($data as &$value)
                            {
                                $value = self::FormatGBKToUTF8($value);
                            }
                            break;
                        case "object":
                            foreach($data as &$value)
                            {
                                $value = self::FormatGBKToUTF8($value);
                            }
                            break;
                        default:
                            break;
                    }
                    return $data;
        }
        
        /**
         * 递归还原数据中经过编码的html特殊字符
         * @param  $data mixed 数据
         * @return $data mixed 返回数据
         */
        static public function DecodeHtmlSpecialchars(&$data)
        {
            switch(gettype($data))
            {
                case "string":
                    $data = self::_unhtmlspecialchars($data);
                    break;
                case "NULL":
                    $data = '';
                    break;
                case "array":
                    foreach($data as &$value)
                    {
                        $value = self::DecodeHtmlSpecialchars($value);
                    }
                    break;
                case "object":
                    foreach($data as &$value)
                    {
                        $value = self::DecodeHtmlSpecialchars($value);
                    }
                    break;
                default:
                    break;
            }
            return $data;
        }
    
    /**
     * 同类对象间的值拷贝
     * @param $FObj：源对象
     * @param $TObj：目的对象
     * @return 0 success
     */
    static public function ObjToObj($fObj, &$tObj)
    {
        if(is_null($fObj) || is_null($tObj))
        {
            return -1002;
        }
    
        if(($rst = self::CleanObj($tObj)) < 0)
        {
            return $rst;
        }
    
        foreach($fObj as $key => $value)
        {
            if(!isset($fObj->$key) || is_null($tObj))
            {
                continue;
            }
    
            $tObj->$key = $fObj->$key;
        }
    
        return 0;
    }
    
    /**
     * 判断对象
     * @param $Obj 对象
     * @return TRUE   是对象
     *         FALSE  不是对象
     */
    static public function IsInstance($obj)
    {
        return (!is_null($obj) && is_object($obj));
    }
    
    /**
     * 文本过滤
     *
     * @param string $str
     * @return string
     */
    static public function TextFilter($str)
    {
        $str = trim($str);
        $str = preg_replace( '/[\a\f\n\e\0\r\t\x0B]/is', "", $str );
        $str = htmlspecialchars($str, ENT_QUOTES);
        $str = self::_TagFilter($str);
        $str = self::_CommonFilter($str);
//         $str = self::_SQLFilter($str);
        return $str;
    }
    
    
    /**********************************************2.内部方法**********************************************/
    /**
     * 做一些字符转换，防止XSS等方面的问题
     *
     * @param string $str
     * @return string
     */
    static private function _TagFilter($str)
    {
        $str = str_ireplace( "javascript" , "j&#097;v&#097;script", $str );
        $str = str_ireplace( "alert"      , "&#097;lert"          , $str );
        $str = str_ireplace( "about:"     , "&#097;bout:"         , $str );
        $str = str_ireplace( "onmouseover", "&#111;nmouseover"    , $str );
        $str = str_ireplace( "onclick"    , "&#111;nclick"        , $str );
        $str = str_ireplace( "onload"     , "&#111;nload"         , $str );
        $str = str_ireplace( "onsubmit"   , "&#111;nsubmit"       , $str );
        $str = str_ireplace( "<script"	  , "&#60;script"		  , $str );
        $str = str_ireplace( "document."  , "&#100;ocument."      , $str );
    
        return $str;
    }
    /**
     * 一些字符串格式化
     *
     * @param string $str
     * @return string
     */
    static private function _CommonFilter($str)
    {
        $str = str_replace( "&#032;"			, " "			, $str );
        $str = preg_replace( "/\\\$/"			, "&#036;"		, $str );
        $str = self::_stripslashes($str);
        return $str;
    }
    
    /**
     * SQL注入的过滤
     *
     * @param string $str
     * @return string
     */
    static private function _SQLFilter($str)
    {
        $_sqlCommond = array('sleep');
        foreach($_sqlCommond as $_sc)
        {
            if(preg_match( "/". $_sc ."\s*(\(|%28)/i", $str ))
            {
                $str = '';
                break;
            }
        }
        return $str;
    }
    
    /**
     * 包装stripslashes
     *
     * @param string $str
     * @return string
     */
    static private function _stripslashes($str)
    {
        global $magic_quotes_gpc;
    
        if ($magic_quotes_gpc)
        {
            $str = stripslashes($str);
        }
        return $str;
    }
    
    /**
     * 还原数据中经过编码的html特殊字符
     *
     * @param string $str
     * @return string
     */
    static private function _unhtmlspecialchars($str)
    {
        $str = str_replace( "&amp;" , "&", $str );
        $str = str_replace( "&lt;"  , "<", $str );
        $str = str_replace( "&gt;"  , ">", $str );
        $str = str_replace( "&quot;", '"', $str );
        $str = str_replace( "&#039;", "'", $str );
    
        return $str;
    }
    
    static private function _GetRndNum($ip, $site)
    {
        $ip = ip2long($ip);
        $time = time();
        $rndNum = $ip+$time;
        $rndNum = ($rndNum*9301 + 49297)%233280;
        $rndNum = $rndNum/(233280.0);
    
        return ceil($rndNum*pow(10, $site));
    }
    
    /**
     * added by yongze on 2013-06-13
     * 递归格式化数组中数据UTF8-->GBK
     * @param  $data mixed 数据
     * @return $data mixed 返回数据
     */
    static public function FormatUTF8ToGBK(&$data)
    {
        switch(gettype($data))
        {
            case "string":
                $data = @iconv('UTF-8', 'GBK//IGNORE', $data);
                break;
            case "NULL":
                $data = '';
                break;
            case "array":
                foreach($data as &$value)
                {
                    $value = self::FormatUTF8ToGBK($value);
                }
                break;
            case "object":
                foreach($data as &$value)
                {
                    $value = self::FormatUTF8ToGBK($value);
                }
                break;
            default:
                break;
        }
        return $data;
    }
    
    /**
     * 输出GBK编码的字符串，首先检测是否为UTF-8，是则转换为GBK；并处理类似“磨叽”这种非utf8字符，但检测结果为utf8的字符串
     * @param $data
     * @return string
     */
    static public function OutputToGBK($data)
    {
        $orginalData = $data;
        $out = $orginalData;
        if (FunctionUTL::CheckUTF8($data)) {
            FunctionUTL::FormatUTF8ToGBK($data);//处理类似“磨叽”这种非utf8字符，但检测结果为utf8的字符串
            $data = trim($data);
            if(!empty($data)){
                $out = $data;
            }
        }
        return $out;
    }
    
    
    
    
}