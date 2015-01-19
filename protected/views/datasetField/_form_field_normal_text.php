<!-- 多行文本 -->
<?php
	if (isset($data['extra']['field_info']['length'])) {
		$length = intval($data['extra']['field_info']['length']);
	} else {
		$length = 0;
	}
?>
<!-- 单行文本 -->
<div class="row offset1">
	长度限制：&nbsp;<input type="text" id="field_0_0" placeholder="0为不限制" class="J_num_input" name="fields[extra][field_info][length]" value="<?php echo $length;?>" >&nbsp;&nbsp;字符
</div>
