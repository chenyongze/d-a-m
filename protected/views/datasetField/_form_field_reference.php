<?php
$fieldsArray = isset($data['extra']['field_info']['fields'])?$data['extra']['field_info']['fields']:array();
$referenceField = isset($data['extra']['field_info']['reference_field'])?$data['extra']['field_info']['reference_field']:'';
$type = isset($data['extra']['field_info']['type'])?$data['extra']['field_info']['type']:0;
?>
<div class="row offset1">
	<label>关联字段<span style="color:red">(必选)</span>: </label>
</div>
<div class="row offset1">
	<?php foreach ($fieldArray as $key=>$value): ?>
	<label class="span2"><input value="<?=$key?>" <?php if ($key == $referenceField) echo "checked"; ?> type="radio" name="fields[extra][field_info][reference_field]"  > <?=$value?></label>
	<?php endforeach; ?>
</div>
<div class="row offset1">
	<label>调用字段: </label>
</div>
<div class="row offset1">
	<?php foreach ($fieldArray as $key=>$value): ?>
	<label class="span2"><input value="<?=$key?>" <?php if (in_array($key, $fieldsArray)) echo "checked"; ?> type="checkbox" name="fields[extra][field_info][fields][]"  > <?=$value?></label>
	<?php endforeach; ?>
</div>
<div class="row offset1">
	<label>关联类型: </label>
</div>
<div class="row offset1">
	<label class="span2"><input value="0" type="radio" name="fields[extra][field_info][type]" <?php if ($type != 1) echo 'checked';?>>单选</label>
	<label class="span2"><input value="1" type="radio" name="fields[extra][field_info][type]" <?php if ($type == 1) echo 'checked';?>>多选</label>
</div>
</div>
