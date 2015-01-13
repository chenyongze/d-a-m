<?php
/**
 * 备份和还原
 * @author Gavin
 */
class DumpController extends Controller {
	
	public $dirpath = 'webroot.file.dump';
	
	public function init(){
		$this->actCheck('dump', false);
		$this->layout = '//layouts/column1';
	}
	
	/**
	 * 游戏数据管理
	 * 用来管理所有游戏的数据备份
	 */
	public function actionIndex(){
		header("Location: /dump/export");exit();
		$rs = $this->findDir($this->dirpath, 'js');
		$exe_msg = $this->_exe_message();
		$this->render('index', array('rs'=>$rs, 'exe_msg'=>$exe_msg));
	}
	
	/**
	 * 库数据导出
	 * 导出及备份系统数据
	 * { "v" : 1, "key" : { "code" : 1 }, "unique" : true, "ns" : "gamedb_test.game", "name" : "code_" }
	 * { "_id" : { "$oid" : "51adb8139b2a950402000008" }, "code" : "bns11", "name" : "剑灵11", "addtime" : "2013-06-04 17:49:07", "status" : 1 }
	 */
	public function actionExport(){
		$selectedTables = isset($_POST['checked'])?$_POST['checked']:array();
		
		//获取数据库对象和所有文档列表
		$db = CardItem::model()->getDb();
		$tables = $this->_listCollections($db);		//获取文档列表
		//获取需要导出的文档列表，并导出
		if(!empty($_POST)){
			//循环处理勾选文档
			foreach ($selectedTables as $collection) {
				$contents =  "";											//内容
				//indexes 获取文档索引
				$collObj = $db->selectCollection($collection);	//文档对象
				$infos = $collObj->getIndexInfo();				//索引信息
				foreach ($infos as $info) {
					if(!isset($info['key']['_id'])){				//非“_id”索引才保留
						$contents .= 'index'.$this->json_unicode_utf8(json_encode($info)) . "\n";	//标准json格式的index语句
					}
				}
				
				//data	获取文档数据
				$cursor = DBModel::model()->getfind($collection);				//查询所有文档数据
				foreach ($cursor as $one) {
					$contents .= str_replace('"_id":{"$id":"', '"_id":{"$oid":"', $this->json_unicode_utf8(json_encode($one))) . "\n";	//将$id变为$oid键
				}
				
				unset($cursor);
				//生成文件
				$addtime = time();
				$fie_name = yiiBase::getPathOfAlias($this->dirpath).'/'.date("YmdHis", $addtime).'-'.$collection.'.js';
				file_put_contents($fie_name, $contents);
				//保存入库
				$image = new File();
				$image->filename = $fie_name;
				$image->metadata = array('name'=>$collection.'.js', 'addTime'=>$addtime);
				$res = $image->save();
				unlink($fie_name);	//删除临时文件
				
				//多文件无法循环下载，暂时在服务器上生成文件
				//$this->_output_json();
			}
		}
		
		//扫描导出文件并获取列表
		$exportTables = $this->findDir($this->dirpath, 'js');
		$exe_msg = $this->_exe_message();
		$this->render('export', array('tables'=>$tables,'selectedTables'=>$selectedTables,'exportTables'=>$exportTables,'exe_msg'=>$exe_msg));
	}
	
