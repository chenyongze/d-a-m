<?php
/**
* 改为读取Js数组对象里边数组往DOM元素里边填值更方便
* @author gentle
*/
$enName = isset($enName) ? $enName : '';
if (isset($group) && $enName) {
	$field = $model->fields[$group]['fields'][$enName];
} elseif ($enName) {
	$field = $model->fields[$enName];
}
?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'field-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<div class="row">
		<div class="span2 offset1">
		<?php 
			if (isset($errorMsg)) {
				echo "<h5><font color='red'>*".$errorMsg."</font></h5>";
			}
		?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->hiddenField($model,'id'); ?>
		<?php echo $form->hiddenField($model,'database_id'); ?>
		<?php //echo $form->hiddenField($model,'dataset_id'); ?>
		<input name="fields[type]" value="field" type="hidden">
		<?php
			if (isset($group)):
		?>
				<input name="fields[group]" id="J_group" value="<?php echo $group;?>" type="hidden">
		<?php
			endif;
		?>
		<?php
			if (isset($enName)):
		?>
				<input name="fields[old_en_name]" value="<?php echo $enName;?>" type="hidden">
		<?php
			endif;
		?>
		<?php
			if (isset($field['name'])):
		?>
				<input name="fields[old_name]" value="<?php echo $field['name'];?>" type="hidden">
		<?php
			endif;
		?>
		<?php
			if (isset($field['listorder'])):
		?>
				<input name="fields[listorder]" value="<?php echo $field['listorder'];?>" type="hidden">
		<?php
			endif;
		?>
	</div>

	<div class="row">
		<div class="span1"><label>名字：</label></div>
		<div class="span2">
			<input name="fields[name]" type="text" value="<?php if (isset($field['name'])) { echo $field['name']; }?>">
		</div>
	</div>
	<div class="row">
		<div class="span1"><label>英文标识：</label></div>
		<div class="span2">
			<input name="fields[en_name]" id="J_en_name" type="text" value="<?php if (isset($enName)) { echo $enName; }?>">
		</div>
	</div>

	<div class="row">
		<div class="span1"><label>字段属性</label></div>
	</div>

	<div class="row">
		<div class="span1"></div>
		<div class="span1">
			<label> <input name="fields[must]" value="1" type="checkbox" <?php if(isset($field['must']) && $field['must']==1){ echo "checked"; }?> > 必填项 </label>
		</div>
	</div>

	<div class="row" style="display:none">
		<div class="span1"></div>
		<label>
		<div class="span1"><label>筛选字段</label></div>
		<div class="span2">
		<select onChange="filterChange(this)" name="fields[extra][filter][type]" >
			<option value="0" <?php echo (isset($field['extra']['filter']['type']) && $field['extra']['filter']['type']==0) ? 'selected' : '';?> >非筛选字段</option>
			<option value="1" <?php echo (isset($field['extra']['filter']['type']) && $field['extra']['filter']['type']==1) ? 'selected' : '';?>>主要筛选字段</option>
			<?php if (false): ?>
			<option value="2" <?php echo (isset($field['extra']['filter']['type']) && $field['extra']['filter']['type']==2) ? 'selected' : '';?>>高级筛选字段</option>
			<?php endif; ?>
		</select>
		</label>
		<?php
		//echo $form->dropDownList($model, 'fields[0][filter]', array('非筛选字段', '主要筛选字段', '高级筛选字段'),
		//    array('onChange' => 'if (this.value!=0) {$("#filter_type").show();} else {$("#filter_type").hide();}',)
		//    );
		?>
	</div>
		<div class="span8 offset1" id="filter_type" <?php if(!isset($field['extra']['filter']['type']) || $field['extra']['filter']['type']==0) echo 'style="display:none;"';?> >

			<div class="span2">
				<input id="singleChangeLable" onClick="rangeChange(this)" value="0" type="radio" name="fields[extra][filter][filter_type][]" <?php if (isset($field['extra']['filter']['filter_type']) && in_array("0", $field['extra']['filter']['filter_type'])) {echo "checked";} ?> >
				<label for="singleChangeLable" style="display:inline;">单选</label>
			</div>
			<div class="span2">
				<input id="multiChangeLable" onClick="rangeChange(this)" value="1" type="radio" name="fields[extra][filter][filter_type][]" <?php if (isset($field['extra']['filter']['filter_type']) && in_array("1", $field['extra']['filter']['filter_type'])) {echo "checked";} ?> >
				<label for="multiChangeLable" style="display:inline;">多选</label>
			</div>

			<?php
				if (false):
			?>
			<div class="span3">
				<input id="rangeChangeLable" onClick="rangeChange(this)" value="2" type="checkbox" name="fields[extra][filter][filter_type][]" <?php if (isset($field['extra']['filter']['filter_type']) && in_array("2", $field['extra']['filter']['filter_type'])) {echo "checked";} ?> >
				<label for="rangeChangeLable" style="display:inline;">范围筛选</label>
			</div>
			<?php
				endif;
			?>
		</div>
	</div>

		<?php
			if (false):
		?>
		<div class="row offset1" id="range" <?php if(!isset($field['extra']['filter']['filter_type']) || !in_array("2", $field['extra']['filter']['filter_type'])) echo 'style="display:none;"';?> >
			<div class="span1">
				<?php
					//echo $form->dropDownList($model, 'name', array('整数', '1位小数', '2位小数', '不限位数'));
				?>
				<select name="fields[extra][filter][select_type]">
					<option value="0" <?php if(isset($field['extra']['filter']['select_type']) && "0"==$field['extra']['filter']['select_type']) echo "selected"; ?> >整数</option>
					<option value="1" <?php if(isset($field['extra']['filter']['select_type']) && "1"==$field['extra']['filter']['select_type']) echo "selected"; ?> >1位小数</option>
					<option value="2" <?php if(isset($field['extra']['filter']['select_type']) && "2"==$field['extra']['filter']['select_type']) echo "selected"; ?> >2位小数</option>
					<option value="3" <?php if(isset($field['extra']['filter']['select_type']) && "3"==$field['extra']['filter']['select_type']) echo "selected"; ?> >3位小数</option>
				</select>
			</div>
			<div class="span7 offset2">
				范围：&nbsp;&nbsp;从&nbsp;&nbsp;
				<input placeholder="0为不限制" class="J_num_input" name="fields[extra][filter][select_from]" type="text" value="<?php if(isset($field['extra']['filter']['select_from'])) echo $field['extra']['filter']['select_from']; ?>">
				<?php //echo $form->textField($model,'name', array('placeholder'=>'0为不限制')); ?>
				&nbsp;&nbsp;到
				<?php //echo $form->textField($model,'name', array('placeholder'=>'0为不限制')); ?>
				<input placeholder="0为不限制" class="J_num_input" name="fields[extra][filter][select_to]" type="text" value="<?php if(isset($field['extra']['filter']['select_to'])) echo $field['extra']['filter']['select_to']; ?>">
			</div>
		</div>
		<?php
			endif;
		?>

	<div class="row">
		<div class="span1"><label>字段类型<label></div>
	</div>
	<div class="row">

		<!-- 第一个Field区域 -->
		<div class="row">
		<div class="offset1">
			<select name="fields[extra][field_info][field_type]" onChange="fieldTypeChange()" id="fieldType" >
				<option value="">选择基本类型</option>
				<option value="normal" <?php if(isset($field['extra']['field_info']['field_type']) && $field['extra']['field_info']['field_type']=='normal') echo "selected"; ?>>普通字段</option>
				<option value="reference" <?php if(isset($field['extra']['field_info']['field_type']) && $field['extra']['field_info']['field_type']=='reference') echo "selected"; ?>>关联字段</option>
			</select>
		</div>
		</div>
		<!-- 第二个Field区域 -->
		<div id="secondField" <?php if(!isset($field['extra']['field_info']['field_type'])) echo 'style="display:none;"';?> class="row">
		<div class="offset1">
			<select name="fields[extra][field_info][addition_type]" onChange="additionTypeChange()" id="additionType" >
				<option value="">选择附属类型</option>
			</select>
		</div>
		</div>
	</div>

	<!-- 第三个Field区域 -->
	<div class="row">
	<div id="thirdField" style="display:none;" class="row">
	</div>
	</div>

	<div class="row"></div>

	<div class="row">
		<div class="span1">
			<?php echo CHtml::submitButton('完成'); ?>
		</div>
	</div>
