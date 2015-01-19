<?php
$this->breadcrumbs = array(
	'用户管理',
);

$this->leftTree = $dataTree;
?>

<?php
$this->widget('application.extensions.FancyBox.EFancyBox', array());
?>
<input type="button" onclick="addInfo()" value="新建用户">

<script type="text/javascript">
	function addInfo() {
		var id = arguments[0] ? arguments[0] : 0;
		if (id!=0) {
			var ajaxHref = '/user/update/id/'+id;
		} else {
			var ajaxHref = '/user/create';
		}
		$.fancybox({
			'hideOnOverlayClick'	: false,
			'enableEscapeButton'	: false,
			'autoScale'		: false,
			'autoDimensions'	: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'width'			: 550,
			'height'		: 370,
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


<?php 
$labels = $model->attributeLabels();
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'card-grid',
	/*'type'=>'striped bordered condensed',*/
	'itemsCssClass'=>'table table-hover table-striped table-bordered table-condensed',
	'summaryText' => '显示{start}-{end}, 共{count}条记录',
	'enableHistory' => true,
	'dataProvider'=>$model->search(),
	'columns'=>array(
		array('name'=>'id', 'header'=>$labels['id']),
		array(
			'name'=>'username',
			'header'=>$labels['username'],
		),
		array(
			'name'=>'role', 'header'=>$labels['role'],
			'value' => 'Yii::app()->params["role"][$data->role]["name"]',
		),
		array('name'=>'last_uid', 'header'=>$labels['last_uid'], 'type'=>'raw'),
		array(
			'name' => 'update_time',
			'header' => $labels['update_time'],
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
			'template' => '{modify} - {delete}',
			'buttons' => array(
				'modify' => array(
					'label' => '修改',
					'type' => 'raw',
					'click' => 'function () {var id=$(this).parent().siblings(":first").text();addInfo(id);}',
				),
				'delete' => array(
					'label' => '删除',
					'url' => 'Yii::app()->createUrl("user/delete", array("id"=>$data->id))',
				),
			),
		),
	),
)); ?>
