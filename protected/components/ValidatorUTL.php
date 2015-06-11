<?php
/**
 *字段检查类
 */

class ValidatorUTL
{
	//变量检验类型

    private $_error;
    private $_defaultMsg = 'invalid';
    private $_notExistTypeError = 'not exist type';
    private $_notExistFuncError = 'not exist function';
    private $_acts = array('Max', 'Min', 'In', 'Regexp');

    /**
     * Check if is not empty
     *
     * @param string $str
     * @return boolean
     */
    static public function NotEmpty($mixed, $is_trim=true)
    {
        if (is_array($mixed)) {
            return (0 < count($mixed));
        }

        return strlen($is_trim?trim($mixed):$mixed) ? true : false;
    }

    /**
     * Match regex
     *
     * @param string $value
     * @param string $regex
     * @return boolean
     */
    static public function Regexp($value, $regex)
    {
        return preg_match($regex, $value) ? true : false;
    }

    /**
     * Max
     *
     * @param mixed $value numbernic|string
     * @param number $max
     * @return boolean
     */
    static public function Max($value, $max)
    {
        return is_string($value) ? (strlen($value)<=$max) : ($value<=$max);
    }

    /**
     * Min
     *
     * @param mixed $value numbernic|string
     * @param number $min
     * @return boolean
     */
    public static function Min($value, $min)
    {
        return is_string($value) ? (strlen($value)>=$min) : ($value>=$min);
    }

    /**
     * Check if in array
     *
     * @param mixed $value
     * @param array $list
     * @return boolean
     */
    static public function In($value, $list)
    {
        return (is_array($list) && in_array($value, $list)) ? true : false;
    }

    /**
     * Check if is email
     *
     * @param string $email
     * @return boolean
     */
    static public function IsEmail($email)
    {
        return preg_match('/^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)$/', $email) ? true : false;
    }

    /**
     * Check if is url
     *
     * @param string $url
     * @return boolean
     */
    static public function IsUrl($url)
    {
        return preg_match('/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&amp;]*)?)?(#[a-z][a-z0-9_]*)?$/i', $url) ? true : false;
    }

    /**
     * Check if is ip
     *
     * @param string $ip
     * @return boolean
     */
    static public function IsIp($ip)
    {
        return ((false === ip2long($ip)) || (long2ip(ip2long($ip)) !== $ip)) ? false : true;
    }

    /**
     * Check if is date
     *
     * @param string $date
     * @return boolean
     */
    static public function IsDate($date)
    {
                if (!preg_match('/^(\d{4})[\/-]?(\d{1,2})[\/-]?(\d{1,2})(\s(\d{1,2}):(\d{1,2}):(\d{1,2}))?$/', $date, $matches)) {
            return false;
        }

        // 支持0000-00-00格式
        if (0 == intval($matches[1])) $matches[1] = 1970;
        if (0 == intval($matches[2])) $matches[2] = 1;
        if (0 == intval($matches[3])) $matches[3] = 1;

        return checkdate($matches[2], $matches[3], $matches[1]);
    }

    /**
     * Check if is numbers
     *
     * @param mixed $value
     * @return boolean
     */
    static public function IsNumber($value)
    {
        return is_numeric($value);
    }

    /**
     * Check if is int
     *
     * @param mixed $value
     * @return boolean
     */
    static public function IsInt($value)
    {
        return is_int($value);
    }

    /**
     * Check if is digit
     *
     * @param mixed $value
     * @return boolean
     */
    static public function IsDigit($value)
    {
        return (is_int($value) || ctype_digit($value));
    }

    /**
     * Check if is string
     *
     * @param mixed $value
     * @return boolean
     */
    static public function IsString($value)
    {
        return is_string($value);
    }
    
    /**
     * Check if is  float
     *
     * @param string $value
     * @return boolean
     */
    static public function IsFloat($value)
    {
        return (is_float($value)) ? TRUE : FALSE;
    }
    
    /**
     * Check if is  positive int
     *
     * @param string $value
     * @return boolean
     */
    static public function IsPositiveInt($value)
    {
        return (is_numeric($value) && $value > 0) ? TRUE : FALSE;
    }
    
    /**
     * Check if is not negative int
     *
     * @param string $value
     * @return boolean
     */
    static public function IsNotNegativeInt($value)
    {
        return (is_numeric($value) && $value >= 0) ? TRUE : FALSE;
    }
    
