<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'系统管理'=>array('index'),
	'数据管理',
);
?>
<h1>数据管理</h1>
（注意：功能开发中，其中的所有功能暂时都不要使用）
<div class='view' id="data_list">
	<?php if($exe_msg){ ?>
		<div style="color: #a00"><?php echo $exe_msg; ?></div>
	<?php }?>
	<ul class="operations">
		<form name="port_form" id="port_form" action="/index.php" method="post" enctype="multipart/form-data">
			<li style="display: inline;">
				<input type="file" name="file"/>
				<input type="hidden" name="r" value="system/addfile">
				<input type="hidden" name="url" value="confindex">
				<input type="submit" value="添加">每个数据文件都是名为"entity_游戏代码_实体代码"的js文件,如‘entity_dota2_hero.js’
			</li>
			<li style="float:right">
				<?php echo CHtml::link('数据导入导出', '/dump/dataexport', array('style'=>"display: inline;")); ?>
			</li>
		</form>
	</ul>
	<table>
		<tr>
			<th>名称</th>
			<th>文件大小(B)</th>
			<th>修改时间</th>
			<th>操作</th>
		</tr>
		<?php if($rs){ ?>
			<?php foreach($rs as $key=>$vo){ ?>
			<tr>
				<td><?php echo $vo['name'];?></td>
				<td><?php echo $vo['size'];?></td>
				<td><?php echo $vo['edittime'];?></td>
				<td>
					<a href="<?php echo $this->uploadUrl();?>/file/system/<?php echo $vo['name'];?>" target="_blank">查看</a>
					<a  href="<?php echo $this->uploadUrl();?>/dump/downfile/name/<?php echo $vo['name'];?>">下载</a>
				 	<a href="<?php echo $this->uploadUrl();?>/dump/deletefile/name/<?php echo $vo['name'];?>" onClick="return confirm('确认删除该文件么?');">删除</a>
				 	<a href="<?php echo $this->uploadUrl();?>/dump/synjstodb/name/<?php echo $vo['name'];?>" onClick="return confirm('主键不存在才会添加！确认要导入该数据文件么?');">导入</a>
				</td>
			</tr>
			<?php } ?>
		<?php }else{ ?>
			<tr><td style="text-align: center;" colspan="3">没有任何数据文件</td></tr>
		<?php } ?>
	</table>
</div>




