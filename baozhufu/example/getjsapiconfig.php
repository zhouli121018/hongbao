<?php 
//获取用户信息
require("phpconnect.php");
 
require_once "jssdk.php";
 
 
 
$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
//$ret=true;
if($ret===true)
{
	  
	$datalist->errorcode=0;//0表示正常
	$jssdk = new JSSDK("wx13665daee96a688b", "b3e2127e043848a38eda12952dfc9147");
	$signPackage = $jssdk->GetSignPackage();

	$datalist->timestamp=$signPackage["timestamp"];//用户头像
	$datalist->noncestr=$signPackage["nonceStr"];
	$datalist->signature=$signPackage["signature"];

}
else
{
$datalist->message=$ret;
}
$con->close();
$persiondata = json_encode($datalist);
 
//file_put_contents($openid."-.txttt",$persiondata." \r\n", FILE_APPEND);
echo $persiondata ;
 
//列表
class Datalist 
{
	public $errorcode=1;//0表示正常
	public $message='';

	public $appid='wx13665daee96a688b';//红包id
	public $timestamp='';//用户头像
	public $noncestr='';
	public $signature='';
	 
}

?>