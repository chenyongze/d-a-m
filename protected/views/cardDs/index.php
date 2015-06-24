<?php

$this->breadcrumbs = array(
	'结构定义' => array('CardDb/index'),
	$dbModel->name => array('CardDs/index/id/'.$databaseId),
);

$this->leftTree = $dataTree;

$this->widget('application.extensions.FancyBox.EFancyBox', array());

?>
<input type="button" onclick="DsInfo()" value="新建数据表" id="CreateDs">

<script type="text/javascript">

	function clearData(dsId) {
		var dsId = arguments[0] ? arguments[0] : 0;
		if (confirm("是否清空数据？ 【谨慎操作】")) {
			$.ajax({
			    type: "post",
			    url: '/CardDs/ClearData/id/'+dsId,
			    async : true,
			    success: function(data){
				var infoHtml = '';
				data = $.parseJSON(data);
				if(data.type == 'success') {
					infoHtml = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button> '+data.msg+'</div>';
				} else {
					infoHtml = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>< '+data.msg+'</div>';
				}
				$('.mfInfoBox').html(infoHtml);
			    }
			})
		}
		return false;
	}

	function DsInfo() {
		var dsId = arguments[0] ? arguments[0] : 0;
		if (dsId!=0) {
			var ajaxHref = '/cardDs/Update/id/'+dsId;
			var dsWidth = 700;
		} else {
			var ajaxHref = '/cardDs/create/id/<?php echo $databaseId;?>';
			var dsWidth = 730;
		}
		$.fancybox({
			'hideOnOverlayClick'	: false,
			'enableEscapeButton'	: false,
			'autoScale'		: false,
			'autoDimensions'	: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'width'			: dsWidth,
			'height'		: 400,
			'href'			: ajaxHref,
			'type'			: 'inline',
		});
		$("#fancybox-close").unbind("click");
		$("#fancybox-close").bind("click", function (){
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
$this->widget('zii.widgets.grid.CGridView', array(
    	'id' => 'card-grid',
	'itemsCssClass'=>'table table-hover table-striped table-bordered table-condensed',
	'dataProvider' => $model->search(),
	'columns' => array(
		array('name'=>'id', 'header'=>'#'),
		array(
			'name'=>'name',
			'type'=>'raw',
			'value' => 'CHtml::link("$data->name", array("DatasetField/Index", "id" => $data->id))',
		),
		array('name'=>'en_name', 'header'=>'英文标识'),
		array('name'=>'request_times', 'header'=>'请求次数'),
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
			//'template' => '{fields} - <span onclick="DsInfo()">{modify}</span> - {delete}',
			'template' => '{fields} - {templates}-{modify} - {clear} - {delete}',
			'buttons' => array(
				'fields' => array(
					'label' => '字段管理',
					'url' => 'Yii::app()->createUrl("DatasetField/Index", array("id"=>$data->id))',
					),
			    'templates' => array(
			            'label' => '模板管理',
			            'url' => 'Yii::app()->createUrl("Template/Index", array("id"=>$data->id))',
			        ),
				'modify' => array(
					'label' => '修改',
					//'url' => 'Yii::app()->createUrl("CardDs/Update", array("id"=>$data->id))',
					//'url' => '',
					//'options' => array('datasetId'=>$data->id),
					'click' => 'function () {var dsId=$(this).parent().siblings(":first").text();DsInfo(dsId);}',
				),
				'clear' => array(
					'label' => '清空数据',
					//'url' => 'Yii::app()->createUrl("CardDs/Update", array("id"=>$data->id))',
					//'url' => '',
					//'options' => array('datasetId'=>$data->id),
					'click' => 'function () {var dsId=$(this).parent().siblings(":first").text();clearData(dsId);}',
				),
				'del' => array(
					'label' => '删除',
					'url' => 'Yii::app()->createUrl("CardDs/Delete", array("id"=>$data->id))',
				),
			),
		),
	),
));
?>
