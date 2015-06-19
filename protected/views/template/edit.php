<?php 
//$operation
$operation = isset($update)&&($update === true)?'修改':'添加';
$this->breadcrumbs = array(
    $dbModel->name => array('CardDb/index'),
    $dsModel->name => array('CardDs/index/'.$dsModel->id),
    "[{$dsModel->name}]模板管理",
    $operation,
);

$form=$this->beginWidget('ActiveForm', array(
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
		<?php echo $form->hiddenField($model,'dataset_id',array('value'=>$datasetId)); ?>
	</div>
	<div class="row">
		<div class="span3">
		<p class="note">标注 <span class="required">*</span>为必填项目.</p>
		<p><?php echo $_txtfiled;?></p>
		</div>
	</div>
	<div class="row">
		<div class="span3">
		<?php echo $form->errorSummary($model); ?>
		</div>
	</div>

	<div class="row">
		<div class="span1" style="width:80px;"><?php echo $form->labelEx($model,'tpname'); ?></div>
		<div class="span3">
			<?php echo $form->textField($model, 'tpname'); ?>
			<?php echo $form->error($model,'tpname'); ?>
		</div>
		
		<div class="span2">使用说明:新建模板名称</div>
	</div>

	<div class="row">
		<div class="span1" style="width:80px;">
			<?php echo $form->labelEx($model,'type'); ?>
		</div>
		<div class="span3">
		    <?php echo $form->radioButtonList($model,'type', array('1'=>'pc','2'=>'wap'),array('separator'=>'','readonly'=>$param));?>
			<?php //echo $form->textField($model,'type', $param); ?>
			<?php echo $form->error($model,'type'); ?>
		</div>
		<div class="span2">使用说明:新建模板类型 pc,wap,...</div>
	</div>
	<div class="row">
		<div class="span1" style="width:80px;">
		</div>
		<div class="span3">
			<?php echo $form->ueditor($model,'content',array('style'=>'width:850px;height:400px;'));//style是限定文本框的大小?> 
			<?php echo $form->error($model,'content'); ?>
		</div>
	</div>	
                    
    <div>
    </div>
	<div class="row buttons">
		<div class="span1">
		<?php echo CHtml::submitButton('完成'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>
