<?php
$this->breadcrumbs = array(
	'结构定义' => array('CardDb/index'),
	$dbModel->name => array('CardDs/index/id/'.$dbModel->id),
	$dsModel->name,
);

$this->leftTree = $dataTree;
?>

<?php $this->widget('application.extensions.FancyBox.EFancyBox', array()); ?>
<div class="span1">
	<input type="button" onClick="FieldInfo()" value="新建字段" id="CreateField">
</div>
<div class="span1">
	<input type="button" onClick="GroupInfo()" value="新建字段组" id="CreateGroup">
</div>
<div class="span1">
	<input type="button" value="预览" id="Preview">
</div>

<script type="text/javascript">
	function FieldInfo() {
		var dsId = arguments[0] ? arguments[0] : 0;
		if (dsId!=0) {
			var enName = arguments[1] ? arguments[1] : '';
			var type = arguments[2] ? arguments[2] : '';
			var group = arguments[3] ? arguments[3] : '';
			if (group) {
				var ajaxHref='/DatasetField/update/id/'+dsId+'/enName/'+enName+'/type/'+type+'/group/'+group;
			} else {
				var ajaxHref='/DatasetField/update/id/'+dsId+'/enName/'+enName+'/type/'+type;
			}
		} else {
			var group = arguments[1] ? arguments[1] : '';
			if (group) {
				var ajaxHref='/DatasetField/create/id/<?php echo $datasetId;?>/group/'+group;
			} else {
				var ajaxHref='/DatasetField/create/id/<?php echo $datasetId;?>';
			}
		}
		$.fancybox({
			'hideOnOverlayClick'	: false,
			'enableEscapeButton'	: false,
			'autoScale'		: false,
			'autoDimensions'	: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'width'			: 500,
			'height'		: 800,
			'href'			: ajaxHref,
			'type'			: 'inline',
			'centerOnScroll':true,
		});
		$("#fancybox-close").unbind("click");
		$("#fancybox-close").bind("click", function () {
				$.fancybox.close();
		});
		return false;
	}

	function GroupInfo() {
		var dsId = arguments[0] ? arguments[0] : 0;
		if (dsId!=0) {
			var enName = arguments[1] ? arguments[1] : '';
			var type = arguments[2] ? arguments[2] : 'group';
			var ajaxHref='/DatasetField/update/id/'+dsId+'/enName/'+enName+'/type/'+type;
		} else {
			var ajaxHref='/DatasetField/createGroup/id/<?php echo $datasetId;?>';
		}
		$.fancybox({
			'hideOnOverlayClick'	: false,
			'enableEscapeButton'	: false,
			'autoScale'		: false,
			'autoDimensions'	: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'width'			: 500,
			'height'		: 200,
			'href'			: ajaxHref,
			'type'			: 'inline',
		});
		$("#fancybox-close").unbind("click");
		$("#fancybox-close").bind("click", function (){
				$.fancybox.close();
		});
		return false;
	}

	$("#Preview").click(function() {
		$.fancybox({
			'autoScale'		: false,
			'autoDimensions'	: false,
            'hideOnOverlayClick'	: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'width'			: 900,
			'height'		: 400,
			'href'			: '/CardItem/create/id/<?php echo $datasetId;?>/preview/true',
			'type'			: 'inline',
		});
		$("#fancybox-close").unbind("click");
		$("#fancybox-close").bind("click", function (){
			$.fancybox.close();
		});
		return false;
	})
</script>

	<?php
	/**
	 * 新建字段组(暂未启用)
	 *
	 */
		if (false):
	?>
	<div class="span1">
	<?php
	$this->widget('application.extensions.FancyBox.EFancyBox', array(
		'target'=>'#importItems',
		'config'=>array(
		),
		)
	);
	echo CHtml::ajaxButton('新建字段组', array('//CardItem/import'), 
			array(
				'data' => array('id' => $datasetId),
				'update'=>'#fancybox-content',
			), 
			array('id' => 'importItems'));
	?>
	&nbsp;&nbsp;
	<a href="/DatasetField/CreateGroup/id/<?=$datasetId;?>">新建字段组</a>
	<a href="DatasetField/CreateGroup/id/<?=$datasetId;?>">新建字段组</a>
	</div>
	<?php
		endif;
	?>


	<div class="row"></div>
	<div class="row">&nbsp;&nbsp;</div>

