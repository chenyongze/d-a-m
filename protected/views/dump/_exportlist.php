<!-- 导出文件列表 -->
<?php if($exe_msg){ ?>
	<div style="color: #a00"><?php echo $exe_msg; ?></div>
<?php }?>
<table>
	<tr>
		<th>名称</th>
		<th>文件大小(KB)</th>
		<th>修改时间</th>
		<th>操作</th>
	</tr>
	<?php if($exportTables){ ?>
		<?php foreach($exportTables as $key=>$vo){ ?>
		<tr>
			<td style="padding-left:5%"><?php echo $vo['name'];?></td>
			<td style="text-align: right;padding-right:5%"><?php echo number_format(ceil($vo['size']));?></td>
			<td style="text-align: center;"><?php echo $vo['edittime'];?></td>
			<td style="text-align: center;">
				<a href="/dump/viewfile/id/<?php echo $vo['id'];?>" target="_blank">查看</a>
				<a href="/dump/downfile/id/<?php echo $vo['id'];?>">下载</a>
		    	<a href="/dump/deletefile/id/<?php echo $vo['id'];?>" onClick="return confirm('确认删除该文件么?');">删除</a>
		    	<a href="/dump/synjstodb/id/<?php echo $vo['id'];?>" onClick="return confirm('确认使用该数据文件更新现有数据么？旧数据将无法恢复！');">导入</a>
			</td>
		</tr>
		<?php } ?>
	<?php }else{ ?>
		<tr><td style="text-align: center;" colspan="3">没有任何导出文件</td></tr>
	<?php } ?>
</table>