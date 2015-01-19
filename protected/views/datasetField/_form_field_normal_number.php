<!-- 数值 -->
<?php
	$dropDownSelect0 = array('value'=>0);
	$dropDownSelect1 = array('value'=>1);
	$dropDownSelect2 = array('value'=>2);
	$dropDownSelect3 = array('value'=>3);
	if (isset($data['extra']['field_info']['num_type'])) {
		switch ($data['extra']['field_info']['num_type']) {
			case 0:
				$dropDownSelect0["selected"] = "selected";
				break;
			case 1:
				$dropDownSelect1["selected"] = "selected";
				break;
			case 2:
				$dropDownSelect2["selected"] = "selected";
				break;
			case 3:
				$dropDownSelect3["selected"] = "selected";
				break;
		}
	}

	$dropDown = '';
	$dropDown .= CHtml::tag('option', $dropDownSelect0,CHtml::encode('整数'),true);
	$dropDown .= CHtml::tag('option', $dropDownSelect1,CHtml::encode('1位小数'),true);
	$dropDown .= CHtml::tag('option', $dropDownSelect2,CHtml::encode('2位小数'),true);
	$dropDown .= CHtml::tag('option', $dropDownSelect3,CHtml::encode('不限位数'),true);

	if (isset($data['extra']['field_info']['limit_from'])) {
		$limit_from = intval($data['extra']['field_info']['limit_from']);
	} else {
		$limit_from = 0;
	}
	if (isset($data['extra']['field_info']['limit_to'])) {
		$limit_to = intval($data['extra']['field_info']['limit_to']);
	} else {
		$limit_to = 0;
	}

?>
<div class="row offset1">
		<select id="numType" name="fields[extra][field_info][num_type]"><?php echo $dropDown; ?></select>
</div>
<div class="row offset1">
      	范围：
</div>
<div class="row offset1">
	&nbsp;&nbsp;从&nbsp;&nbsp;
	<input type="text" placeholder="0为不限制" id="field_0_2_from" class="J_num_input" name="fields[extra][field_info][limit_from]" value="<?php echo $limit_from;?>"  style="width:100px;">&nbsp;&nbsp;
      	到&nbsp;&nbsp;
	<input type="text" placeholder="0为不限制" id="field_0_2_to" class="J_num_input" name="fields[extra][field_info][limit_to]"  value="<?php echo $limit_to;?>" style="width:100px;">
</div>
<script type="text/javascript">
	$("#numType").change(function(){
	})
</script>