<!-- 提示信息 -->
<div class="row">
<div class="span6">
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

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'contact-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<div id="card-grid" class="grid-view">
		<table class="table table-hover table-striped table-bordered table-condensed">
			<thead>
				<tr>
					<th id="card-grid_c1">排序</th>
					<th id="card-grid_c1">字段名称</th>
					<th id="card-grid_c2">英文标识</th>
					<th id="card-grid_c3">字段类型</th>
					<th id="card-grid_c4">必填项</th>
					<th id="card-grid_c5">筛选字段</th>
					<th class="button-column" id="card-grid_c5">操作</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				foreach ($dsModel->fields as $key=>$value):
			?>
			<tr class="odd">
				<td>
					<input type="text" style="width:50px;" name="listorder[<?php echo $key;?>]" value="<?php if (isset($value['listorder']) && $value['listorder']!=0) { echo $value['listorder'];} else {echo 0;}?>" >
				</td>
				<td>
					<?php 
						if ($value['type'] == 'group') {
							$url = Yii::app()->createUrl("DatasetField/index", array("id"=>$dsModel->id, "enName"=>$key, "type"=>"group"));
							echo '<a href="'.$url.'">'.$value['name'].'</a>';
						} else {
							echo $value['name'];
						}
					?>
				</td>
				<td><?php echo $key;?></td>
				<td>
					<?php
						if ($value['type']=='field') {
							//$fieldType = '基本字段';
							$fieldType = '';
							if ($value['extra']['field_info']['field_type'] == 'reference') {
								$fieldType .= '关联字段';
							} elseif ($value['extra']['field_info']['field_type']=='normal') {
								switch ($value['extra']['field_info']['addition_type']) {
									case 'text':
										$fieldType .= '单行文本';
										break;
									case 'multitext':
										$fieldType .= '多行文本';
										break;
									case 'number':
										$fieldType .= '数值';
										break;
									case 'select':
										$fieldType .= '单选';
										break;
									case 'multiselect':
										$fieldType .= '多选';
										break;
									case 'image':
										$fieldType .= '图片';
										break;
								}
							}
						} elseif ($value['type']=='group') {
							$fieldType = '组字段';
						}
						echo $fieldType;
					?>
				</td>
				<td>
					<?php 
						if (isset($value['must'])) {
							if ($value['must']==1) {
								echo '是';
							} else {
								echo '否';
							}
						}
					?>
				</td>
				<td>
					<?php
						if (isset($value['extra']['filter']['type'])) {
							switch ($value['extra']['filter']['type']) {
								case '0':
									$filterType = '非';
									break;
								case '1':
									$filterType = '是(主要)';
									break;
								case '2':
									$filterType = '是(高级)';
									break;
							}
						} else {
							$filterType = '';
						}
						echo $filterType;
					?>
				</td>
				<td class="button-column">
					<a title="修改" onclick="FieldInfo(<?php echo $dsModel->id;?>, '<?php echo $key;?>', '<?php echo $value['type'];?>')" href="#">修改</a>
					- 
					<a class="delete" title="删除" href="javascript:confirmurl('<?php echo Yii::app()->createUrl("DatasetField/Delete", array("id"=>$dsModel->id, "enName"=>$key, "type"=>$value['type']));?>', '确认要删除『 <?php echo $value['name'];?> 』吗？');">删除</a>
					<?php if ($value['type']=='group'): ?>	
					- 
					<a title="新建子字段" onclick="FieldInfo(0, '<?php echo $key; ?>')" href="#">新建子字段</a>
					<?php endif;?>
				</td>
			</tr>
			<?php if ($value['type'] == 'group' && !empty($value['fields'])) :?>
			<?php
				$group = $key;
				$groupName = $value['name'];
				foreach ($value['fields'] as $key=>$value):
			?>
			<tr class="odd">
				<td>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" style="width:50px;" name="listorder[<?php echo $group . '.fields.' . $key;?>]" value="<?php if (isset($value['listorder']) && $value['listorder']!=0) { echo $value['listorder'];} else {echo 0;}?>" >
				</td>
				<td>
					<?php 
						if ($value['type'] == 'group') {
							$url = Yii::app()->createUrl("DatasetField/index", array("id"=>$dsModel->id, "enName"=>$key, "type"=>"group"));
							echo '<a href="'.$url.'">'.$value['name'].'</a>';
						} else {
							echo $groupName . ' >> ' . $value['name'];
						}
					?>
				</td>
				<td><?php echo $key;?></td>
				<td>
					<?php
						if ($value['type']=='field') {
							//$fieldType = '基本字段';
							$fieldType = '';
							if ($value['extra']['field_info']['field_type'] == 'reference') {
								$fieldType .= '关联字段';
							} elseif ($value['extra']['field_info']['field_type']=='normal') {
								switch ($value['extra']['field_info']['addition_type']) {
									case 'text':
										$fieldType .= '单行文本';
										break;
									case 'multitext':
										$fieldType .= '多行文本';
										break;
									case 'number':
										$fieldType .= '数值';
										break;
									case 'select':
										$fieldType .= '单选';
										break;
									case 'multiselect':
										$fieldType .= '多选';
										break;
									case 'image':
										$fieldType .= '图片';
										break;
								}
							}
						} elseif ($value['type']=='group') {
							$fieldType = '组字段';
						}
						echo $fieldType;
					?>
				</td>
				<td>
					<?php 
						if (isset($value['must'])) {
							if ($value['must']==1) {
								echo '是';
							} else {
								echo '否';
							}
						}
					?>
				</td>
				<td>
					<?php
						if (isset($value['extra']['filter']['type'])) {
							switch ($value['extra']['filter']['type']) {
								case '0':
									$filterType = '非';
									break;
								case '1':
									$filterType = '是(主要)';
									break;
								case '2':
									$filterType = '是(高级)';
									break;
							}
						} else {
							$filterType = '';
						}
						echo $filterType;
					?>
				</td>
				<td class="button-column">
					<a title="修改" onclick="FieldInfo(<?php echo $dsModel->id;?>, '<?php echo $key;?>', '<?php echo $value['type'];?>', '<?php echo $group;?>')" href="#">修改</a>
					- 
					<a class="delete" title="删除" href="javascript:confirmurl('<?php echo Yii::app()->createUrl("DatasetField/Delete", array("id"=>$dsModel->id, "group" => $group, "enName"=>$key, "type"=>$value['type']));?>', '确认要删除『 <?php echo $value['name'];?> 』吗？');">删除</a>
				</td>
			</tr>
			<?php endforeach;?>
			<?php endif; ?>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>
<div class="row">
	<div class="span1">
		<?php echo CHtml::submitButton('排序'); ?>
	</div>
</div>
<?php $this->endWidget();?>

<script type="text/javascript">
function confirmurl(url,message) {
	if(confirm(message)) {
		location.href = url;
	}
}
</script>
