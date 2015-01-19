<?php

/**
 * 数据库逻辑类
 * @author wli
 */
class MFLogic {
    
    /**
     * ajax返回信息
     */
    public $ajaxInfo = array();
    
    
    /**
     * 初始化操作
     */
    public function __construct() {}
    
	/**
	* 左侧栏树形结构
	* @author gentle
	*/
	public function dataTree() {
		$data = array();
		$databases = CardDb::model()->findAll();
		foreach ($databases as $key => $value) {
			$data[$key]['text'] = '<span>'.$value->name.'</span>';
			$data[$key]['expanded'] = false;
			$data[$key]['children'] = array();
			$datasets = CardDs::model()->findAllByAttributes(array('database_id'=>(string)$value->id));
			if (empty($datasets)) {
				continue;
			}
			foreach($datasets as $k => $v) {
				$data[$key]['children'][$k] = array('text' => '<a href="'.$this->createUrl('/CardDs/Index/id/'.$value->id).'">'.$v->name.'</a>');
			}
		}
		return $data;
	}
	    
	    /**
     * 更新逻辑
     */
    /*
    public function updateLogic($libao) {
        $exists = CouponOrder::model()->exists(
            'user_id = :user_id AND coupon_id = :coupon_id AND order_type = :order_type', 
             array(':user_id' => $this->userId, ':coupon_id' => $libao->id, ':order_type' => 'receive',
        ));
        if ($exists) {
            $this->ajaxInfo = array(
                'msgImg' => 'wran',
                'msgCont' => '您已经领取了此礼包！',
                'msgInfo' => '',
            );
            return;
        } else if ($this->premiseRule($libao)==-1){
            return;
        }
        // 增加领号
        $code = CouponCode::model()->find(array(
            'condition' => 'coupon_id = :coupon_id AND is_used = :is_used',
            'params' => array(':coupon_id' => $libao->id, ':is_used' => 0),
            'order' => 'id asc',
            'limit' => 1,
        ));
       
        if ($code === null) {
            $this->ajaxInfo = array(
                'msgImg' => 'wran',
                'msgCont' => '礼包已领取完毕！',
                'msgInfo' => '',
            );
            return;
        } else {
            $code->is_used = 1;
            $code->save();
        }
        
        $order = new CouponOrder;
        $order->user_id = $this->userId;
        $order->user_name = $this->userName;
        $order->coupon_id = $libao->id;
        $order->code_id = $code->id;
        $order->code_name = $code->name;
        
        if ($order->save()) {
            Coupon::model()->updateCounters(array('receive_quantity' => 1), 'id = ' . $libao->id);
            $this->ajaxInfo = array(
                'msgImg' => 'ok',
                'msgCont' => '您领取的礼包码:',
                'msgInfo' => '<div class="gethao clearfix">
                                    <input class="haotext" id="haotext" value="' . $code->name.'" type="text"/>
                                    <a class="cloneHao" id="cloneHao" href="javascript:void(0)">复制</a>
                                </div>
                                <div class="no-hao">
                                    您的礼包已保存到用户中心的存号箱<a class="changeHao" href="http://u.mofang.com/usercenter/trade">前往查看>></a>
                                </div>',
            );
        }
        return;
    }
    */
    
}
