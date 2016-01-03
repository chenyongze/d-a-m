<?php 
class ActiveForm extends CActiveForm {  
  
    public function ueditor($model, $attribute, $htmlOptions=array()) {  
        //得到这个插件的name  
        CHtml::resolveNameID($model, $attribute, $htmlOptions);  
        $attr = 'name="'.$htmlOptions['name'].'" style="'.$htmlOptions['style'].'"';  
        $content = $model->content?$model->content:'';  
        $str = '<script type="text/javascript" src="/themes/abound/js/ueditor/ueditor.config.js"></script>';  
        $str .='<script type="text/javascript" src="/themes/abound/js/ueditor/ueditor.all.min.js"></script>'; 
        $str .='<script type="text/javascript" src="/themes/abound/js/ueditor/lang/zh-cn/zh-cn.js"></script>';
        $str .= '<script id="editor" type="text/plain" '.$attr.'>'.html_entity_decode($content).'</script>';      
        $str .= '<script type="text/javascript">var ue = UE.getEditor(\'editor\');</script>';  
        return $str;  
    }  
}  