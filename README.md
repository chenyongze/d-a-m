#m-p-a
/* Author: yongzechen */

<h2>卡牌库api接口说明</h2>
<h4>接口请求地址</h4>
<p><strong><em>测试Host</em></strong>：http://xxxx.com/api/card/   [hosts:192.168.1.39]<br />
<strong><em>正式Host</em></strong>：http://xxxx.com/api/card/</p>
<h4>所有请求返回数据类型均为JSON</h4>
<pre>
{
    code : 0 (0：成功，其他失败),
    data : 响应信息
}
</pre>
<h2>API列表</h2>
<h4>1 根据筛选条件获取卡牌列表</h4>
<ul>
<li>请求地址:/getitems</li>
<li>请求方式: get  
</li>
<li>请求参数: </li>
</ul>
<table>
    <tr>
        <td>参数</td>
		<td>是否为必填项</td>
		<td>含义</td>
		<td>示例</td>
    </tr>
	<tr>
        <td>setid</td>
		<td>true</td>
		<td>游戏表id</td>
		<td>1</td>
    </tr>
	<tr>
        <td>select</td>
		<td>flase</td>
		<td>返回字段逗号分隔</td>
		<td>name,f_technical</td>
    </tr>
	<tr>
        <td>filter</td>
		<td>false</td>
		<td>过滤条件</td>
		<td>name|狂戰神索迪::wpower|20:1000::profession|巡遊者</td>
    </tr>
	<tr>
        <td>regex</td>
		<td>false</td>
		<td>正则匹配</td>
		<td>name|狂戰</td>
    </tr>
	<tr>
        <td>order</td>
		<td>false</td>
		<td>排序，默认id正序</td>
		<td>id|0</td>
    </tr>
	<tr>
        <td>page</td>
		<td>false</td>
		<td>当前页码，默认1</td>
		<td>1</td>
    </tr>
	<tr>
        <td>size</td>
		<td>false</td>
		<td>每页数量,默认不分页</td>
		<td>20</td>
    </tr>
</table>
<ul>
<li>响应结果:</li>
</ul>
<pre>
{
  "code":0,
  "data":[
      {"id":9324,"data":{"name":"守護神謝爾","f_technical":"普通技-死亡之握"},"listorder":0},
      {"id":9325,"data":{"name":"狂戰神索迪","f_technical":"普通技-大地裂震"},"listorder":0},
      ...
  ],
  "pages":{"itemCount":43,"pageSize":2,"currPage":1}
}
</pre>


<h4>2 单条卡牌获取</h4>
<ul>
<li>请求地址:/getitem </li>
<li>请求方式: get  
</li>
<li>请求参数: </li>
</ul>
<table>
    <tr>
        <td>参数</td>
		<td>是否为必填项</td>
		<td>含义</td>
		<td>示例</td>
    </tr>
	<tr>
        <td>setid</td>
		<td>true</td>
		<td>游戏表id</td>
		<td>19</td>
    </tr>
	<tr>
    <td>id</td>
		<td>true</td>
		<td>itemid</td>
		<td>9326</td>
    </tr>
</table>
<ul>
<li>响应结果:</li>
</ul>
<pre> 
{
  code: 0,
  data: {
    id: 9326,
    data: {
          name: "狩獵神布蘭",
          character: "史詩",
          profession: "巡遊者",
          life: "3712",
          m_power: "371",
          w_power: "266",
          w_defense: "266",
          f_power: "0",
          f_defense: "328",
          strike: "120",
          treat: "0",
          parry: "40",
          hurt: "40",
          f_technical: "普通技-暴雨連射",
          f_technical_dec: "向前方扇形範圍內發射多枚箭矢，並對敵人造​​成暈眩效果。",
          t_technical: "普通技-疾風刺",
          t_technical_dec: "弓手準備3次連續強力射擊，對直線上的所有單位造成3次傷害，前兩次攻擊會擊退目標一段距離，第三次射擊會將目標擊飛。（當目標為玩家時，無擊退及擊飛效果。）",
          f_solder: "雪域獵魔場",
          f_solder_dec: "與雪域神曼達一同參戰，物理攻擊提高10%",
          t_solder: "神聖一擊",
          t_solder_dec: "與聖光領主羅斯一同參戰，生命上限提高5%",
          k_pic: ""
    },
    listorder: 0
  }
}
</pre>





<h4>3 获取选择框的候选项</h4>
<ul>
<li>请求地址:/getoptionlist </li>
<li>请求方式: get  
</li>
<li>请求参数: </li>
</ul>
<table>
    <tr>
        <td>参数</td>
		<td>是否为必填项</td>
		<td>含义</td>
		<td>示例</td>
    </tr>
	<tr>
    <td>setid</td>
		<td>true</td>
		<td>游戏表id</td>
		<td>19</td>
    </tr>
	<tr>
    <td>enname</td>
		<td>true</td>
		<td>字段英文名</td>
		<td>profession</td>
    </tr>