	/**
	 * 文档数据导出
	 * 导出及备份系统制定字段的数据
	 * 20130419 容易导致混乱，有整体导出即可，本功能暂停开发
	 */
	public function actionCExport(){
		exit();
		$tableName = isset($_REQUEST['name'])?$_REQUEST['name']: '';					//表明
		//验证表明是否为实体表
		if(!preg_match('/^item/i', $tableName)){
			$this->redirect_back();
		}
		
		$selectedFields = isset($_POST['checked'])?$_POST['checked']:array();		//选中
		//获取数据库对象和所有字段列表
		$db = CardItem::model()->getDb();
		$nameInfo = explode('_', $tableName);

		$attr = Attr::model()->getAll($nameInfo[1], $nameInfo[2]);
		$fields = array_merge(array('name', 'edittime', 'addtime'), array_keys($attr));		//获取字段列表
		
		//获取需要导出的字段列表，并导出
		if(!empty($_POST)){
			$contents =  "";											//内容
			array_unshift($selectedFields, '_id');						//加入必选字段id
			//data	获取文档数据
			$cursor = $db->selectCollection($tableName)->find(array(), $selectedFields);	//查询指定字段数据
			
			foreach ($cursor as $one) {
				$one_id = is_object($one['_id'])?''.$one['_id']:$one['_id'];	//id类型处理
				unset($one['_id']);
				//$contents .= '{_id:"'.$one_id.'"},{$set:'.$this->_exportJSON($one).'}'."\n";
				$contents .= $this->json_unicode_utf8(json_encode(array('_id'=>$one_id))).',{"$set":'.$this->json_unicode_utf8(json_encode($one)).'}'."\n";
			}
			unset($cursor);
			
			$fiels_ext = '';
			array_shift($selectedFields);		//移出顶部的_id
			$sfcount = count($selectedFields);	//字段数量
			if($sfcount>2){
				$fiels_ext = $selectedFields[0].','.$selectedFields[1].'...['.$sfcount.']';
			}else{
				$fiels_ext = implode(',', $selectedFields);
			}
			file_put_contents(yiiBase::getPathOfAlias($this->dirpath.'.export').'/'.date("YmdHis").'-'.$tableName.'-'.$fiels_ext.'.js', $contents);
		}
		
		//扫描导出文件并获取列表
		$exportTables = $this->findDir($this->dirpath.'.export', 'js');
		$exe_msg = $this->_exe_message();
		$this->render('cexport', array('fields'=>$fields,'selectedFields'=>$selectedFields,'tableName'=>$tableName,'exportTables'=>$exportTables,'exe_msg'=>$exe_msg));
		
	}
	
	/**
	 * 在文件夹中查找文件
	 * @param $url string 文件夹所在路径（yii格式,如：application.data.game）
	 * @param $ext string 扩展名过滤（默认为不过滤）
	 * @return array 所有文件的列表
	 */
	public function findDir($url, $ext=false, $order=false){
		$rs = array();		//初始化结果集
		$dir = yiiBase::getPathOfAlias($url);	//文件夹路径
		
		//数据库查询
		$criteria = new EMongoCriteria();
		$criteria->filename = new MongoRegex('#^'.$dir.'.*\.'.$ext.'$#i');	//按目录前缀查询
		$files = File::model()->findAll($criteria);
		foreach($files as $fv){
			$rs[$fv->_id.''] = array(
				'id'=>$fv->_id.'',				//名称
        		'name'=>$fv->metadata['name'],		//全名
        		'edittime'=>date('Y-m-d H:i:s', $fv->metadata['addTime']),	//修改时间
        		'size'=>$fv->getSize()/1000,	//文件大小
			);
		}
		
		/*文件夹扫描
		 * if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
				$i = 0;
		        while (($file = readdir($dh)) !== false) {
		        	if(!is_dir($dir.'/'.$file) && !in_array($file, array('.', '..', '.svn'))){
		        		$source_name = $file;
		        		//$file = $this->toAppCharset($file);
		        		$finfo = pathinfo($file);
		        		if($ext===false || (isset($finfo['extension']) && $finfo['extension']==$ext)){
		        			$edit_time = filemtime($dir.'/'.$source_name);
		        			$rs[$file] = array(
		        				'id'=>$finfo['filename'],			//名称
		        				'name'=>$file,						//全名
		        				'edittime'=>date('Y-m-d H:i:s', $edit_time),	//修改时间
		        				'size'=>filesize($dir.'/'.$source_name)/1000,	//文件大小
		        			);
		        		}
		        	}
					$i++;
		        }
		    	closedir($dh);
		    	krsort($rs);
		    }
		}*/

		return $rs;
	}
	
	/**
	 * 下载文件
	 * @param $fileUrl	string	文件url
	 * @return null
	 */
	public function downloadFile($file){
		header("Content-length: ".$file->getSize());
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' .  date('YmdHis',$file->metadata['addTime']).'-'.$file->metadata['name'] . '"');
		echo $file->getBytes();
		//readfile("$fileUrl");
		exit();
	}
	
	/**
	 * 导入结果信息
	 * @return string 内容
	 */
	public function _exe_message(){
		$msg = '';
		$exers = isset($_GET['exers'])?$_GET['exers']:'';
		if($exers){
			$rs = explode('#', $exers);
			$msg = '导入结果：索引'.$rs[1].'/'.($rs[0]+$rs[1]).'，新增'.$rs[3].'/'.($rs[2]+$rs[3]).'，更新'.$rs[5].'/'.($rs[4]+$rs[5]).'。';
		}
		return $msg;
	}
	