    /**
     * Check if is array
     *
     * @param mixed $value
     * @return boolean
     */
    static public function IsArray($value)
    {
        return is_array($value);
    }
    
   /**
     * Check if is boolean
     *
     * @param mixed $value
     * @return boolean
     */
    static public function IsBool($value)
    {
        return is_bool($value);
    }

    /**
     * Check
     *
     * $rules = array(
     *     'type'     => var type, should be in ('email', 'url', 'ip', 'date', 'number', 'int', 'string')
     *     'required' => TRUE FALSE
     *     'max'      => 数值
     *     'min'      => 数值
     *     'msg'      => error message,can be as an array
     * )
     *
     * @param array $data
     * @param array $rules
     * @return boolean
     */
    public function Check($data, $rules)
    {
    
        foreach ($rules as $key => $rule) 
        {
            if ((!isset($rule['required']) || !$rule['required']) && empty($data[$key])) 
            {
                continue;
            }
            $value = (isset($data[$key]) ? $data[$key] : '');
            
            $rstMsg = '';
            $rst = self::_Check($value, $rule, $rstMsg);
            if(!$rst)
            {
	            $this->_error = $rstMsg;
	        	return FALSE;
            }
        }

        return TRUE;
    }


    /**
     * Get error
     *
     * @return array
     */
    public function Error()
    {
        return $this->_error;
    }
    
    
    /**
     * Check value
     *
     * @param mixed $value
     * @param array $rule
     * @return mixed string as error, true for OK
     */
    private function _Check($value, $rule, &$rstMsg)
    {
    	$rstMsg = '';
    	if (!isset($rule['type']))
    	{
    		$rstMsg = $this->_notExistTypeError;
            return FALSE;
    	}
    	
        if (!empty($rule['required']) && $rule['required'] && !self::NotEmpty($value)) 
        {
        	$rstMsg = $this->_Msg($rule);
            return FALSE;
        }
        
        $funcName = "Is". ucfirst($rule['type']);
        if(!method_exists($this, $funcName))
        {
        	$this->_error = $this->_notExistTypeError;
        	return FALSE;
        }
        	
        if(!self::$funcName($value))
        {
        	$rstMsg = $this->_Msg($rule);
            return FALSE;
        }

        foreach ($this->_acts as $act) 
        {
        	if(!isset($rule[lcfirst($act)]))
        	{
        		continue;
        	}
        	
        	if(!method_exists($this, $act))
        	{
        		$rstMsg = $this->_notExistFuncError;
            	return FALSE;
        	}
            if (!self::$act($value, $rule[lcfirst($act)])) 
            {
                $rstMsg = $this->_Msg($rule);
            	return FALSE;
            }
        }

        return true;
    }

    /**
     * Get error message
     *
     * @param array $rule
     * @param string $name
     * @return string
     */
    private function _Msg($rule)
    {
    	return (!empty($rule['msg']) && is_string($rule['msg'])) ? $rule['msg'] : $this->_defaultMsg;
    }
    
    /**
     * 验证合法用户姓名
     *
     * @param array $rule
     * @param string $name
     * @return string
     */
    static function IsUserName($name)
    {
    	//$regex = '/^([\u4E00-\uFA29]|[\uE7C7-\uE7F3]|[a-zA-Z0-9])*$/';
    	//$regex = '/^([\x{4e00}-\x{9fa5}]|[a-zA-Z0-9_])*$/';
    	$regex = '/^[\x{4e00}-\x{9fa5}]|[a-zA-Z0-9]+$/u';
    	return self::Regexp($name, $regex);
    }
    
    /**
     * 验证合法手机号码
     *
     * @param array $rule
     * @param string $name
     * @return string
     */
    static function IsMobile($mobile)
    {
    	$regex = '/^0?1[0-9]{10}$/';
    	return self::Regexp($mobile, $regex);
    }
    
    static function IsValidFileType($fileName, $validType='')
    {
    	if (empty($validType))
    	{
    		return TRUE;
    	}
    	
		$fileTypeArr = is_array($validType) ? $validType : array($validType);
    	
    	$lastdot = strrpos($fileName, ".");        //取出.最后出现的位置
    	$extended = substr($fileName, $lastdot+1); //取出扩展名
    	//转换大小写并检测
    	$extended = strtolower($extended);
    	
    	return in_array($extended, $fileTypeArr) ? TRUE : FALSE;
    }
}