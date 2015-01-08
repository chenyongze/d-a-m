<?php
$this->breadcrumbs=array(
	'游戏数据库' => array('site/index'),
	'数据管理',
);
?>
<style>
<!--
ul.list li{
	float: left;
    width: 320px;
	list-style: none outside none;
	margin: 2px 10px;
}
ul label{
	display: inline;
}
table {
    margin-bottom: 1.4em;
    width: 100%;
}
-->
</style>
<form method="post" action="/dump/export">
	<ul class="list">
		<?php if(empty($tables)):?>
			数据库为空
		<?php else: ?>
			<?php foreach ($tables as $en_name=>$name):
				$rows = DBModel::model()->getCount($en_name);
			?>
				<li>
					<label>
						<input type="checkbox" class="all_input" name="checked[]" value="<?php echo $en_name;?>" <?php if (in_array($name, $selectedTables)): ?>checked="checked"<?php endif;?>/> 
						<b><?php echo $name; ?></b> (<?php echo $rows?>)
					</label>
					<?php 
						/* 按照字段导出暂缓开放
						if(preg_match('/^item/i', $name)){
						 	echo CHtml::link('进入', '/dump/cexport/name/'.$name);
						}*/ 
					?>&nbsp;&nbsp;
					<?php 
						if(empty($rows) && $en_name==$name){
							echo CHtml::link('删除', '/dump/tabledrop/name/'.$en_name, array('confirm'=>'确定要彻底删除该表么?现有数据和结构都将无法还原！'));
						}else if($rows){
							echo CHtml::link('清空', '/dump/tableremove/name/'.$en_name, array('confirm'=>'确定要清空该表么?现有数据将无法还原！'));
						}
					?>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
		<div class="clear"></div>
	</ul>
	<div style="text-align: center">
		<a href="javascript:;" class="select_all_click">[反选]</a>
		<input type="submit" value="导出"/>
	</div>
	
	<?php echo $this->renderPartial('_exportlist', array('exportTables'=>$exportTables,'exe_msg'=>$exe_msg)); ?>
</form>