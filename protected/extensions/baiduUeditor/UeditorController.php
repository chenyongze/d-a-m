<?php

/**
 * Class UeditorController
 * 因为没有用到视图，可以随意继承自己写的基类，
 * 如果基类里已经做了权限检查，可以把init方法里的注释掉了
 */
class UeditorController extends CExtController{

    private $_config;

    private $_base64='upload';

    function init(){
        parent::init();

        //为flash上传设置session
        $sessionName=session_name();

        if(isset($_GET[$sessionName])&&strlen($_GET[$sessionName])>0){
            $_COOKIE[$sessionName] = $_GET[$sessionName];
        }
        //TODO:更详细的权限检查需要自己做

        if(Yii::app()->user->isGuest){
            echo json_encode(array('state'=>'没有权限'));
            Yii::app()->end();
        }


        Yii::import('ext.baiduUeditor.*');

        $this->_config=require_once('config.php');

        require_once('Uploader.class.php');

    }

    function actionIndex($action=''){
        $action=htmlspecialchars($action);
        switch ($action) {
            case 'config':
                $result =  json_encode($this->_config);
                break;

            // 上传图片
            case 'uploadimage':
                // 上传涂鸦
            case 'uploadscrawl':
                // 上传视频
            case 'uploadvideo':
                // 上传文件
            case 'uploadfile':
                $result=$this->_upload($action);
                break;

            // 列出图片
            case 'listimage':
                $result=$this->_list($action);
                break;
            // 列出文件
            case 'listfile':
                $result=$this->_list($action);
                break;

            // 抓取远程文件
            case 'catchimage':
                $result=$this->_crawler();
                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        // 输出结果
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }
    //上传附件和上传视频
    private function _upload($action){
        $this->_base64 = "upload";
        switch ($action) {
            case 'uploadimage':
                $config = array(
                    "pathFormat" => $this->_config['imagePathFormat'],
                    "maxSize" => $this->_config['imageMaxSize'],
                    "allowFiles" => $this->_config['imageAllowFiles']
                );
                $fieldName = $this->_config['imageFieldName'];
                break;
            case 'uploadscrawl':
                $config = array(
                    "pathFormat" => $this->_config['scrawlPathFormat'],
                    "maxSize" => $this->_config['scrawlMaxSize'],
                    //"allowFiles" => $this->_config['scrawlAllowFiles'], 这个不需要
                    "oriName" => "scrawl.png"
                );
                $fieldName = $this->_config['scrawlFieldName'];
                $this->_base64 = "base64";
                break;
            case 'uploadvideo':
                $config = array(
                    "pathFormat" => $this->_config['videoPathFormat'],
                    "maxSize" => $this->_config['videoMaxSize'],
                    "allowFiles" => $this->_config['videoAllowFiles']
                );
                $fieldName = $this->_config['videoFieldName'];
                break;
            case 'uploadfile':
            default:
            $config = array(
                    "pathFormat" => $this->_config['filePathFormat'],
                    "maxSize" => $this->_config['fileMaxSize'],
                    "allowFiles" => $this->_config['fileAllowFiles']
                );
                $fieldName = $this->_config['fileFieldName'];
                break;
        }

        /* 生成上传实例对象并完成上传 */
        $up = new Uploader($fieldName, $config, $this->_base64);

        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */

        /* 返回数据 */
        return json_encode($up->getFileInfo());
    }
    //获取已上传的文件列表
    private function _list($action){
        switch ($action) {
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $this->_config['fileManagerAllowFiles'];
                $listSize = $this->_config['fileManagerListSize'];
                $path = $this->_config['fileManagerListPath'];
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $this->_config['imageManagerAllowFiles'];
                $listSize = $this->_config['imageManagerListSize'];
                $path = $this->_config['imageManagerListPath'];
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;

        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
        $files = $this->_getfiles($path, $allowFiles);
        if (!count($files)) {
            return json_encode(array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            ));
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}

        /* 返回数据 */
        $result = json_encode(array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ));

        return $result;
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private function _getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->_getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }
    //抓取远程图片
    private function _crawler(){
        set_time_limit(0);

        /* 上传配置 */
       $config = array(
            "pathFormat" => $this->_config['catcherPathFormat'],
            "maxSize" => $this->_config['catcherMaxSize'],
            "allowFiles" => $this->_config['catcherAllowFiles'],
            "oriName" => "remote.png"
        );
        $fieldName = $this->_config['catcherFieldName'];

        /* 抓取远程图片 */
        $list = array();
        if (isset($_POST[$fieldName])) {
            $source = $_POST[$fieldName];
        } else {
            $source = $_GET[$fieldName];
        }
        foreach ($source as $imgUrl) {
            $item = new Uploader($imgUrl, $config, "remote");
            $info = $item->getFileInfo();
            array_push($list, array(
                "state" => $info["state"],
                "url" => $info["url"],
                "size" => $info["size"],
                "title" => htmlspecialchars($info["title"]),
                "original" => htmlspecialchars($info["original"]),
                "source" => htmlspecialchars($imgUrl)
            ));
        }

        /* 返回抓取数据 */
        return json_encode(array(
            'state'=> count($list) ? 'SUCCESS':'ERROR',
            'list'=> $list
        ));
    }

}