<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'database-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); 

if (isset($update) && $update == true) {
	$param = array('readonly'=>'readonly');
} else {
	$param = array();
}
?>

	<div class="row">
		<div class="span3">
		<p class="note">标注 <span class="required">*</span>为必填项目.</p>
		</div>
	</div>

	<div class="row">
		<div class="span3">
		<?php echo $form->errorSummary($model); ?>
		</div>
	</div>

	<div class="row">
		<div class="span1" style="width:80px;"><?php echo $form->labelEx($model,'name'); ?></div>
		<div class="span3">
			<?php echo $form->textField($model, 'name'); ?>
			<?php echo $form->error($model,'name'); ?>
		</div>
		<div class="span2">使用说明:新建游戏名称</div>
	</div>

	<div class="row">
		<div class="span1" style="width:80px;">
			<?php echo $form->labelEx($model,'en_name'); ?>
		</div>
		<div class="span3">
			<?php echo $form->textField($model,'en_name', $param); ?>
			<?php echo $form->error($model,'en_name'); ?>
		</div>
		<div class="span2">使用说明:新建游戏英文标识,尽量用游戏英文简称</div>
	</div>

	<div class="row buttons">
		<div class="span1">
		<?php echo CHtml::submitButton('完成'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
//$("#fancybox-close").click(function (){
//	if (confirm("是否保存数据？")) {
//		$("#database-form").submit();
//	} else {
		//alert('未保存数据');
//		parent.$.fancybox.close();
//	}
//})
//parent.$.fancybox.close(function () {alert('test');});

$(this).keydown( function(e) {
	var key = window.event?e.keyCode:e.which;
	//alert(key.toString());
	if(key.toString() == "13"){
		return false;
	}
});
</script>
