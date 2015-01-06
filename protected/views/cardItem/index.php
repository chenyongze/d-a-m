<?php
/**
* Item列表页
* @author gentle
*/
if (Yii::app()->user->name=='creator') {
	$this->breadcrumbs = array(
		'卡牌库' => array('CardDb/index'),
		$dbModel->name => array('CardDs/index/id/'.$datasetId),
		$dsModel->name,
		'数据列表',
	);
} else {
	$this->breadcrumbs = array(
		'游戏数据库' => array('CardDb/index'),
		'数据库管理' => array('CardDb/index'),
		$dbModel->name,
		$dsModel->name,
	);
}

$this->leftTree = $dataTree;
?>

<script type="text/javascript">
function selectall(name) {
	if ($("#check_box").attr("checked")=='checked') {
		$("input[name='"+name+"']").each(function() {
			$(this).attr("checked","checked");
			
		});
	} else {
		$("input[name='"+name+"']").each(function() {
			$(this).removeAttr("checked");
		});
	}
}
</script>

<?php $this->widget('application.extensions.FancyBox.EFancyBox', array()); ?>
<div class="span1">
	<input type="button" onclick="ItemInfo()" value="发布新数据" id="CreateItem">
</div>
<div class="span1">
	<input type="button" value="导入数据" id="ImportData">
</div>
<div class="span1">
	<input type="button" value="导出数据" id="ExportData" onClick="location.href='/CardItem/export/id/<?php echo $datasetId;?>?<?php echo $_SERVER['QUERY_STRING']?>'">
</div>
<div class="span1">
	<input type="button" value="导出模板" id="ExportTplData" onClick="location.href='/CardItem/exporttpl/id/<?php echo $datasetId;?>'">
</div>
<script type="text/javascript">
	function ItemInfo() {
		var itemId = arguments[0] ? arguments[0] : 0;
		if (itemId!=0) {
			var ajaxHref='/CardItem/Update/id/'+itemId;
		} else {
			var ajaxHref='/CardItem/Create/id/<?php echo $datasetId;?>';
		}
		window.location = ajaxHref;
		$.fancybox({
			'hideOnOverlayClick'	: false,
			'enableEscapeButton'	: false,
			'autoScale'		: false,
			'autoDimensions'	: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'width'			: 900,
			'height'		: 400,
			'href'			: ajaxHref,
			'type'			: 'inline',
		});
		$("#fancybox-close").bind("click", function (){
			$.fancybox.close();
		});
		return false;
	}
	$("#ImportData").click(function() {
		$.fancybox({
			'hideOnOverlayClick'	: false,
			'enableEscapeButton'	: false,
			'autoScale'		: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'width'			: 1600,
			'height'		: 400,
			'href'			: '/CardItem/import/id/<?php echo $datasetId;?>',
			'type'			: 'inline',
		});
		$("#fancybox-close").bind("click", function (){
			$.fancybox.close();
		});
		return false;
	})
</script>
	<div class="row"></div>
	<div class="row"></div>
<!-- 提示信息 -->
<div class="row">
<div class="span6">
<?php if (!empty($info)): ?>
	<?php if ($info['type'] == 'success'): ?>
	<div class="alert alert-success">
	  <button type="button" class="close" data-dismiss="alert">×</button>
	  <?=$info['msg']?>
	</div>
	<?php else: ?>
	<div class="alert alert-error">
	  <button type="button" class="close" data-dismiss="alert">×</button>
	  <?=$info['msg']?>
	</div>
	<?php endif; ?>