</table>
<ul>
<li>响应结果:</li>
</ul>
<pre>
{
    "code":0,
    "data":[
        "狂暴騎士",
        "巡遊者",
        "狙擊者",
        "冰語者",
        "火語者",
        "守護騎士",
        "聖殿祭祀",
        "戒律神官"
    ]
} 
</pre>
<h4>4 获取筛选头部[后期需要和前端拼接成html筛选头部]</h4>
<ul>
<li>请求地址:/gettables</li>
<li>请求方式: get  
</li>
<li>请求参数: </li>
</ul>
<table>
    <tr>
    <td>参数</td>
		<td>是否为必填项</td>
		<td>含义</td>
		<td>示例</td>
    </tr>
	<tr>
    <td>dbid</td>
		<td>true</td>
		<td>库id</td>
		<td>1</td>
    </tr>
    <tr>
    <td>setid</td>
		<td>true</td>
		<td>表id</td>
		<td>1</td>
    </tr>
</table>
<ul>
<li>响应结果:</li>
</ul>
<pre>
{
	code: 0,
	data: {
		list: {
			1: {
				id: 1,
				name: "英雄",
				en_name: "cardplate",
				listorder: 0
			},
			2: {
				id: 2,
				name: "物品",
				en_name: "goods",
				listorder: 0
			}
		},
		info: {
			m_power: {
				name: "魔法力",
				field_info: {
				field_type: "normal",
				addition_type: "number",
				num_type: "0",
				limit_from: "0",
				limit_to: "0"
				},
				listorder: 0
			}
		}
	}
}
</pre>
<h4>5 获取卡牌详细页面模板[html]</h4>
<ul>
<li>请求地址:/getitemhtml</li>
<li>请求方式: get  
</li>
<li>请求参数: </li>
</ul>
<table>
    <tr>
    <td>参数</td>
		<td>是否为必填项</td>
		<td>含义</td>
		<td>示例</td>
    </tr>
	<tr>
    <td>id</td>
		<td>true</td>
		<td>卡牌ID</td>
		<td>4</td>
    </tr>
    <tr>
    <td>type</td>
		<td>true</td>
		<td>模板类型</td>
		<td>1[1,代表pc 2,代表wap...]</td>
    </tr>
</table>
<ul>
<li>响应结果:</li>
</ul>
<pre>
<table><tbody><tr class="firstRow"><td width="187" valign="top" style="word-break: break-all;"><span style="background-color: rgb(84, 141, 212);">名称</span>：狩獵神布蘭</td><td width="187" valign="top" style="word-break: break-all;"><span style="background-color: rgb(217, 150, 148);"><strong>卡牌技能</strong></span>1：普通技-暴雨連射</td><td width="187" valign="top" style="word-break: break-all;"><span style="background-color: rgb(0, 176, 240);">卡牌技能2</span>：普通技-疾風刺</td><td width="187" valign="top" style="word-break: break-all;">2015-06-19</td></tr><tr><td width="187" valign="top" style="word-break: break-all;"><span style="background-color: rgb(255, 0, 0);">卡牌</span>：<img src='http://pic1.mofang.com/185/162/0a0dd636e1a9065964a5a90b98f7619600568d39.jpg'></td><td width="187" valign="top" style="word-break: break-all;"><span style="background-color: rgb(118, 146, 60);">卡牌描述1</span>：<span style="text-decoration: underline;">向前方扇形範圍內發射多枚箭矢，並對敵人造​​成暈眩效果。</span></td><td width="187" valign="top" style="word-break: break-all;"><span style="background-color: rgb(255, 255, 0);">卡牌描述2</span>：弓手準備3次連續強力射擊，對直線上的所有單位造成3次傷害，前兩次攻擊會擊退目標一段距離，第三次射擊會將目標擊飛。（當目標為玩家時，無擊退及擊飛效果。）</td><td width="187" valign="top" style="word-break: break-all;"><br/></td></tr><tr><td width="187" valign="top" style="word-break: break-all;"><span style="background-color: rgb(112, 48, 160);">治疗</span>：1000</td><td width="187" valign="top"><br/></td><td width="187" valign="top"><br/></td><td width="187" valign="top"><br/></td></tr></tbody></table><p><br/></p>
</pre>

<h4>6 获取列表也需要绑定字段</h4>
<ul>
<li>请求地址:/getselectfields</li>
<li>请求方式: get  
</li>
<li>请求参数: </li>
</ul>
<table>
    <tr>
    <td>参数</td>
		<td>是否为必填项</td>
		<td>含义</td>
		<td>示例</td>
    </tr>
	<tr>
    <td>enname</td>
		<td>true</td>
		<td>游戏表英文名</td>
		<td>cardplate</td>
    </tr>
</table>
<ul>
<li>响应结果:</li>
</ul>
<pre>
{
	code: 0,
	data: {
		name: "卡牌名称",
		character: "品质",
		profession: "职业",
		w_defense: "物防"
	}
}
</pre>
</body>
</html>
<!-- This document was created with MarkdownPad, the Markdown editor for Windows (http://markdownpad.com) -->
