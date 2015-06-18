<?php
/**
 * @info: api提供前台数据【卡牌】
 * @info: 数据来源后台卡牌库mfcart【dataset】
 * @author yongze
 *
 */

class ApiController extends Controller {
    
    private $_id = null;//表模型ID
	public function init(){
	    $arr = [
	        ["name"=>'cyz'],
	        2,
	        5,
	    ];
	    $this->_id = 19;//卡牌
	    
	}
	
	public function actionIndex() {
	    
	   $info = array();
// 	   $itemModel = $this->loadModel((int)$this->_id, 'item', 'dataset_id', true);
	   $dsModel = $this->loadModel((int)$this->_id, 'ds');					//获取表模型
	   $dsModel = $dsModel->sortField();
	   $criteria = new EMongoCriteria();
	   $criteria->dataset_id = $this->_id;
// 	   FunctionUTL::Debug($dsModel['fields']);
	   //添加查询条件
	   $this->fillCond($criteria, $dsModel['fields']);
// 	   FunctionUTL::Debug($criteria);
// 	   FunctionUTL::Debug($criteria->getConditions());
	   $count = CardItem::model()->count($criteria);
	   $pages = new CPagination($count);
	   $perPage = 10;
	   $pages->pageSize = $perPage;
	   //$pages->applyLimit($criteria);
	   $offset = isset($_GET['page']) ? intval($_GET['page']) : 1;
	   $offset = ($offset - 1) * $perPage;
	   $criteria->limit($perPage)->offset($offset)->sort('id', EMongoCriteria::SORT_DESC);
	   $itemModel = CardItem::model()->findAll($criteria);
// 	   FunctionUTL::Debug($itemModel);

	   $info = array();
	   if(!empty($itemModel))
	   {
	       foreach ($itemModel as $itemObj)
	       {
	           if(empty($itemObj->data))
	           {
	               continue;
	           }
	           
	          $info[] =$itemObj->data; 
	       }
	   }
	   
// 	   FunctionUTL::Debug($info);
	   echo FunctionUTL::ToJson($info);
	}
	
	/**
	 * @info:内容【详细】
	 */
	public function actionDetail(){}
	
	/**
	 * @info:【搜索】
	 */
	public function actionSearch(){}
	

	/**
	 * 填充提交过来的筛选条件
	 * @param $criteria	EMongoCriteria	填充前的查询器
	 * @param $fields	array	字段定义
	 * @return EMongoCriteria	填充后的查询器
	 */
	private function fillCond(&$criteria, $fields){
	    
	    parse_str($_SERVER['QUERY_STRING']);
// 	    FunctionUTL::Debug($fields);
	    $_fieldKey = array_keys($fields);
// 	    FunctionUTL::Debug($_fieldKey);
	    foreach ($_fieldKey as $val)
	    {
	        $kfield = $val;			//字段名
	        $koperator = '==';	//操作符
	        $kword = $$val;				//值
	        //检验-字段名和操作符必选
	        if(empty($kfield) || empty($kword)){
	            continue;
	        }
	        
	        //检查-字段有定义，且不是字段组
	        if($kfield!='id' && (!isset($fields[$kfield]) || $fields[$kfield]['type']=='group')){
	            continue;
	        }
	        
// 	        FunctionUTL::Debug($kfield);
	        //字段定义
	        $field_info = $fields[$kfield]['extra']['field_info'];
	        $type = $field_info['addition_type'];
	        //字段名处理
	        if($kfield!='id'){
	            $kfield = 'data.'.$kfield;
	         }
	        
// 	        FunctionUTL::Debug($type);
	        //根据提交参数添加条件
	        $this->makeCond($criteria, $type, $kfield, $koperator, $kword);
	    }
	    
	    return '';

	}
	
	function actionUnzip(){
    	$zip = new ZipArchive;//新建一个ZipArchive的对象
        /*
        通过ZipArchive的对象处理zip文件
        $zip-&gt;open这个方法的参数表示处理的zip文件名。
        如果对zip文件对象操作成功，$zip-&gt;open这个方法会返回TRUE
        */
    	FunctionUTL::Debug($zip);
//     	FunctionUTL::Debug($zip->open('test.zip'));exit;
        if ($zip->open('test.zip') === TRUE)
        {
            $res = $zip->extractTo('/upload/aaa');//假设解压缩到在当前路径下images文件夹的子文件夹php
            FunctionUTL::Debug($res);
            $zip->close();//关闭处理的zip文件
        }
	}

}
