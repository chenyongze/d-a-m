<?php
$this->breadcrumbs = array(
	'游戏数据库' => array('index'),
	'数据库管理',
);

$this->leftTree = $dataTree;
?>

<?php
$this->widget('application.extensions.FancyBox.EFancyBox', array(
	//'target'=>'.mfFancybox',
	/*
	'config'=>array(
		'autoScale' => false,
		'type' => 'ajax',
		'width' => 12000,
		'height' => 400,
		'async' => 'false',
		//'autoDimensions' => true,
	),*/
	)
);
/*
echo CHtml::ajaxButton('新建数据库', array('//CardDb/create'), 
		array(
			'data' => array(),
			//'update'=>'#fancybox-content',
			'update'=>'#mfFancybox',
			'async' => 'false',
		), 
		array()
		//array('class' => 'mfFancybox')
		);
*/
?>
<input type="button" onclick="DbInfo()" value="新建数据库" id="CreateDb">

<script type="text/javascript">
	function DbInfo() {
		var dbId = arguments[0] ? arguments[0] : 0;
		if (dbId!=0) {
			var ajaxHref = '/cardDb/Update/id/'+dbId;
		} else {
			var ajaxHref = '/cardDb/create';
		}
		$.fancybox({
			'hideOnOverlayClick'	: false,
			'enableEscapeButton'	: false,
			'autoScale'		: false,
			'autoDimensions'	: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'width'			: 650,
			'height'		: 170,
			'href'			: ajaxHref,	//请求地址
			'type'			: 'inline',
		});
		$("#fancybox-close").unbind("click");
		$("#fancybox-close").bind("click", function () {
            $.fancybox.close();
		});
		return false;
	}
</script>

<!-- 提示信息 -->
<div class="row">
<div class="span6 mfInfoBox">
<?php
	if (!empty($info)):
?>
	<?php
		if ($info['type'] == 'success'):
	?>
	<div class="alert alert-success">
	  <button type="button" class="close" data-dismiss="alert">×</button>
	  <?php echo $info['msg']; ?>
	</div>
	<?php
		else:
	?>
	<div class="alert alert-error">
	  <button type="button" class="close" data-dismiss="alert">×</button>
	  <?php echo $info['msg']; ?>
	</div>
	<?php
		endif;
	?>
<?php
	endif;
?>
</div>
</div>


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'card-grid',
	/*'type'=>'striped bordered condensed',*/
	'itemsCssClass'=>'table table-hover table-striped table-bordered table-condensed',
	'summaryText' => '显示{start}-{end}, 共{count}条记录',
	'enableHistory' => true,
	'dataProvider'=>$model->search(),
	'columns'=>array(
		array('name'=>'id', 'header'=>'id'),
		array(
			'name'=>'name',
			'header'=>'游戏名称',
			'type'=>'raw',
			//'value' => '<span class="ui-button ui-widget ui-state-default ui-corner-all">CHtml::link("$data->name", array("CardDs/Index", "id" => $data->id))</span>',
			'value' => 'CHtml::link("$data->name", array("CardDs/Index", "id" => $data->id))',
		),
		array('name'=>'en_name', 'header'=>'英文标识'),
		array('name'=>'request_times', 'header'=>'请求次数', 'type'=>'raw'),
		array('name'=>'last_uid', 'header'=>'最后修改', 'type'=>'raw'),
		array(
			'name' => 'update_time',
			'header' => '最后更新',
			'value' => 'date("Y-m-d H:i:s", $data->update_time)',
		),

		array(
			'class' => 'CButtonColumn',
			'header' => '操作',
			'afterDelete' => "function(link,success,data) {
						var infoHtml = '';
						data = $.parseJSON(data);
						if(data.type == 'success') {
							infoHtml = '<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>成功!</strong> '+data.msg+'</div>';
						} else {
							infoHtml = '<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>失败!</strong> '+data.msg+'</div>';
						}
						$('.mfInfoBox').html(infoHtml);
					}",
			'deleteConfirmation' => '确定删除这条记录吗？',
			//'template' => '<span class="badge badge-info" style="font-color:white;">{modify}</span> - {delete}',
			'template' => '{modify} - {delete}',
			'buttons' => array(
				'modify' => array(
					'label' => '修改',
					'type' => 'raw',
					'click' => 'function () {var dbId=$(this).parent().siblings(":first").text();DbInfo(dbId);}',
					//'url' => 'Yii::app()->createUrl("CardDb/Update", array("id"=>$data->id), array("class"=>"delDb", "title"=>"$data->name"))',
					//'url' => 'Yii::app()->createUrl("CardDb/Update", array("id"=>$data->id))',
					//'value' => 'CHtml::ajaxButton("发布新卡牌库", array("//CardDb/Update"), array("data" => array("id"=>$data->id),"update"=>"#fancybox-content",), array("class" => "mfFancybox"))',
				),
				'delete' => array(
					'label' => '删除',
					'url' => 'Yii::app()->createUrl("CardDb/Delete", array("id"=>$data->id))',
				),
			),
		),
	),
)); ?>
