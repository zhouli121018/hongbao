<?php 
//获取用户信息

//session_start();
require("phpconnect.php");
include 'phpqrcode.php'; 
//require_once('aop/AopClient.php');
//require_once('aop/request/AlipayTradeWapPayRequest.php');

$openid=$_POST["openid"];// 
$title=$_POST["title"];//标题
$hengfu=$_POST["hengfu"];//横幅 
$shanglian=$_POST["shanglian"];//上联
$xialian=$_POST["xialian"];//下联
$sumcash=$_POST["sumcash"];
$hbnum=$_POST["hbnum"];
$serverid=$_POST["serverid"];//
$voicetime=$_POST["voicetime"];//

// $openid='o_z2AwLZyJNoCVWLJngoMrXk-Yw0';
// $title='title';
// $hengfu='hengfu';
// $shanglian='shanglian';
// $xialian='xialian';
// $sumcash='11';
// $hbnum='2';
// $serverid='2';
// $voicetime='9';


$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
if(!isset($openid) || strlen($openid)<3 || $openid=='')
{
	$ret='用户不存在';
}
 

 
if(isEmptyStr($title) )
$ret='祝福语不能为空';
if(isEmptyStr($hengfu) )
$ret='横批不能为空';
if(isEmptyStr($shanglian) )
$ret='上联不能为空';
if(isEmptyStr($xialian) )
$ret='下联不能为空';
if(isEmptyStr($xialian) )
$ret='下联不能为空';
if(isEmptyStr($sumcash) )
$ret='红包金额不能为空';
if(isEmptyStr($hbnum) )
$ret='人数不能为空';
if(isEmptyStr($voicetime) )
$ret='祝福录音不能为空';

//$ret=true;
if($ret===true)
{
	$sumcash=round(floatval($sumcash),2);

	if($sumcash>200 )
	{
		$ret='红包金额不能超200元';
	}
	if($sumcash/$hbnum<0.5 )
	{
		$ret='人均金额不能小于0.5元';
	}
}


