<!-- 单行文本 -->
<?php
if (!empty($group)) {
	$fieldLabelId = "singleTextName{$group}_{$key}_{$enName}";
	$fieldId = "singleText{$group}_{$key}_{$enName}";
	$fieldName = "CardItem[data][{$group}][{$key}][$enName]";
} else {
	$fieldLabelId = "singleTextName{$enName}";
	$fieldId = "singleText{$enName}";
	$fieldName = "CardItem[data][$enName]";
}
?>
<div class="row label">
	<div class="span2" id="<?=$fieldLabelId?>" >
		<?php echo $data['name'];?>
	</div>
</div>
<div class="row" style="color:red;display:none;" id="<?=$fieldLabelId?>Error" >
	<div class="span3" id="<?=$fieldLabelId?>ErrorTxt"></div>
</div>
<div class="row">
	<div class="span5">
	<?php
		$value = isset($itemData[$enName]) ? $itemData[$enName] : '';
		echo(CHtml::textField($fieldName, $value, array('class'=>'input-xxlarge', 'id'=>$fieldId)));
	?>
	</div>
	<div class="span2 offset1">
		长度限制
		&nbsp;<span style="color:red;font-size:15px;"><?php echo $data['extra']['field_info']['length']; ?></span>
	</div>
</div>
<?php if ($data['extra']['field_info']['length']): ?>
<script type="text/javascript">
	$('#<?=$fieldId?>').blur(function(e) {
		var str_len = getStrLen($(this).val());
		if (str_len><?php echo intval($data['extra']['field_info']['length']); ?>) {
			$("#<?=$fieldLabelId?>").css("color", "red");
			//alert('<?=$data['name']?>字段 字符超出长度限制!');
			$("#<?=$fieldLabelId?>ErrorTxt").text('<?php echo $data['name']; ?>字段 字符超出长度限制!');
			$("#<?=$fieldLabelId?>Error").show();
			//$(this).focus();
			permitSubmit = 0;
			e.preventDefault();
		} else {
			$("#<?=$fieldLabelId?>Error").hide();
			$("#<?=$fieldLabelId?>").css("color", "black");
			permitSubmit = 1;
		}
	});
</script>
<?php endif; ?>
