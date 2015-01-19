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
		<div class="span1" style="width:80px;">
			<?php echo $form->labelEx($model,'username'); ?>
		</div>
		<div class="span3">
			<?php echo $form->textField($model, 'username'); ?>
			<?php echo $form->error($model,'username'); ?>
		</div>
		<div class="span2">用户登陆名</div>
	</div>

	<div class="row">
		<div class="span1" style="width:80px;">
			<?php echo $form->labelEx($model,'password'); ?>
		</div>
		<div class="span3">
			<?php echo $form->textField($model,'password'); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>
		<div class="span2">登陆密码，编辑时若填写将更新原有密码</div>
	</div>
	
	<div class="row">
		<div class="span1" style="width:80px;">
			<?php echo $form->labelEx($model,'role'); ?>
		</div>
		<div class="span3">
			<?php echo $form->dropDownList($model,'role', $this->list_from_rs(Yii::app()->params['role'],'name')); ?>
			<?php echo $form->error($model,'role'); ?>
		</div>
		<div class="span2">用户角色</div>
	</div>
	
	<div class="row">
		<div class="span1" style="width:80px;">
			<?php echo $form->labelEx($model,'scope'); ?>
		</div>
		<div class="span3">
			<select style="width:120px;height:150px;" multiple="multiple" name="User[onescope][]" id="User_onescope">
				<?php echo $this->option_from_list($yxlist[1], $scopeinfo);?>
			</select>
			<select style="width:120px;height:150px;" multiple="multiple" name="User[scope][]" id="User_scope">
				<?php echo $this->option_from_list($yxlist[0], $scopeinfo);?>
			</select>
			<?php echo $form->error($model,'scope'); ?>
		</div>
		<div class="span2">数据的管理范围</div>
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
$(function(){
	$(".scopes").hide();
	$("#User_role").live('change',function(){
		 selectrole($(this).val());
	});
	$("#User_role").change();
});
function selectrole($data){
	if ( $data == 10 || $data == 20 ) {
		$(".scopes,#User_onescope").show();
		$("#User_scope").hide();
		//$("#User_onescope").find('option').removeAttr("selected");
	 }else if ( $data == 30 ) {
		$(".scopes,#User_scope").show();
		$("#User_onescope").hide();
	 }else{
		$(".scopes").hide();
	 }
}
</script>
