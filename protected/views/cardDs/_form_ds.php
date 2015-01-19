<?php 
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'dataset-form',
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
		<div class="span3" style="font-size:15px;">
			<?php if (!isset($model->name)): ?>新建<?php else: ?>修改<?php endif; ?>
			<?php echo $dbModel->name;?> 数据表.
		</div>
	</div>
	<div class="row">&nbsp;</div>
	<div class="row">
		<div class="span3">
		<?php echo $form->errorSummary($model); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->hiddenField($model,'database_id'); ?>
		<input type="hidden" id="rowNums" value="1">
	</div>

	<div class="row">
		<div class="span1" style="width:80px;">
			<label>数据库表名:</label>
		</div>
		<div class="span2">
			<?php echo $form->textField($model, 'name', array('placeholder'=>'数据表中文名')); ?>
			<?php echo $form->error($model,'name'); ?>
		</div>
		<div class="span1" style="width:70px;margin-left:70px;">
			<label>英文标识:</label>
		</div>
		<div class="span2">
			<?php echo $form->textField($model, 'en_name', array_merge(array('placeholder'=>'数据表英文标识'), $param)); ?>
			<?php echo $form->error($model,'en_name'); ?>
		</div>

		<?php if (!isset($model->name)): ?>
		<div class="span1" style="margin-left:60px;width:30px;">
			<span style="font-size:30px;"><a href="javascript:void(0)" onclick="addRow();this.blur();">+</a></span>
		</div>
		<?php endif; ?>
	</div>

	<div id="submit" class="row buttons">
		<div class="span1 offset3" style="padding-top:5px;"><?php echo CHtml::submitButton('     完成     '); ?></div>
	</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
	//增加输入行
	function addRow() {
		var rowNums = parseInt($("#rowNums").val());
		var rowHtml = '';
		$.ajax({
		url: "/CardDs/addRow/",
		async : true,
		data : "id=" + rowNums,
		type:"GET",
		success: function(data){
		    rowHtml = $.parseJSON(data);
		    $("#submit").before(rowHtml);
		    rowNums += 1;
		    $("#rowNums").val(rowNums);
		}
		});
	}

	$(this).keydown( function(e) {
		var key = window.event?e.keyCode:e.which;
		//alert(key.toString());
		if(key.toString() == "13"){
			return false;
		}
	});
</script>
