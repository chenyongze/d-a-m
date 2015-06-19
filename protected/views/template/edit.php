<?php 
$this->breadcrumbs = array(
    $dbModel->name => array('CardDb/index'),
    $dbModel->name,
);

$form=$this->beginWidget('ActiveForm', array(
    'id'=>'database-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
//    'action'=>Yii::app()->createUrl('/Template/create/id/'),   //这里我把action重新指向site控制器的login动作
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
		    <?php echo $form->radioButtonList($model,'type', array('1'=>'pc','2'=>'wap'),array('separator'=>''));?>
			<?php //echo $form->textField($model,'type', $param); ?>
			<?php echo $form->error($model,'type'); ?>
		</div>
		<div class="span2">使用说明:新建模板类型 pc,wap,...</div>
	</div>
	
	<?php echo $form->ueditor($model,'content',array('style'=>'width:850px;height:400px;'));//style是限定文本框的大小?> 
   <?php echo $form->error($model,'tpdata'); ?>
                    
    <div>
    </div>
	<div class="row buttons">
		<div class="span1">
		<?php echo CHtml::submitButton('完成'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>
