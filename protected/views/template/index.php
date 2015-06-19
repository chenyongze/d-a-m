<?php

$this->breadcrumbs = array(
	$dbModel->name => array('CardDs/index/id/'.$databaseId),
    "[{$dsModel->name}]模板管理",
    
);

$this->widget('application.extensions.FancyBox.EFancyBox', array());

?>
<input type="button" onclick="TpInfo()" value="新建模板" id="CreateDs">

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

	function TpInfo() {
		var tpId = arguments[0] ? arguments[0] : 0;
		if (tpId!=0) {
			var Href = '/Template/Update/id/'+dsId;
		} else {
			var Href = '/Template/create/id/<?php echo $databaseId;?>';
		}
		window.location = Href;
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
// 		array('name'=>'id', 'header'=>'#'),
		array(    
			'name'=>'id',
			'type'=>'raw',
			'value' => $data->id,
		),
		array(
			'name' => 'acttime',
			'header' => '最后更新',
			'value' => 'date("Y-m-d H:i:s", $data->acttime)',
		),
	    array(
	        'name' => 'tpname',
	        'header' => '模板名称',
	        'value' => '$data->tpname',
	    ),
	    array(
	        'name' => 'type',
	        'header' => '类型',
	        'value' => '$data->type',
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
			'template' => '{modify} - {delete}',
			'buttons' => array(
				'modify' => array(
					'label' => '修改',
					'url' => 'Yii::app()->createUrl("Template/update", array("id"=>$data->id))',
				),
				'del' => array(
					'label' => '删除',
					'url' => 'Yii::app()->createUrl("Template/Delete", array("id"=>$data->id))',
				),
			),
		),
	),
));
?>