	/**
	 * 获取数据库中的文档列表
	 * @param MongoDB $db DB
	 * @return array<MongoCollection>
	 */
	public function _listCollections(MongoDB $db) {		
		//获取所有文档列表
		$names = array();
		try {
			//$names = $db->execute('function (){ return db.getCollectionNames(); }');	//生产线不支持js格式mongo指令
			$names = $db->getCollectionNames();
		} catch(Exception $e) {
			print_r($names);
			echo $e->errorMessage();exit();
		}
		
		//获取非系统文档
		$ret = array();
		$inlay_tabse = Yii::app()->params['inlay_tabse'];
		foreach ($names as $name) {
			//屏蔽掉系统文档和item
			if (preg_match("/^(system\\.|auto|file\\.)/", $name)) {
				continue;
			}
			$ret[$name] = isset($inlay_tabse[$name])?$inlay_tabse[$name]:$name;
		}
		
		//拆分item表
		$dsmap = CardDs::model()->getDBDSMap();	//获取游戏表对应
		foreach($dsmap as $dv){
			foreach($dv['list'] as $tv){
				$ret['item_'.$dv['en_name'].'_'.$tv['en_name']] = '数据表_'.$dv['name'].'_'.$tv['name'];
			}
		}
		
		$names = $ret;
		return $names;
	}
	
	/**
	 * 添加一个文件
	 * @param $url
	 * @return unknown_type
	 */
	public function actionAddFile(){
		if(empty($_FILES['file']['tmp_name'])){
			throw new CHttpException(403,'你没有选择要导入文件！');
		}
		@move_uploaded_file($_FILES['file']["tmp_name"], yiiBase::getPathOfAlias($this->dirpath).'/'.$_FILES['file']['name']);
		$this->redirect_back();
	}
	
	/**
	 * 查看一个文件
	 * @param $url
	 * @return unknown_type
	 */
	public function actionViewFile(){
		$id = isset($_GET['id'])?$_GET['id']:'';
		$file = File::model()->findByPk(new MongoId($id));
		if($file instanceof File){
			echo $file->getBytes();
		}else{
			echo '没有这个文件';
		}
		exit();
	}
	
	/**
	 * 删除一个文件
	 * @param $url
	 * @return unknown_type
	 */
	public function actionDeleteFile(){
		$id = isset($_GET['id'])?$_GET['id']:'';
		File::model()->deleteByPk(new MongoId($id));
		//$name = isset($_GET['name'])?$_GET['name']:'';
		//@unlink(yiiBase::getPathOfAlias($this->dirpath).'/'.$name);
		$this->redirect_back();
	}
	
	/**
	 * 下载一个文件
	 * @param $url
	 * @return unknown_type
	 */
	public function actionDownFile(){
		$id = isset($_GET['id'])?$_GET['id']:'';
		$file = File::model()->findByPk(new MongoId($id));
		$this->downloadFile($file);
		//$name = isset($_GET['name'])?$_GET['name']:'';
		//$this->downloadFile(yiiBase::getPathOfAlias($this->dirpath).'/'.$name);
		$this->redirect_back();
	}
	
	/**
	 * 清空一个数据表
	 * @param $url
	 * @return unknown_type
	 */
	public function actionTableRemove(){
		$name = isset($_GET['name'])?$_GET['name']:'';
		DBModel::model()->getRemove($name);	//drop会删除集合，remove仅清空记录
		$this->redirect_back();
	}
	
	/**
	 * 删除一个数据表
	 * @param $url
	 * @return unknown_type
	 */
	public function actionTableDrop(){
		$name = isset($_GET['name'])?$_GET['name']:'';
		$rows = DBModel::model()->getDb()->selectCollection($name)->count();
		if($rows){
			throw new CHttpException(404,'不能删除数据不为空的表！');
		}
		DBModel::model()->getDb()->selectCollection($name)->drop();	//drop会删除集合，remove仅清空记录
		$this->redirect_back();
	}
	
