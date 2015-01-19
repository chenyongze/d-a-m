<!-- 单选字段 -->
<?php 
	$selectItems = $data['extra']['field_info']['select_value'];
	$selectItemValue = (isset($itemData[$enName])&&!empty($itemData[$enName])) ? $itemData[$enName] : -1;
?>
<div class="row label">
	<div class="span2">
		<?php echo $data['name']?>
	</div>
</div>
<div class="row">
	<?php foreach($selectItems as $key=>$value): ?>
	<div class="span2">
		<input id="singleChangeLable_<?php echo $enName.'_'.$key;?>" type="radio" <?php if ($selectItemValue==$value['value']) echo 'checked="checked"';?> name="CardItem[data][<?php echo $enName;?>]" value="<?=$value['value']?>" >
		<label for="singleChangeLable_<?php echo $enName.'_'.$key;?>" style="display:inline;"><?php echo isset($value['value'])?$value['value']:$value;?></label>
	</div>
	<?php endforeach;?>
</div>
