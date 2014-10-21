<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
    <div class="container">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
     
          <!-- Be sure to leave the brand out there if you want it shown -->
          <a class="brand" href="/">魔方<small> 卡牌数据库beta</small></a>
          
          <div class="nav-collapse">
			<?php $this->widget('zii.widgets.CMenu',array(
			    'htmlOptions'=>array('class'=>'pull-right nav'),
			    'submenuHtmlOptions'=>array('class'=>'dropdown-menu'),
			    'itemCssClass'=>'item-test',
			    'encodeLabel'=>false,
			    'items'=>array(
				array('label'=>'首页', 'url'=>array('/CardDb/index')),
				/*array('label'=>'Gii generated', 'url'=>array('customer/index')),*/
				array('label'=>'登陆', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'退出 ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
			    ),
                ));	?>
    	</div>
    </div>
	</div>
</div>
