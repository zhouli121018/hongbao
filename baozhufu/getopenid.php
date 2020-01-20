<?php 
//获取用户信息

//session_start();
//require("phpconnect.php");
//include 'phpqrcode.php'; 
$code=$_GET["code"];//微信授权返回的code

  if(isset($code)&&strlen($code)>4 )//授权过来
{
	//AppID：wx039d71c6949b4804
	$appid="wx13665daee96a688b";//广州小蚯
	$secret="b3e2127e043848a38eda12952dfc9147";//
	
	//file_put_contents("222.txt", 'step1', FILE_APPEND);

	$ch = curl_init("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
	$output = curl_exec($ch) ;
	//file_put_contents( "11.txt",$output);
	//$exposurelist->message=$output;
	curl_close($ch);
	//echo $output;

	/*	
	{"access_token":"28_d_8nr8AKkN-rMme90Ycg_o1StKdwCOuyaU9cnJqYrY8O8pIsEb_9TAFNYMLSdVdRZciz7jeMuy8BsoKoZXj1l60LVn9IymP7xxfEzhMIQ7o",
		"expires_in":7200,"refresh_token":"28_TpFX-EHHy9NeiJQDZdiPx2D5_ihHMC-HzD501o7GMZBslMDhqchkXdbl2TWTEAOjlQ5UKYiGE4ubZSJyER8suuW1C3JnzH0x8uMUMEH4BLA",
		"openid":"oHmFm6BOF4okrdNKvjk2BXmKritM","scope":"snsapi_userinfo"}*/
	//	echo $output;
	$tokenjs=json_decode($output);
	echo $tokenjs->openid;
	 
}
 
 
//列表
class Datalist 
{
	public $errorcode=1;//0表示正常
	public $message='';

	public $hbid='22';//红包id
	public $headimg='uicon/default.png';//用户头像
	public $myhongbao='12';
	public $hongbao='9.2';
	public $serverid='';//获取录音需要的id
	public $zhufutitle='祝您2020年幸福安康';//祝福语
	public $hengfu='万事如意';// 
	public $shanglian='年年如意发大财';//上联
	public $xialian='岁岁平安行大运';//下联
	public $gongzhonghao='gongzhonghao.jpg';//公众号二维码
	public $voicetime=0;
	public $openid='';//返回的openid，有的话需要保存起来。

	 
}

?>