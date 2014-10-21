<!-- 单行文本 -->
<?php
if (!empty($group)) {
	$fieldLabelId = "imageName{$group}_{$key}_{$enName}";
	$fieldId = "image{$group}_{$key}_{$enName}";
	$fieldName = "CardItem[data][{$group}][{$key}][$enName]";
} else {
	$fieldLabelId = "imageName{$enName}";
	$fieldId = "image{$enName}";
	$fieldName = "CardItem[data][$enName]";
}
?>
<div class="row label">
	<div class="span2" id="<?=$fieldLabelId?>" >
		<?=$data['name']?>
	</div>
</div>
<div class="row" style="color:red;display:none;" id="<?=$fieldLabelId?>Error" >
	<div class="span3" id="<?=$fieldLabelId?>ErrorTxt"></div>
</div>
<div class="row">
	<div class="span5">
	<label> 图片URL：
	<?php
		$value = isset($itemData[$enName]) ? $itemData[$enName] : '';
		echo(CHtml::textField($fieldName, $value, array('class'=>'input-xxlarge', 'id'=>$fieldId)));
	?>
	</label>
	<label id="<?=$fieldId?>preview">
	<?php if ($value): ?>
		<a href="<?=$value?>" target="_blank"><img class="image-preview" src="<?=$value?>"/></a>
	<?php endif;?>
	</label>
	<input type="file" id="<?=$fieldId?>file" accept="image/*" value="上传图片">
	</div>
	<div class="span2 offset1">
	</div>
</div>
<script type="text/javascript">
	$('#<?=$fieldId?>file').change(function(e){
		file = e.target.files[0];
		var data = new FormData();
		var preview = $('#<?=$fieldId?>preview');
		var value = $('#<?=$fieldId?>');
		data.append('image', file);
		preview.text('图片上传中。。。');
		$.ajax({
			type: 'post',
			url: '/CardItem/uploadImage/name/' + file.name,
			data: data,
			dataType: 'json',
			contentType: false,
			processData: false,
			success: function (data) {
				if (data.code == 0) {
					var img = $('<a>').attr('href', data.data.url)
						.attr('target', '_blank')
						.append($('<img>')
							.attr('src', data.data.url)
							.attr('class', 'image-preview')
						);
					preview.html('');
					preview.append(img);
					value.val(data.data.url);
				} else {
					preview.text(data.message);
				}
			},
			error: function () {
				preview.text('上传接口调用失败');
			}
		});
	});
</script>
