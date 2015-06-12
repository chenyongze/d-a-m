<!-- 多行文本 -->
<?php
if (!empty($group)) {
	$fieldLabelId = "numItemName{$group}_{$key}_{$enName}";
	$fieldId = "numItem{$group}_{$key}_{$enName}";
	$fieldName = "CardItem[data][{$group}][{$key}][$enName]";
} else {
	$fieldLabelId = "numItemName{$enName}";
	$fieldId = "numItem{$enName}";
	$fieldName = "CardItem[data][$enName]";
}
?>

<!-- 数值字段 -->
<div class="row label">
	<div class="span2" id="<?=$fieldLabelId?>" ><?php echo $data['name'];?></div>
</div>
<div class="row" style="color:red;display:none;" id="<?=$fieldLabelId?>Error" >
	<div class="span3" id="<?=$fieldLabelId?>ErrorTxt"></div>
</div>
<div class="row">
	<div>
	<div class="span5">
	<?php
		$value = isset($itemData[$enName]) ? $itemData[$enName] : '';
		echo(CHtml::textField($fieldName, $value, array('class'=>'input-xxlarge', 'id'=>$fieldId)));
	?>
	</div>
	</div>
	<div class="span2 offset1">
		<?php
			$limit_from = $data['extra']['field_info']['limit_from'];;
			$limit_to = $data['extra']['field_info']['limit_to'];;
		?>
		<?php if($limit_from==0 && $limit_to==0): ?>
			<font color="red">不限长度</font>
		<?php else: ?>
		从&nbsp;<span style="color:red;font-size:15px;"><?=$limit_from?></span>到&nbsp;<span style="color:red;font-size:15px;"><?=$limit_to?></span>
		<?php endif;?>
		&nbsp;&nbsp;&nbsp;
		<?php
			$numType = '';
			switch ($data['extra']['field_info']['num_type']) {
				case "0":
					$numType = '整数';
					break;
				case "1":
					$numType = '1位小数';
					break;
				case "2":
					$numType = '2位小数';
					break;
				case "3":
					$numType = '不限位数';
					break;
			}
			echo "<span style='color:blue;font-size:15px;' >".$numType."</span>";
		?>
	</div>
</div>
<script type="text/javascript">
	$("#<?=$fieldId?>").blur(function(e) {
		//var str_len = getStrLen($(this).val());
		var str_val = $(this).val();
		if (str_val.indexOf('.') >= 0) {
			var num_len=str_val.split('.')[1].length;
		} else {
			var num_len=0;
		}
		var limit_from = <?php echo $data['extra']['field_info']['limit_from'];?>;
		var limit_to = <?php echo $data['extra']['field_info']['limit_to'];?>;
		var num_type = <?php echo $data['extra']['field_info']['num_type'];?>;

		if (num_type==0 && num_len!=0) {
			$("#<?=$fieldLabelId?>").css("color", "red");
			$(this).focus();
			permitSubmit = 0;
			e.preventDefault();
			$("#<?=$fieldLabelId?>ErrorTxt").text('所填数值非整数!');
			$("#<?=$fieldLabelId?>Error").show();
		} else if (num_type==1 && num_len!=1) {
			$("#<?=$fieldLabelId?>").css("color", "red");
			$(this).focus();
			permitSubmit = 0;
			e.preventDefault();
			$("#<?=$fieldLabelId?>ErrorTxt").text('小数位数异常,请检查!');
			$("#<?=$fieldLabelId?>Error").show();
		} else if (num_type==2 && num_len!=2) {
			$("#<?=$fieldLabelId?>").css("color", "red");
			$(this).focus();
			permitSubmit = 0;
			e.preventDefault();
			$("#<?=$fieldLabelId?>ErrorTxt").text('小数位数异常,请检查!');
			$("#<?=$fieldLabelId?>Error").show();
		} else if (limit_to!=0 && (str_val<limit_from || str_val>limit_to)) {
			$("#<?=$fieldLabelId?>").css("color", "red");
			$(this).focus();
			permitSubmit = 0;
			e.preventDefault();
			$("#<?=$fieldLabelId?>ErrorTxt").text('数值范围异常,请检查!');
			$("#<?=$fieldLabelId?>Error").show();
		} else {
			$("#<?=$fieldLabelId?>Error").hide();
			$("#<?=$fieldLabelId?>").css("color", "black");
			permitSubmit = 1;
		}
	})
</script>
