<?php
class UeditorWidget extends CWidget{


    private $_assetUrl;

    public $jsFiles=array(
        '/ueditor.config.js',
        '/ueditor.all.min.js',
    );
    public $cssFiles=array(
        '/themes/default/css/ueditor.css'
    );

    //容器的ID 具有唯一性
    public $id;
    //后台接收name名称
    public $name;
    //初始化内容
    public $content='';
    //容器宽
    public $width='100%';
    //容器高
    public $height='400px';
    /**
     * 配置选项
     * 将ueditor.config.js的选项以数组键值的方式配置
     * @var array
     */
    public $config=array();
    //后台统一url
    public $serverUrl;

    function init(){

        parent::init();

        if(trim($this->id)==''||trim($this->name)==''){
            throw new CException('必须设置容器id和name值');
        }

        //发布资源

        $this->_assetUrl=Yii::app()->assetManager->publish(Yii::getPathOfAlias('ext.baiduUeditor.resource'));

        $clientScript=Yii::app()->clientScript;
        //注册常量
        $jsConstant='window.UEDITOR_HOME_URL = "'.$this->_assetUrl.'/"';
        $clientScript->registerScript('ueditor_constant',$jsConstant,CClientScript::POS_BEGIN);

        //注册js文件

        foreach($this->jsFiles as $jsFile){
            $clientScript->registerScriptFile($this->_assetUrl.$jsFile,CClientScript::POS_END);

        }
        //注册css文件
        foreach($this->cssFiles as $cssFile){
            $clientScript->registerCssFile($this->_assetUrl.$cssFile);
        }
        //判断是否存在module
        if($this->owner->module!=null){
            $moduleId=$this->owner->module->id;
            $this->serverUrl=Yii::app()->createUrl($moduleId.'/ueditor');
        }else{
            $this->serverUrl=Yii::app()->createUrl('ueditor');
        }
        //config
        $this->config['serverUrl']=$this->serverUrl;

        $this->render('ueditor');


    }


}