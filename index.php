<?php
include "wechat.class.php";
$appid = "wxee382921b24b4157";
$appsecret = "c30eec67b4a9d164ded24293ccd94eec";
$token = "weixin";
$options = array(
    'token'=>'weixin', //填写你设定的key
    'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey，如接口为明文模式可忽略
    'appid' => 'wxee382921b24b4157', //填写高级调用功能的app id
    'appsecret' => 'c30eec67b4a9d164ded24293ccd94eec' //填写高级调用功能的密钥
);
$weObj = new Wechat($options);
$weObj->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
$Rev = $weObj->getRev();
$type = $Rev->getRevType();
if($type == Wechat::MSGTYPE_TEXT) {
    $weObj->text("Hello My Heart!")->reply();
}else if($type == Wechat::MSGTYPE_VOICE){
    if("删除。" == trim($weObj->getRevContent())){
        $db = mysqli_connect(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB);
        $sql = "select max(id) from weixin_test";
        $data = mysqli_query($db, $sql);
        $row = $data->fetch_array();
        if (mysqli_query($db, "delete from weixin_test where id = $row[0]") == 1) {
            $weObj->text("删除成功！")->reply();
        } else {
            $weObj->text("删除失败！")->reply();
        }
    }else{
        $weObj->text($weObj->getRevContent())->reply();
    }
}else if($type == Wechat::MSGTYPE_EVENT){
	$eventype = $Rev->getRevEvent();
	if($eventype['event'] == Wechat::EVENT_SUBSCRIBE){
		//$openid可以正常获取
		$openid = $Rev->getRevFrom();
		$arr_json = $weObj->getUserInfo($openid);
		$name = $arr_json["nickname"];
		//$name为空，显示不出来
		$weObj->text("欢迎".$name ."订阅牛毅的公众号:)")->reply();
	}	
}else{
    $weObj->text("还没有该功能，sorry")->reply();
}
//获取菜单操作:
$menu = $weObj->getMenu();
//设置菜单
$newmenu = array(
    "button" =>
        array(
            array('type' => 'view', 'name' => '合聚官网', 'url' => 'http://www.sxheju.cn/'),
            array('type' => 'view', 'name' => '合聚商城', 'url' => 'http://www.hjwsc.cn/'),
            array('type' => 'view', 'name' => '我的业务', 'url' => 'http://niuyi00.applinzi.com/newindex.html'),
        ),
);
$result = $weObj->createMenu($newmenu);
