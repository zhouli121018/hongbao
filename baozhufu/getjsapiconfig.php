<?php 
//获取用户信息
require("phpconnect.php");
 
require_once "jssdk.php";
 
$url=$_POST["url"];
//$url='http://www.baozhufu.com/baozhufu/loadhongbao.html';
$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
//$ret=true;
if($ret===true)
{
	//file_put_contents("url.txt",  file_get_contents('php://input')."-", FILE_APPEND);
	$datalist->errorcode=0;//0表示正常
	$jssdk = new JSSDK("wx13665daee96a688b", "b3e2127e043848a38eda12952dfc9147",$url);
	$signPackage = $jssdk->GetSignPackage();

	$datalist->timestamp=$signPackage["timestamp"];//用户头像
	$datalist->noncestr=$signPackage["nonceStr"];
	$datalist->signature=$signPackage["signature"];
	//$datalist->url=$signPackage["url"];
	//$datalist->rawString=$signPackage["rawString"];

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
	//public $url='';
	//public $rawString="";
	
	 
}

?>