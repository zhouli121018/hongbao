<?php 
//获取用户信息

//session_start();
require("phpconnect.php");
//$hbid=$_POST["hbid"];//红包id 6位数 
$openid=$_POST["openid"];

$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
if(!isset($openid) || strlen($openid)<3 || $openid=='')
{
	$ret='用户不存在';
}

if($ret===true)
{

	$result_a = $con->query("SELECT  id,username,headimgurl,hongbao FROM user where   openid='$openid' "  );
		
	if($result_a->num_rows>0)
	{ 
		$userdata =$result_a->fetch_array();
		$invitecode=$userdata['invitecode'];
		$hongbao=$userdata['hongbao'];

		$datalist->money=round($hongbao,2);
		$datalist->username=$userdata['username'];
		$datalist->headimg=$userdata['headimgurl'];
		
	}
	else
	{
		$datalist->message='用户不存在！';
	}
	$result_a->free();

}
else
{
$datalist->message=$ret;
}
$con->close();
$persiondata = json_encode($datalist);
  
echo $persiondata ;
 
 
//列表
class Datalist 
{
	public $errorcode=1;//0表示正常
	public $message='';

	public $money=0;//总金额
	public $username='';//昵称
	public $headimg='';//头像
}
 

?>