<?php endif; ?>
</div>
</div>


	<div class="row list_filter">
		<form action="<?php echo $this->createUrl('cardItem/index', array('id'=>$_GET['id']));?>">
			<div class="span1" style="width:auto;">
				<select name="kfield" style="width:100px;" title="请选择查询的字段">
					<?php foreach($dsModel->getFieldNameMap() as $key=>$value){ ?>
							<option value="<?php echo $key?>" <?php echo (isset($_GET['kfield'])&&$_GET['kfield']==$key)?' selected="selected" ':''?>><?php echo $value?> </option>
					<?php } ?>
				</select>
				<select name="koperator" style="width:70px;" title="请选择查询操作符">
					<?php $operator = Yii::app()->params['filter_operator'];?>
					<?php foreach($operator as $ko=>$vo){ ?>
						<option value="<?php echo $ko?>" <?php echo (isset($_GET['koperator'])&&$_GET['koperator']==$ko)?' selected="selected" ':''?>><?php echo $vo?></option>
					<?php } ?>
				</select>
				<input type="text" name="kword" title="请填一个查询字符串,留空表示空字符串或0，使用符合类型查询时多个元素使用半角逗号隔开" value="<?php echo isset($_GET['kword'])?$_GET['kword']:''?>"/>
			</div>
			<div class="span1">
				<input type="submit" name="sub" value="查询"/>
			</div>
		</form>
	</div>

	<form action="/CardItem/Delete" method="post" >
	<div id="card-grid" class="grid-view">
		<table class="table table-hover table-striped table-bordered table-condensed">
			<thead>
				<tr>
					<th class="button-column" style="width:50px;">
						<input type="checkbox" id="check_box" onclick="selectall('CardItem[id][]');">
					</th>
					<?php 
						$i = 1;
						foreach ($dsModel->fields as $key=>$value):
							if ($value['type'] != 'group'):
							$i += 1;
					?>
						<th id="card-grid_c<?=$i?>">
						<?=$value['name']?>
						</th>
					<?php
							endif;
						endforeach;
					?>
					<th class="button-column" id="card-grid_c<?=($i+1)?>">操作</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				foreach ($itemModel as $keyItem=>$valueItem):
			?>
			<tr class="odd">
				<td><input type="checkbox" name="CardItem[id][]" value="<?=$valueItem['id'];?>" > </td>
				<?php 
					foreach ($dsModel->fields as $key=>$value):
						if ($value['type'] == 'field') :
				?>
					<td style="width:200px;overflow:hidden;height:30px;">&nbsp;
						<?php
							$fieldType = $value['extra']['field_info']['field_type'];
							$additionType = $value['extra']['field_info']['addition_type'];
							if ($fieldType == 'normal') {
								switch ($additionType) {
									case 'select':
										if (isset($valueItem['data'][$key])) {
											echo $valueItem['data'][$key];
										}
										break;
									case 'multiselect':
										if (isset($valueItem['data'][$key]) && !empty($valueItem['data'][$key])) {
											$selectItems = $valueItem['data'][$key];
											if(is_array($selectItems)){
												$selectItems = join(',', $selectItems);
											}
											
											echo $selectItems;
										} else {
											echo "";
										}
										break;
									case 'image':
										if (isset($valueItem['data'][$key]) && !empty($valueItem['data'][$key])) {
											echo sprintf('<a href="%1$s" target="_blank"><img class="image-preview-small" src="%1$s" style="max-height: 150px;"/></a>', $valueItem['data'][$key]);
										} else {
											echo "";
										}
										break;
									default :
										if (isset($valueItem['data'][$key])) {
											echo mb_strimwidth($valueItem['data'][$key], 0, 180, '...', 'utf-8');
										} else {
											echo "";
										}
								}
							//调用元素集字段
							} elseif($fieldType == 'reference') {
								$selectItems = $valueItem['data'][$key];
								if(is_array($selectItems)){
									$selectItems = join(',', $selectItems);
								}
								echo $selectItems;
							}
						?>
					</td>
				<?php 
						endif;
					endforeach;
				?>
				<td class="button-column" style="width:200px;overflow:hidden;height:30px;">
					<a title="修改" onclick="ItemInfo(<?php echo $valueItem->id;?>)" href="#">修改</a>
					- 
					<a class="delete" title="删除" href="javascript:confirmurl('<?php echo Yii::app()->createUrl("CardItem/Delete", array("id"=>$valueItem->id));?>', '确认要删除这条记录吗？');">删除</a>
				</td>
			</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>

	<div class="row">
		<div class="span1"><label for="check_box">全选/反选</label></div>
		<div class="span1"><input type="submit" value="删除" ></div>
	</div>
	</form>
	<div class="row" style="padding-top:5px;">
		<?php
			$this->widget('CLinkPager', array(
				'pages' => $pages,
			));
		?>
	</div>

<script type="text/javascript">
function confirmurl(url,message) {
	if(confirm(message)) {
		location.href = url;
	}
}
</script>
