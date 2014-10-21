<?php
$fieldsArray = isset($data['extra']['field_info']['fields'])?$data['extra']['field_info']['fields']:array();
$referenceField = isset($data['extra']['field_info']['reference_field'])?$data['extra']['field_info']['reference_field']:'';
$type = isset($data['extra']['field_info']['type'])?$data['extra']['field_info']['type']:0;

if (!empty($group)) {
	$fieldLabelId = "refrenceSelected{$group}_{$key}_{$enName}";
	$fieldId = "refrenceSelected{$group}_{$key}_{$enName}";
	$fieldName = "CardItem[data][{$group}][{$key}][$enName][]";
} else {
	$fieldLabelId = "refrenceSelected{$enName}";
	$fieldId = "refrenceSelected{$enName}";
	$fieldName = "CardItem[data][$enName][]";
}
$value = isset($itemData[$enName]) ? $itemData[$enName] : array();
?>
<div class="row label">
	<div class="span2">
	<?php echo $data['name'];?>
	</div>
</div>
<div class="row">
	<div class="span5">
	<?php if ($referenceItems):?>
		<?php if ($type == 0):?>
			<select name="<?=$fieldName?>">
				<option value=''>请选择</option>
				<?php foreach ($referenceItems as $ritem):?>
				<option value="<?=$ritem->data[$referenceField]?>" <?=(in_array($ritem->data[$referenceField], $value)?'selected':'')?>><?=$ritem->data[$referenceField]?></option>
				<?php endforeach;?>
			</select>
		<?php else:?>
			<label>已选择：<span id="<?=$fieldLabelId?>"><?=($value?join(',', $value):'')?></span><br /></label>
			<em>按住 Ctrl 键可多选</em><br />
			<select name="<?=$fieldName?>" multiple="multiple" size="5" id="<?=$fieldId?>" onchange="change<?=$fieldId?>($(this).val());">
				<?php foreach ($referenceItems as $ritem):?>
				<option value="<?=$ritem->data[$referenceField]?>" <?=(in_array($ritem->data[$referenceField], $value)?'selected':'')?>><?=$ritem->data[$referenceField]?></option>
				<?php endforeach;?>
			</select>
			<script type="text/javascript">
				function change<?=$fieldId?>(val) {
					if (val != null) {
						$('#<?=$fieldLabelId?>').text(val);
					} else {
						$('#<?=$fieldLabelId?>').text('');
					}
				}
			</script>
		<?php endif;?>
	<?php else:?>
		暂无可关联的数据。
	<?php endif;?>
	</div>
</div>
