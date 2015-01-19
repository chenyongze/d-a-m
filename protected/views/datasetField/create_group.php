<?php 
$enName = isset($enName) ? $enName : '';
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'database-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); 

if (isset($update) && $update == true) {
	$param = 'readonly="readonly"';
} else {
	$param = '';
}
?>


	<div class="row">
		<div class="span2 offset1">
		<?php 
			if (isset($errorMsg)) {
				echo "<h5><font color='red'>*".$errorMsg."</font></h5>";
			}
		?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->hiddenField($model,'id'); ?>
		<?php echo $form->hiddenField($model,'database_id'); ?>
		<input name="fields[type]" value="group" type="hidden">
		<input name="fields[listorder]" value="<?php echo (isset($enName, $dsModel->fields[$enName]['listorder']) ? intval($dsModel->fields[$enName]['listorder']) : 0); ?>" type="hidden">
		<?php
			if (isset($enName)):
		?>
				<input name="fields[old_en_name]" value="<?php echo $enName;?>" type="hidden">
		<?php
			endif;
		?>
		<?php
			if (isset($model->fields[$enName]['name'])):
		?>
				<input name="fields[old_name]" value="<?php echo $model->fields[$enName]['name'];?>" type="hidden">
		<?php
			endif;
		?>
	</div>

	<div class="row">
		<div class="span1"><label>名称</label></div>
		<div class="span2">
			<input type="text" name="fields[name]" value="<?php if (isset($enName, $dsModel->fields[$enName]['name'])) echo $dsModel->fields[$enName]['name']; ?>">
		</div>
	</div>
	<div class="row">
		<div class="span1"><label>英文标识</label></div>
		<div class="span2">
			<input type="text" <?php echo $param;?>  name="fields[en_name]" value="<?php if (isset($enName)) echo $enName; ?>">
		</div>
	</div>

	<div class="row">
		<div class="span1">
			<?php echo CHtml::submitButton('完成'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>
