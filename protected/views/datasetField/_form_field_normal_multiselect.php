<!-- 选项 -->
<?php
	$selectValue = isset($data['extra']['field_info']['select_value']) ? $data['extra']['field_info']['select_value'] : array();
?>

<div class="span8 offset1">
<label>选项：&nbsp;&nbsp;&nbsp; <span style="font-size:30px;"><a href="javascript:void(0)" onclick="addSelectItem()">+</a></span></label>
</div>
<?php if (empty($selectValue)): ?>
<div class="span8 offset1">
	<input type="text" class="option" style="width:200px;" name="fields[extra][field_info][select_value][0][value]" value="" placeholder="名称" /> <input type="text" class="colorpicker-default form-control" style="width:60px;" name="fields[extra][field_info][select_value][0][color]" value="" placeholder="颜色"/>
	&nbsp;&nbsp; <span style="font-size:30px;"><a href="javascript:void(0)" onclick="removeSelectItem(this)">-</a></span>
</div>
<?php else: ?>
	<?php foreach($selectValue as $key=>$value): ?>
		<div class="span8 offset1">
			<input type="text" class="option" style="width:200px;" name="fields[extra][field_info][select_value][<?php echo $key; ?>][value]" value="<?php echo isset($value['value'])?$value['value']:$value;?>" placeholder="名称" />
			<input type="text" class="colorpicker-default form-control" style="width:60px;" name="fields[extra][field_info][select_value][<?php echo $key; ?>][color]" value="<?php echo (isset($value['color']))?$value['color']:''; ?>" placeholder="颜色"/>
			&nbsp;&nbsp; <span style="font-size:30px;"><a href="javascript:void(0)" onclick="removeSelectItem(this)">-</a></span>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
<div class="selectItem" style="display:none;"></div>


<script type="text/javascript">
	function addSelectItem() {
		var key = $('.option').length;
		var selectItemHtml = '<div class="span8 offset1"> <input type="text" class="option" style="width:200px;" name="fields[extra][field_info][select_value]['+key+'][value]" value="" placeholder="名称" /> <input type="text" class="colorpicker-default form-control" style="width:60px;" name="fields[extra][field_info][select_value]['+key+'][color]" value="" placeholder="颜色"/> &nbsp;&nbsp; <span style="font-size:30px;"><a href="javascript:void(0)" onclick="removeSelectItem(this)">-</a></span></div>';
		$(".selectItem").before(selectItemHtml);
		$('.colorpicker-default').colorpicker();
	}
	function removeSelectItem(obj) {
		$(obj).parent().parent().remove();
	}
	$('.colorpicker-default').colorpicker();
</script>
