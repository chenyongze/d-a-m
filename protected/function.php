<?php
//二维数据排序
function listorder_cmp($a, $b) {
	if ($a==$b) {
		return 0;
	}
	return ($a>$b) ? -1 : 1;
}