<?php $this->endWidget();?>

<script type="text/javascript">
	//提交前验证
	$("#field-form").submit(function(e){
		var fieldType = $("#fieldType").val();
		var additionType = $("#additionType").val();
		var prevent = 0;
		if (fieldType=='' || additionType=='') {
			alert('请正确选择字段类型!');
			prevent = 1;
		} else {
			fieldType = parseInt(fieldType);
			additionType = parseInt(additionType);
			if (fieldType==0) {
				switch (additionType) {
					case 0:
						var field_0_0 = $("#field_0_0").val()=='' ? 0 : parseInt($("#field_0_0").val());
						if (field_0_0<=0 || isNaN(field_0_0)) {
							alert('请输入正确长度限制!');
							prevent = 1;
						}
						break;
					case 1:
						var field_0_1 = $("#field_0_1").val()=='' ? 0 : parseInt($("#field_0_1").val());
						if (field_0_1<=0 || isNaN(field_0_1)) {
							alert('请输入正确长度限制!');
							prevent = 1;
						}
						break;
					case 2:
						var field_0_2_from = $("#field_0_2_from").val()=='' ? 0 : parseFloat($("#field_0_2_from").val());
						var field_0_2_to = $("#field_0_2_to").val()=='' ? 0 : parseFloat($("#field_0_2_to").val());
						if (isNaN(field_0_2_from) || isNaN(field_0_2_to) || field_0_2_from>field_0_2_to) {
							alert('范围值填写错误!');
							prevent = 1;
						}
						if (field_0_2_from==0) {
							$("#field_0_2_from").val(0);
						}
						if (field_0_2_to==0) {
							$("#field_0_2_to").val(0);
						}
						break;
					//单选
					case 3:
						break;
					//多选
					case 4:
						break;
				}
			}
		}
		if (prevent==1) {
			e.preventDefault();
		}
	})

	$(this).keydown( function(e) {
		var key = window.event?e.keyCode:e.which;
		//alert(key.toString());
		if(key.toString() == "13"){
			return false;
		}
	});

	//num事件绑定
	function on_J_num_input_Change() {
		$(".J_num_input").unbind("change");
		$(".J_num_input").bind("change", function (){
			var num = parseInt($(this).val());
			if (num<0 || num>65535) {
				alert("数值超出所需范围:0~65535");
				$(this).val(0);
				}
		})
	}

	//change filter
	function  filterChange(obj) {
		if (obj.value!=0) {
			$("#filter_type").show();
		} else {
			$("#filter_type").hide();
		}
	}
	//change filter type
	function rangeChange(obj) {
		if (obj.checked == true && obj.value==2) {
			$("#range").show();
		} else {
			$("#range").hide();
		}
	}
	//change filed type
	function fieldTypeChange() {
		var additionType = typeof(arguments[0]) !='undefined' ? arguments[0] : -1;
		var databaseId = $("#CardDs_database_id").val();
		var datasetId = $("#CardDs_id").val();
		//var databaseId = 1;
		//var datasetId = 0;
		$.ajax({
			type : 'GET',
			url : '<?php echo $this->createUrl('AdditionType');?>',
			async: false,
			dataType : 'json',
			data : {'fieldType' : $("#fieldType").val(), 'additionType' : additionType, 'databaseId' : databaseId, 'datasetId' : datasetId},
			success : function(data) {
				//第二个Field区域 show
				//addition_type select html change
				//第三个Field区域 empty
				$("#secondField").show();
				$("#additionType").html(data.dropDown);
				$("#thirdField").empty();
			},
		})
	}
	//change addition type
	function additionTypeChange() {
		var isValue = arguments[0] ? arguments[0] : -1;
		var cardDsId = -1;
		var J_en_name = '';
		if (isValue == 1) {
			var cardDsId = $("#CardDs_id").val();
			var J_en_name = $("#J_en_name").val();
			var J_group = $("#J_group").val();
		}
		$.ajax({
			type : 'GET',
			url : '<?php echo $this->createUrl('AdditionField');?>',
			dataType : 'json',
			data : {'fieldType' : $("#fieldType").val(), 'additionType' : $("#additionType").val(), 'cardDsId' : cardDsId, 'enName' : J_en_name, 'group' : J_group},
			success : function(data) {
				//第三个Field区域 show
				//第三个Field区域 html change
				$("#thirdField").show();
				$("#thirdField").html(data.fieldHtml);
				on_J_num_input_Change();
			},
		})
	}

	<?php
		//恢复addtionType下拉框
		if(isset($field['extra']['field_info']['field_type'])) {
			$additionType = isset($field['extra']['field_info']['addition_type']) ? $field['extra']['field_info']['addition_type'] : -1;
			if ($additionType != -1) {
				echo "fieldTypeChange('".$additionType."');";
			} else {
				echo "fieldTypeChange();";
			}
		}

		//恢复thirdField区域Html
		if(isset($field['extra']['field_info']['addition_type'])) {
			$thirdField = isset($field['extra']['field_info']['addition_type']) ? $field['extra']['field_info']['addition_type'] : -1;
			if ($thirdField != -1) {
		     		echo "additionTypeChange(1);";
			} else {
		      		echo "additionTypeChange();";
			}
		}
	?>

	//给num增加事件绑定
	on_J_num_input_Change();

</script>