	/**
	 * 同步json数据到相应数实体表
	 * 用来使用js更新表数据，操作需谨慎
	 * 普通索引： 	index{"RealCost":1},[]
	 * 唯一索引：	index{"ItemShopTags":1},{"unique":true}
	 * 新数据：		{"_id":"item_blink","ItemShopTags":["传送"],"Lines":[],"RealCost":2150,"Category":"奥术"}
	 * 更新数据：	{"_id":"item_blink"},{"$set":{"ItemShopTags":["传送","防具"]}}
	 */
	public function actionSynJStoDB(){
		$js_id = isset($_GET['id'])?$_GET['id']:'';		//待同步文件名路径
		$file = File::model()->findByPk(new MongoId($js_id));
		if(!$file){
			throw new CHttpException(404,'该数据文件不存在。');
		}
		
		$js_name = pathinfo($file->metadata['name']);						//真实文件名
		$js_name = $js_name['filename'];
		
		$exers = array('i'=>array(0,0),'a'=>array(0,0),'u'=>array(0,0));
		
		if($file){
			$id_info = explode('-', $js_name);
			
			//剔除文件名中的日期
//			foreach($id_info as $namekey=>$namevo){
//				if(preg_match('/^\d{14}$/i', $namevo)){
//					unset($id_info[$namekey]);
//					$cname = implode('-', $id_info);
//				}
//			}
			
			
			//确认集合名称
			if($id_info>1){
				$tables = $this->_listCollections(DBModel::model()->getDb());
				foreach($id_info as $namevo){
					//集合名称：当前库中存在该集合或者以‘item_’开头
					if(isset($tables[$namevo])){
						$cname = $namevo;
						break;
					}
				}
			}
			
			//获取并执行导入新数据
			$body = $file->getBytes();						//获取文件内容
			$lines = preg_split('/(\r\n|\n)/i', $body);		//每行作为一个语句
			
			//若仅为集合名称，则清空该集合内容,修改为独立操作
			//if(count($id_info)==1){
			//	CardItem::model()->getDb()->selectCollection($cname)->remove();	//drop会删除集合，remove仅清空记录
			//}
			
			//循环进行插入/更新/创建索引
			foreach ($lines as $line) {
				$line = trim($line);
				if (!empty($line)) {
					//判定是否为索引，若是则创建，否则作为普通数据直接插入
					if(substr($line, 0, 5)=='index'){		//索引记录以index字符串开头
						//$exe_query = 'ensureIndex(o)';
						$line = substr($line, 5);
						//$exe_query = 'ensureIndex(o)';
						$ret = DBModel::model()->getDb()->selectCollection('system.indexes')->insert((Array)json_decode($line));
						$exers['i'][$ret]++;			//记录执行结果
					//判断是否是更新
					}else if(preg_match('/^\{"_id":"[0-9a-z_-]+"\},/i', $line)){
						$line = explode('},{"$set":', $line);
						if (isset($line[1])) {
							$update_where = (Array)json_decode($line[0].'}');
							if(preg_match('/^[a-z0-9]{24}$/', $update_where['_id'])){
								$update_where['_id'] = new MongoId($update_where['_id']);
							}
							$ret = DBModel::model()->getDb()->selectCollection($cname)->update($update_where, (Array)json_decode('{"$set":'.$line[1]));
							$exers['u'][$ret]++;
						}
					//其余为插入
					}else{
						//$exe_query = 'insert(o)';
						$line = (Array)json_decode(preg_replace('/\{.?"\$oid".?:.?("[0-9a-z]+")[^}]?\}/i', '\1', $line));	//转为普通字符串
						//重新构造MongoId的_id
						if(preg_match('/^[a-z0-9]{24}$/', $line['_id'])){
							$line['_id'] = new MongoId($line['_id']);
						}
						$ts = DBModel::model()->parseRealTable($cname);
						$ret = DBModel::model()->getDb()->selectCollection($ts['name'])->insert($line);
						$exers['a'][$ret]++;
					}
					
				}
			}	
		}
		
		$this->redirect_back(array('exers'=>$exers['i'][0].'#'.$exers['i'][1].'#'.$exers['a'][0].'#'.$exers['a'][1].'#'.$exers['u'][0].'#'.$exers['u'][1]));	//返回上一页
		
		/* 	参考代码
			db.getCollection("game").ensureIndex({
			  "code": NumberInt(1)
			},[]);
			db.getCollection("game").insert({
			  "_id": ObjectId("516140719b2a952c14000000"),
			  "name": "Dota2",
			});
		 */
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
	
}
