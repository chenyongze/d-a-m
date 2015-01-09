<script type="text/javascript">
	function getStrLen(str){
		var len = 0;
		var cnstrCount = 0; 
		for(var i=0 ; i<str.length ; i++){
			  if(str.charCodeAt(i)>255)
				     cnstrCount = cnstrCount + 1 ;
		}
		len = str.length + cnstrCount;
		return len;
	}
	permitSubmit = 1;
</script>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'item-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<div class="row">
		<?php echo $form->errorSummary($model); ?>
		<?php echo $form->hiddenField($model,'id'); ?>
	</div>

	<?php
		echo $fieldHtml;
	?>

	<?php
		if ($preview == false):
	?>
	<div class="row buttons">
		<div class="span1">
		<?php echo CHtml::submitButton('发布新数据'); ?>
		</div>
	</div>
	<?php
		endif;
	?>

<?php $this->endWidget(); ?>

<script type="text/javascript">
	$("#item-form").submit(function(e){
		if (permitSubmit==0) {
			e.preventDefault();
		}
	})
</script>
