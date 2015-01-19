<!-- 多行文本 -->
<?php
if (!empty($group)) {
	$fieldLabelId = "multiTextName{$group}_{$key}_{$enName}";
	$fieldId = "multiText{$group}_{$key}_{$enName}";
	$fieldName = "CardItem[data][{$group}][{$key}][$enName]";
} else {
	$fieldLabelId = "multiTextName{$enName}";
	$fieldId = "multiText{$enName}";
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
<div class="span9">
	<div class="span5" style="margin-left:5px;">
	<?php
		$value = isset($itemData[$enName]) ? $itemData[$enName] : '';
		echo(CHtml::textArea($fieldName, $value, array('class'=>'input-block-level','rows'=>'6', 'style'=>'width:540px;', 'id'=>$fieldId)));
	?>
	</div>
	<div class="span1 offset1">
		长度限制
		&nbsp;<span style="color:red;font-size:15px;"><?php echo $data['extra']['field_info']['length']; ?></span>
	</div>
</div>
</div>
<?php if ($data['extra']['field_info']['length']): ?>
<script type="text/javascript">
	$("#<?=$fieldId?>").blur(function(e) {
		var str_len = getStrLen($(this).val());
		if (str_len><?php echo intval($data['extra']['field_info']['length']); ?>) {
			$("#<?=$fieldLabelId?>").css("color", "red");
			$("#<?=$fieldLabelId?>ErrorTxt").text('<?=$data['name']?>字段 字符超出长度限制!');
			$("#<?=$fieldLabelId?>Error").show();
			permitSubmit = 0;
			e.preventDefault();
		} else {
			$("#<?=$fieldLabelId?>Error").hide();
			$("#<?=$fieldLabelId?>").css("color", "black");
			permitSubmit = 1;
		}
	})
</script>
<?php endif; ?>
