<?php
//二维数据排序
function listorder_cmp($a, $b) {
	if ($a==$b) {
		return 0;
	}
	return ($a>$b) ? -1 : 1;
}


function debug($var, $color = 'black')
{
    echo "<pre style=\"font-family:Consolas,Calibri,'Microsoft Yahei','微软雅黑',Tahoma,Arial,Helvetica,STHeiti;color:$color;font-size:14px;\">";
    if (empty($var))
        {
        var_dump($var);
        }
        else
	{
		print_r($var);
	}

	echo "<br/><br/>";
	echo "</pre>";
}

