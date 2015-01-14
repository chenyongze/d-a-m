<?php
$this->breadcrumbs = array(
	'操作日志',
);

?>

<!-- 提示信息 -->
<div class="row">
<div class="span6 mfInfoBox">
<?php
	if (!empty($info)):
?>
	<?php
		if ($info['type'] == 'success'):
	?>
	<div class="alert alert-success">
	  <button type="button" class="close" data-dismiss="alert">×</button>
	  <?php echo $info['msg']; ?>
	</div>
	<?php
		else:
	?>
	<div class="alert alert-error">
	  <button type="button" class="close" data-dismiss="alert">×</button>
	  <?php echo $info['msg']; ?>
	</div>
	<?php
		endif;
	?>
<?php
	endif;
?>
</div>
</div>

	<div class="row list_filter">
		<form action="<?php echo $this->createUrl('log/index');?>">
			<div class="span1" style="width:auto;">
				<select name="kfield" style="width:110px;" title="请选择查询的字段">
					<?php foreach($attr as $key=>$value){ ?>
							<option value="<?php echo $key?>" <?php echo (isset($_GET['kfield'])&&$_GET['kfield']==$key)?' selected="selected" ':''?>><?php echo $value?> </option>
					<?php } ?>
				</select>
				<select name="koperator" style="width:75px;" title="请选择查询操作符">
					<?php $operator = Yii::app()->params['filter_operator'];?>
					<?php foreach($operator as $ko=>$vo){ ?>
						<option value="<?php echo $ko?>" <?php echo (isset($_GET['koperator'])&&$_GET['koperator']==$ko)?' selected="selected" ':''?>><?php echo $vo?></option>
					<?php } ?>
				</select>
				<input type="text" name="kword" title="请填一个查询字符串,留空表示空字符串或0，使用符合类型查询时多个元素使用半角逗号隔开" value="<?php echo isset($_GET['kword'])?$_GET['kword']:''?>"/>
			</div>
			<div class="span1">
				<input type="submit" name="sub" value="查询"/>
			</div>
			<?php if(isset($_GET['kfield'])){?>
			<div class="span1">
				<input type="button" name="clear" value="清除" onClick="location.href='/log/index'"/>
			</div>
			<?php }?>
		</form>
	</div>

	<form action="/CardItem/Delete" method="post" >
	<div id="card-grid" class="grid-view">
		<table class="table table-hover table-striped table-bordered table-condensed">
			<thead>
				<tr>
					<th class="button-column" style="width:50px;"><?=$attr['id']?></th>
					<th><?=$attr['uid']?></th>
					<th><?=$attr['uname']?></th>
					<th><?=$attr['obj_cate']?></th>
					<th><?=$attr['obj_id']?></th>
					<th><?=$attr['acttime']?></th>
					<th><?=$attr['txt']?></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			if($logModels){
				foreach ($logModels as $keyItem=>$valueItem):?>
				<tr class="odd">
					<td><?php echo $valueItem->id ?></td>
					<td><?php echo $valueItem->uid ?></td>
					<td><?php echo $valueItem->uname ?></td>
					<td><?php echo $valueItem->obj_cate ?></td>
					<td><?php echo $valueItem->obj_id ?></td>
					<td><?php echo date('Y-m-d H:i:s', $valueItem->acttime) ?></td>
					<td><?php echo $valueItem->txt ?></td>
				</tr>
			<?php
			 endforeach;
			}else{?>
				<tr><td colspan='7' style="text-align:center;">没有这样的数据！</td></tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	</form>
	<div class="row" style="padding-top:5px;">
		<?php
			$this->widget('CLinkPager', array(
				'pages' => $pages,
			));
		?>
	</div>
