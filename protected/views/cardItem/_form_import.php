<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'import-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
	'htmlOptions'=>array(
		'enctype'=>'multipart/form-data',
	),
)); ?>
	<div class="row">
	<div class="span4">
	<?php
	    $this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>"数据文件",
		));
		
	?>
	<?php
	    echo('excel文件上传:'.CHtml::fileField('CardItem','',array('class'=>'btn','title'=>'execl文件上传')));
	    echo('<br/><br/>压缩文件上传：'.CHtml::fileField('CardPic','',array('class'=>'btn btn-default','title'=>'压缩文件上传【.zip】')));
	?>
	<?php $this->endWidget();?>
	</div>
	</div>
	<div class="row">
		<div class="span1">
		<?php echo CHtml::submitButton('导入'); ?>
		</div>
	</div>
<?php $this->endWidget();?>
