<?php
$this->breadcrumbs=array(
	'游戏数据库' => array('site/index'),
	'数据管理'=>array('index'),
	$tableName
);
?>
<style>
<!--
ul.list li{
	float: left;
    width: 260px;
	list-style: none outside none;
	margin-right: 22px;
}
-->
</style>
<form method="post">
	<ul class="list">
		<?php foreach ($fields as $name):?>
			<li>
				<label>
					<input type="checkbox" class="all_input" name="checked[]" value="<?php echo $name;?>" <?php if (in_array($name, $selectedFields)): ?>checked="checked"<?php endif;?>/> 
					<?php echo $name;?>
				</label>
			</li>
		<?php endforeach; ?>
		<div class="clear"></div>
	</ul>
	<div style="text-align: center">
		<input type="hidden" name="name" value="<?php echo $tableName;?>"/>
		<a href="javascript:;" class="select_all_click">[反选]</a>
		<input type="submit" value="导出"/>
	</div>
	
	<?php echo $this->renderPartial('_exportlist', array('exportTables'=>$exportTables,'exe_msg'=>$exe_msg)); ?>
</form>