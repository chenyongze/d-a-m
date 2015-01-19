<?php
$fieldLabelId = "groupName{$enName}";
$fieldId = "group{$enName}";
?>
<table class="table table-hover table-striped table-bordered table-condensed" id="<?=$fieldId?>" style="margin-top:10px;">
	<thead>
		<tr>
			<th><?php echo $data['name'];?> &nbsp;&nbsp; <span style="font-size:30px;"><a href="javascript:void(0)" onclick="addGroupItem<?=$enName?>(this)">+</a></span> </th>
		</tr>
	</thead>
	<tbody>
		<?php
			if (isset($dataHtml)):
				foreach ($dataHtml as $k => $dHtml):
		?>
			<tr id="<?=$fieldId?>Item_<?=$k?>">
				<td style="padding-left: 50px;">
					<span style="font-size:30px;position:absolute;right:50px;background:#fff;"><a href="javascript:void(0)" onclick="removeGroupItem<?=$enName?>(this)">-</a></span>
					<?=$dHtml?>
				</td>
			</tr>
		<?php
				endforeach;
			endif;
		?>
	</tbody>
</table>
<script type="text/javascript">
	function addGroupItem<?=$enName?>() {
		var key = $('#<?=$fieldId?>').find('tbody')[0].children.length;
		var groupItemTR = $('<tr>').attr('id', '<?=$fieldId?>Item_'+key);
		var groupItemTD = $('<td>').css('padding-left', 50);
		var removeButton = '<span style="font-size:30px;position:absolute;right:50px;background:#fff;"><a href="javascript:void(0)" onclick="removeGroupItem<?=$enName?>(this)">-</a></span>';
		$('#<?=$fieldId?>').find('tbody').append(groupItemTR);
		groupItemTR.append(groupItemTD);
		groupItemTD.load('/CardItem/groupItemHtml/id/<?=$datasetId?>/group/<?=$enName?>/output/1/index/'+key, function(){
				groupItemTD.prepend(removeButton);
			});
	}
	function removeGroupItem<?=$enName?>(obj) {
		$(obj).parent().parent().remove();
	}
</script>
