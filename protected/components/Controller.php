<?php
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column2';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	/**
	* 左侧树形菜单
	* @author gentle
	*/
	public $leftTree=array();


	/**
	* 初始化程序
	* @author gentle
	*/
	public function init() {
		$controller = strtolower(Yii::app()->controller->id);
		$module = $this->module->id;
		$noCheck = array('site');
		//用户未登录，且当前访问不是匿名允许的控制器或模块，则跳回登陆页
		if (empty(Yii::app()->user->id) && !in_array($controller, $noCheck) && $module!=='api') {
			$this->redirect(array('/site/login'));
		}
	}

	/**
	* 左侧栏树形结构
	* @author gentle
	*/
	public function dataTree($databaseId = 0, $action='cardItem/Index') {
		$data = array();
		$databases = CardDb::model()->findAll();
		foreach ($databases as $key => $value) {
			$data[$key]['text'] = '<span>'.$value->name.'</span>';
			if ($databaseId == $value->id) {
				$data[$key]['expanded'] = true;
			} else {
				$data[$key]['expanded'] = false;
			}
			$data[$key]['children'] = array();
			
			$dscriteria = User::model()->getScopeDsCriteria();
			$dscriteria->addCond('database_id', '==', (int)$value->id);
			$datasets = CardDs::model()->findAll($dscriteria);
			if (empty($datasets)) {
				continue;
			}
			foreach($datasets as $k => $v) {
				$data[$key]['children'][$k] = array('text' => '<a href="'.$this->createUrl('/'.$action.'/id/'.$v->id).'">'.$v->name.'</a>');
			}
		}
		return $data;
	}

	/**
	 * 载入model
	 * @param integer $id 元素集id
	 * @param string $type model类型
	 * @return Category the loaded model
	 * @throws CHttpException
	 * @author gentle
	 */
	public function loadModel($id, $type, $fieldType = 'id', $all = false) {
	    //$model = CardDs::model()->findByPk(new MongoID($id));
	    //缩写对照表（特殊）
	    $map = array(
	    	'db'=>'CardDb',
	    	'ds'=>'CardDs',
	    	'item'=>'CardItem',
	    );
	    
	    //使用缩写的转换为类名，其它大写首字母直接使用
	    $modelClass = isset($map[$type])?$map[$type]:ucfirst($type);
	    
	    if ($all) {
	    	$model = $modelClass::model()->findAllByAttributes(array($fieldType=>(int)$id));
	    } else {
	    	$model = $modelClass::model()->findByAttributes(array($fieldType=>(int)$id));
	    }

	    //if ($model === null)
		//throw new CHttpException(404, '请求的记录不存在');
	    return $model;
	}


	/**
	* 提示信息
	* @author gentle
	*/
	public function promptInfo() {
		$successInfo = Yii::app()->user->getFlash("success");
		$errorInfo = Yii::app()->user->getFlash("error");
		$info = array();
		if (!empty($successInfo)) {
			$info['type'] = 'success';
			$info['msg'] = $successInfo;
		}
		if (!empty($errorInfo)) {
			$info['type'] = 'error';
			$info['msg'] = $errorInfo;
		}
		return $info;
	}
	
	/**
	 * 根据结果集导出一个xls文件
	 * @param $rs	数据结果集
	 * @param $name 输出文件名
	 * @return unknown_type
	 */
	protected function outputCsv($rs, $file_name){
		
		$str = array();
		foreach($rs as $key=>$vo){
			foreach($vo as $vk=>$vv){
				//清理掉在CSV中有特殊含义的字符
//				if($key==63 && $vk==1){
//					var_dump($vv);
//					$vv = '11'.$vv;
//				}
				$vv = str_replace(array(",", "\r\n", "\n", "\r", "\""), array("，", "", "", "", "“"), $vv);
				//$vv = str_replace("\n", "", $vv);
//				if($key==63 && $vk==1){
//					var_dump($vv);
//				}
				$vo[$vk] = "\"".trim($vv)."\"";
			}
//			if($key==63){
//				var_dump($vo);
//			}
			
			//$str[] = iconv('utf-8', 'gbk//IGNORE', implode(',', $vo));	//转码
			$str[] = mb_convert_encoding( implode(',', $vo), "GBK", "utf-8");
		}
//				var_dump($rs[63]);
//				var_dump($str[63]);
//				exit();
		
		
		$str = implode("\n", $str);
	    header("Content-type:text/csv");   
	    header("Content-Disposition:attachment;filename=".iconv('utf-8','gbk',$file_name.'.csv'));   
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
	    header('Expires:0');   
	    header('Pragma:public');
	    echo $str;
	    exit();
	}
	
	/**
	 * 获取一个导入内的数据
	 * @param $file_url
	 * @return unknown_type
	 */
	protected function inputCsv($file_url){
		$rs = array();
		$file = fopen($file_url, 'r');
		while($data = fgetcsv($file)){
			if($data){
				$rs[] = $data;
			}	
		}
		fclose($file);
       	return $rs;
	}
	
	
	/**
	 * 根据结果集导出一个xls文件
	 * @param $rs	数据结果集
	 * @param $name 输出文件名
	 * @return unknown_type
	 */
	protected function outputXls($rs, $file_name){
		//数组字段处理，用','连接
		if(isset($rs[1]) && $rs[1]){	//第一行是否存在
			//循环每一行
			foreach($rs as $key=>$vo){
				if(empty($key)){continue;}	//跳过第一行
				foreach($vo as $ak=>$av){
					$rs[$key][$ak] = isset($vo[$ak])?''.(is_array($vo[$ak])?implode(',', $vo[$ak]):$vo[$ak]):'';	//赋值
				}
			}
		}

		//导入phpexcel主文件并启用自加载
		spl_autoload_unregister(array('YiiBase', 'autoload'));
		//require_once Yii::getPathOfAlias('application.vendors.Excel').'/PHPExcel.php';
		Yii::import('application.vendors.Excel.PHPExcel', true);
		
		$rs_rows = count($rs);								//行数
		$rs_clos = count((isset($rs[0])?$rs[0]:array()));	//列数
		
		//生成文件
		$objPHPExcel = new PHPExcel();		//创建xls
		$objActSheet = $objPHPExcel->setActiveSheetIndex(0);	//工作表对象
		$objActSheet->fromArray( $rs, NULL);
		
		//格式处理（日期）
		$pre = 0;		//前置符号,处理超过26列的情况
		$no = 0;
		$cloname = 'A';	//最后一个列号
		//表头不为空才进行处理
		if($rs_clos){
			foreach($rs[0] as $name){
				$format = '';
				if(strpos($name, '日期')!==false){
					$format = 'yyyy-mm-dd';
				}else if(in_array($name, array('添加时间','更新时间'))){	//时间文字容易出现，暂时隐藏时间格式设置
					$format = 'yyyy-mm-dd hh:mm:ss';
				}
				
				$cloname = empty($pre)?chr(65+$no):chr($pre).''.chr(65+$no);
				if($format){
					$objStyleDate = $objActSheet->getStyle($cloname.'2');
					$objStyleDate->getNumberFormat()->setFormatCode($format);
					$objAlignDate = $objStyleDate->getAlignment();  
					$objAlignDate->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  		//对齐
					$objActSheet->duplicateStyle($objStyleDate, $cloname.'2:'.$cloname.''.$rs_rows);
					$objActSheet->getColumnDimension($cloname)->setWidth(12);						//指定字符宽度
				}
				
				//到达Z后，启用前缀
				if(chr(65+$no)==='Z'){
					if(empty($pre)){
						$pre = 65;
					}else{
						$pre++;
					}
					$no = 0;	//重置编号
				}else{
					$no++;		//编号加1
				}
			}
		}

		//表头配色
		$objStyleA1 = $objActSheet->getStyle('A1');
		$objFillA1 = $objStyleA1->getFill();  
		$objFillA1->setFillType(PHPExcel_Style_Fill::FILL_SOLID);  	//填充类型
		$objFillA1->getStartColor()->setARGB('FFEEEEEE');  			//填充颜色
		$objAlignA1 = $objStyleA1->getAlignment();  
		$objAlignA1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  	//对齐
		$objActSheet->duplicateStyle($objStyleA1, 'A1:'.$cloname.'1');				//从指定的单元格复制样式信息.  
		$objActSheet->duplicateStyle($objStyleA1, 'A2:'.$cloname.'2');				//从指定的单元格复制样式信息.  
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		
		ob_clean();		//清理现有输出
		spl_autoload_register(array('YiiBase', 'autoload'));
		header("Content-Type: application/force-download");  
		header("Content-Type: application/octet-stream");  
		header("Content-Type: application/download");  
		header('Content-Disposition:inline;filename="'.iconv('UTF-8', 'GBK', $file_name.'.xls"'));  
		header("Content-Transfer-Encoding: binary");  
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");  
		header("Pragma: no-cache");  
		$objWriter->save('php://output');
	}
	
	/**
	 * 获取一个导入内的数据
	 * @param $file_url
	 * @return unknown_type
	 */
	public function inputXls($file_url){
		//导入phpexcel主文件并启用自加载
		spl_autoload_unregister(array('YiiBase', 'autoload'));
		//require_once Yii::getPathOfAlias('application.vendors.Excel').'/PHPExcel.php';
		Yii::import('application.vendors.Excel.PHPExcel', true);
		$objPHPExcel = PHPExcel_IOFactory::load($file_url);
		spl_autoload_register(array('YiiBase', 'autoload'));
		return $objPHPExcel->setActiveSheetIndex(0)->toArray(null,true,true,true);	//获取第一个工作表信息
	}
	
	/**
	 * 返回上一页
	 * @return null
	 */
	public function redirect_back($data=array()){
		$url = array('site/index');		//控制器首页
		if(isset($_SERVER['HTTP_REFERER'])){
			Yii::app()->user->returnUrl = $_SERVER['HTTP_REFERER'];
			if($data){
				Yii::app()->user->returnUrl .= '?&'.http_build_query($data);
			}
			$url = Yii::app()->user->returnUrl;
		}
     	$this->redirect($url);	//返回上一页
	}
	
	/**
	 * 将数组转为一维列表
	 * @param $rs	array	数据集
	 * @param $val	str		做为值的数据键值
	 * @param $key	str		做为键的数据键值
	 * @return array 一维列表
	 */
	public function list_from_rs($rs,$val='id',$key=''){
		$list = array();
		if(empty($rs)){
			return $list;
		}
		
		foreach($rs as $k => $v){
			if(is_object($v)){
				$v = $v->toArray();
			}
			
			if(isset($v[$val])){
				if(empty($key)){
					$list[$k] = $v[$val];
				}else{
					$list[''.$v[$key]] = $v[$val];
				}
			}
		}
		
		return $list;
	}
	
	/**
	 * 获取登录用户信息
	 * @param $key 属性值
	 * @param $key1 二级属性值
	 * @return unknown_type
	 */
	public static function get_login_user($key='',$key1=false){
		$rs = 0;
		if(!Yii::app()->user->isGuest){
			$rs = Yii::app()->user->getState('info');
			if(!empty($key)){
				$rs = isset($rs[$key])?$rs[$key]:'';
				if($key1!==false){
					$rs = isset($rs[$key1])?$rs[$key1]:'';
				}
			}
		}
		return $rs;
	}
	
	/**
	 * 权限验证方法
	 * @param $action_no	string	权限代码
	 * @param $rebool		bool	是否返回布尔值，false时会跳转到上一页
	 * @return 
	 */
	public function actCheck($action_no, $rebool=true){
		$rs = false;
		if(Yii::app()->user->id){
			$username = $this->get_login_user('username');
			$role = $this->get_login_user('role');
			$actions = $this->get_login_user('actions');
			//当前用户拥有该权限，名为admin的管理员拥有所有权限  && creator 的管理员
			if(in_array($action_no, $actions) || ($role=='10'&&$username=='admin')||($role=='10'&&$username=='creator')){
				$rs = true;
			}
		}
		
		//未通过验证且不返回bool的时候进行跳转
		if($rs==false && $rebool==false){
			$this->redirect_back();
		}
		
		return $rs;
	}
	
	/**
	 * 权限验证方法
	 * @param $action_no	string	权限代码
	 * @param $rebool		bool	是否返回布尔值，false时会跳转到上一页
	 * @return 
	 */
	public function scopeCheck($db, $ds=null){
		$rs = false;
		
		//名为admin的管理员拥有所有权限
		if($this->get_login_user('role')=='10'&& $this->get_login_user('username')=='creator'){
			return true;
		}
		
		$dbInfo = $this->get_login_user('scopeInfo', 'db');
		$dsInfo = $this->get_login_user('scopeInfo', 'ds');
		
		//若拥有所有db，则直接获取所有范围
		if($dbInfo=='all'){
			$rs = true;
		}else{
			if($ds==null){
				//库对应
				if(in_array($db, $dbInfo)){
					$rs = true;
				}
			}else{
				//库对应且，表也对应
				if(in_array($db, $dbInfo) && ($dsInfo=='all' || in_array($ds, $dsInfo))){
					$rs = true;
				}
			}
		}
		
		if($rs==false){
			$this->redirect_back();
		}
	}
	
	/**
	 * 添加一条操作日志
	 * @param $obj_cate		string	对象类型
	 * @param $obj_id		string	对象id	
	 * @param $txt			string	操作备注
	 * @return unknown_type
	 */
	public function addLog($obj_cate, $obj_id, $txt){
		$mod = new Log();
		$mod->obj_cate = $obj_cate;
		$mod->obj_id = (int)$obj_id;
		$mod->txt = $txt;
		return $mod->save();
	}
	
	/**
	 * 为查询器生成一个查询条件
	 * @param $criteria	过滤器
	 * @param $type		字段类型
	 * @param $kfield	字段英文名
	 * @param $koperator操作符号
	 * @param $kword	筛选值
	 * @return $criteria 添加后的过滤器
	 */
	public function makeCond(&$criteria, $type, $kfield, $koperator, $kword){
		//操作符处理
    	switch($koperator){
	    	case '==':
		    case '!=':
		    	if($kfield=='id'){
		    		$kword = intval($kword);
		    	}
		    	$criteria->addCond($kfield, $koperator, $kword);
		        break;
		    case '>':
			case '<':
				//数字比较需要先转换类型
				if($type=='number' || $kfield=='id'){
					$kword = intval($kword);
				}else if($type=='date'){
					$kword = strtotime($kword);
				}
				$criteria->addCond($kfield, $koperator, $kword);
		        break;
		  	case 'regex':
		  		$criteria->$kfield = new MongoRegex('/'.$kword.'/i');
		        break;
		   	case 'in':
		   	case 'notin':
		   	case 'all':
		   		//echo $kfield;
		   		//'data.fglx'=>array('$nin'=>array('保暖')
		   		$kword = explode(',', $kword);
		   		$criteria->addCond($kfield, $koperator, $kword);
		        break;
		    default:
		        return $criteria;
    	}
    	return $criteria;
	}
	
	/**
	 * 转换编码从 json 到 utf-8
	 * @param string $json string to convert
	 * @return string utf-8 string
	 */
	public function json_unicode_utf8($json){
		$json = preg_replace_callback("/\\\u([0-9a-f]{4})/", create_function('$match', '
			$val = intval($match[1], 16);
			$c = "";
			if($val < 0x7F){        // 0000-007F
				$c .= chr($val);
			} elseif ($val < 0x800) { // 0080-0800
				$c .= chr(0xC0 | ($val / 64));
				$c .= chr(0x80 | ($val % 64));
			} else {                // 0800-FFFF
				$c .= chr(0xE0 | (($val / 64) / 64));
				$c .= chr(0x80 | (($val / 64) % 64));
				$c .= chr(0x80 | ($val % 64));
			}
			return $c;
		'), $json);
		return $json;
	}
	
	/**
	 * 将一维列表转为下拉框选项
	 * @param $list	array	数据源
	 * @param $def	str		默认选中值
	 * @return str		下拉框选项html
	 */
	public function option_from_list($list, $def='0'){
		$str = '<option value="0">请选择</option>';
		//为空则返回提示
		if(empty($list)){
			return '<option value="0">没有数据</option>';
		}

		foreach($list as $k => $v){
			$isg = false;	//二维标示
			//二维处理
			if(is_array($v)){
				$str .= '<optgroup label="'.$k.'">';
				foreach($v as $kk=>$vv){
					$str .= $this->_option_select($kk, $vv, $def);
				}
				$str .= '</optgroup>';
			}else{
				$str .= $this->_option_select($k, $v, $def);
			}
			
		}
		return $str;
	}
	
	/**
	 * 下拉框选中判定
	 * @param string $key	选项键值
	 * @param string $val 	选项名称
	 * @param string/array	$def	以选中的默认值
	 * @return string 一个选项的html
	 */
	public function _option_select($key, $val,$def='0'){
		if(empty($key)&&empty($val)){
			return '';
		}
		$sub_html = '';
		if((!is_array($def)&&''.$def==$key) || (is_array($def) && in_array($key, $def))){
			$sub_html = ' selected="selected" ';		
		}
		return '<option '.$sub_html.' value="'.$key.'">'.$val.'</option>';
	}
}
