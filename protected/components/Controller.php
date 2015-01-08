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
		$controller = Yii::app()->controller->id;
		$noCheck = array('site', 'card');
		if (Yii::app()->user->name=='Guest' && !in_array($controller, $noCheck)) {
			$this->redirect(array('Site/login'));
		}
		//如果是建库人员，则使用column1布局
		if (Yii::app()->user->name == 'creator') {
			$this->layout = '//layouts/column1';
		}
	}

	/**
	* 左侧栏树形结构
	* @author gentle
	*/
	public function dataTree($databaseId = 0) {
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
			$datasets = CardDs::model()->findAllByAttributes(array('database_id'=>(int)$value->id));
			if (empty($datasets)) {
				continue;
			}
			foreach($datasets as $k => $v) {
				$data[$key]['children'][$k] = array('text' => '<a href="'.$this->createUrl('/CardItem/Index/id/'.$v->id).'">'.$v->name.'</a>');
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
	    $modelClass = '';
	    switch ($type) {
	    	case 'db':
			$modelClass = 'CardDb';
			break;
		case 'ds':
			$modelClass = 'CardDs';
			break;
		case 'item':
			$modelClass = 'CardItem';
			break;
	    }
	    
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
		$url = array('index');		//控制器首页
		if(isset($_SERVER['HTTP_REFERER'])){
			Yii::app()->user->returnUrl = $_SERVER['HTTP_REFERER'];
			if($data){
				Yii::app()->user->returnUrl .= '?&'.http_build_query($data);
			}
			$url = Yii::app()->user->returnUrl;
		}
     	$this->redirect($url);	//返回上一页
	}
	
	
	

}