if($ret===true)
{
	$total_fee=$sumcash*100;
	$result_a = $con->query("SELECT  id,invitecode,hongbao FROM user where   openid='$openid' "  );
		
	if($result_a->num_rows>0)
	{ 
		$userdata =$result_a->fetch_array();
		$invitecode=$userdata['invitecode'];
		$userid=$userdata['id'];

		$ivcode=genInviteCode(6);
		$result_aba =  $con->query("SELECT id  FROM flow_hongbao_kj where hbid='$ivcode'  "  );
		while($result_aba->num_rows>0)
		{
			$ivcode=genInviteCode(6);
			$result_aba =  $con->query("SELECT id  FROM flow_hongbao_kj where hbid='$ivcode'  "  );
		}
		$endtime=date('Y-m-d H:i:s',strtotime('+7 days'));


		//openhongbao.html?state=pid,hbid&code=xxx& pid=xxx&hbid=xx
 
		$tinyurl='http://www.baozhufu.com/baozhufu/openhongbao.html?pid='.$invitecode.'&hbid='.$ivcode;

		//$tinyurl=$html;	
		//$value = $tinyurl; //二维码内容 
		$errorCorrectionLevel = 'L';//容错级别 
		$matrixPointSize = 6;//生成图片大小 
		//生成二维码图片 
		$barcodepath='barcode/' . $ivcode . '.png';
		QRcode::png($tinyurl, $barcodepath, $errorCorrectionLevel, $matrixPointSize, 2);

		$out_trade_no=$userid . '-' . time().'-'.rand(100,999);
		$tsql = "INSERT INTO flow_hongbao_kj(trade_no,tinyurl,voicetime,barcode,hbid,invitecode,hongbao,endtime,hongbao_sy,renshu,renshu_sy,serverid,zhufutitle,hengfu,shanglian,xialian) VALUES ('$out_trade_no','$tinyurl','$voicetime','$barcodepath','$ivcode','$invitecode','$sumcash' ,'$endtime' ,'$sumcash','$hbnum','$hbnum','$serverid','$title','$hengfu','$shanglian','$xialian')";
		$con->query($tsql);
		if($con->affected_rows>0)
		{

			$md_key='9934e7d25453e97507ef794cf7b0529e';

			$datalist->errorcode=0;
			$datalist->hbid=$ivcode;
			
			$nonce_str = md5(rand());
			//$total_fee='';
			//http://118.31.109.150/phpMyAdmin/sql.php?server=1&db=chuanyin&table=flow_hongbao_kj&pos=0
			$notifyurl="http://118.31.109.150/baozhufu/notify_url.php";
			$body='传音包祝福红包-送亲朋好友';
			$detail='传音包祝福红包-送亲朋好友';
			$clientip=$_SERVER['REMOTE_ADDR'];
			$stringA="appid=wx13665daee96a688b&body=$body&detail=$detail&mch_id=1573874181&nonce_str=$nonce_str&notify_url=$notifyurl&openid=$openid&out_trade_no=$out_trade_no&spbill_create_ip=$clientip&total_fee=$total_fee&trade_type=JSAPI";
			$stringSignTemp=$stringA."&key=".$md_key;
			$sign=strtoupper(MD5($stringSignTemp));
			
			$postdata="<xml>
			<appid>wx13665daee96a688b</appid>
			<body>$body</body>
			<detail>$detail</detail>
			<mch_id>1573874181</mch_id>
			<nonce_str>$nonce_str</nonce_str>
			<notify_url>$notifyurl</notify_url>
			<openid>$openid</openid>
			<out_trade_no>$out_trade_no</out_trade_no>
			<spbill_create_ip>$clientip</spbill_create_ip>
			<total_fee>$total_fee</total_fee>
			<trade_type>JSAPI</trade_type>
			<sign>$sign</sign>
			</xml>";
			
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder");
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$file_contents = curl_exec($ch);
			curl_close($ch);
			//echo $file_contents."<br />";
			$xml = simplexml_load_string($file_contents);
			$return_code =(string)$xml->return_code;
			if($return_code=="SUCCESS")
			{
				$result_code =(string)$xml->result_code;
				if($result_code=="SUCCESS")//这里应该验证服务器过来的签名
				{
					//$exposurelist->errorcode=0;//
					//$datalist->appid=(string)$xml->appid;
					//$datalist->mch_id=(string)$xml->mch_id;
					$prepay_id=(string)$xml->prepay_id;
					//$datalist->prepay_id=(string)$xml->prepay_id;
					//$datalist->package="Sign=WXPay";
					$datalist->package="prepay_id=".$prepay_id;
					$package="prepay_id=".$prepay_id;
					$datalist->nonce_str = md5(rand());//(string)$xml->nonce_str;
					$datalist->timestamp="".time();
					$stringB="appId=wx13665daee96a688b&nonceStr=".$datalist->nonce_str."&package=".$package."&signType=MD5&timeStamp=".$datalist->timestamp;
					$stringSignTempB=$stringB."&key=".$md_key;
						//$exposurelist->message=$stringB;
					$sign=strtoupper(MD5($stringSignTempB));
					$datalist->paysign=$sign;

				}
			}
		}
		else
		{
			$datalist->message='DB Error！';
		}
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
 
//file_put_contents($openid."-.txttt",$persiondata." \r\n", FILE_APPEND);
echo $persiondata ;
 
 
//列表
class Datalist 
{
	public $errorcode=1;//0表示正常
	public $message='';

	public $hbid=0;//红包id
	// public $package='';//wx.WeixinJSBridge 使用
	// public $nonce_str='';//wx.chooseWXPay 使用
	// public $timestamp='';//wx.chooseWXPay 使用
	// public $paysign='';//wx.chooseWXPay 使用

	public $appid='wx13665daee96a688b';//WeixinJSBridge.invoke 使用
	public $package='';//WeixinJSBridge.invoke 使用
	public $nonce_str='';//WeixinJSBridge.invoke 使用
	public $timestamp='';//WeixinJSBridge.invoke 使用
	public $paysign='';//WeixinJSBridge.invoke 使用
	public $signType='MD5';
	 

	 
}
 

?>