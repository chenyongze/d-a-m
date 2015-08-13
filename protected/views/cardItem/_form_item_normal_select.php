<!-- 单选字段 -->
<!-- by author:yongze -->
<?php 
	if (!empty($group)) {
	    $fieldLabelId = "singleChangeLable_{$group}_{$key}_";
	    $selectItems = $data['extra']['field_info']['select_value'];
	    $selectItemValue = (isset($itemData[$enName])&&!empty($itemData[$enName])) ? $itemData[$enName] : -1;
	    $fieldName = "CardItem[data][{$group}][{$key}][$enName]";
	} else {
	    $fieldLabelId = "singleChangeLable_";
	    $selectItems = $data['extra']['field_info']['select_value'];
	    $selectItemValue = (isset($itemData[$enName])&&!empty($itemData[$enName])) ? $itemData[$enName] : -1;
	    $fieldName = "CardItem[data][$enName]";
	}
?>
<div class="row label">
	<div class="span2">
		<?php echo $data['name']?>
	</div>
</div>
<div class="row">
	<?php foreach($selectItems as $key=>$value): ?>
	<div class="span2">
		<input id="<?php echo $fieldLabelId.$enName.'_'.$key;?>" type="radio" <?php if ($selectItemValue==$value['value']) echo 'checked="checked"';?> name="<?php echo $fieldName;?>" value="<?=$value['value']?>" >
		<label for="<?php echo $fieldLabelId.$enName.'_'.$key;?>" style="display:inline;"><?php echo isset($value['value'])?$value['value']:$value;?></label>
	</div>
	<?php endforeach;?>
</div>
