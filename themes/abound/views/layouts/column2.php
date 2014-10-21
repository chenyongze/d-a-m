<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>

  <div class="row-fluid">
	<div class="span3">
		<div class="sidebar-nav">
			<?php $this->widget('system.web.widgets.CTreeView',array(
				'animated' => 'normal',
				'collapsed' => true,
				'data'=>$this->leftTree,
				));
			?>	
		</div>
        	<br>
    	</div><!--/span-->
    <div class="span9">
    
    <?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
            		'links'=>$this->breadcrumbs,
			'htmlOptions'=>array('class'=>'breadcrumb')
        )); ?><!-- breadcrumbs -->
    <?php endif?>
    
    <!-- Include content pages -->
    <?php
    	if (Yii::app()->user->name=='publisher' && $this->getId()!='cardItem') {
	
	} else {
    		echo $content;
	}
    ?>

	</div><!--/span-->
  </div><!--/row-->


<?php $this->endContent(); ?>
