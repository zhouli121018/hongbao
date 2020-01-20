<?php 
//获取用户信息

//session_start();
require("phpconnect.php");
//include 'phpqrcode.php'; 
 

$hbid=$_POST["hbid"];//红包id 6位数
$openid=$_POST["openid"];//
 
if(isEmptyStr($openid) )
$ret='帐号不能为空';
if(isEmptyStr($hbid) )
$ret='红包ID不能为空';
 
 
$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
//$ret=true;
if($ret===true)
{

	$result_a = $con->query("SELECT  id,invitecode,hongbao FROM user where   openid='$openid' "  );
		
	if($result_a->num_rows>0)
	{ 
		//$userdata =$result_a->fetch_array();
		//$invitecode=$userdata['invitecode'];

		$result_p = $con->query("SELECT voicetime,barcode,invitecode,hongbao,zhufutitle,hengfu,shanglian,xialian FROM flow_hongbao_kj where   hbid='$hbid' "  );
	 
		if($result_p->num_rows>0)
		{
			$datalist->errorcode=0;

			  
			$userdata =$result_p->fetch_array();
			 
			$invitecode=$userdata['invitecode'];

			$datalist->hongbao=$userdata['hongbao'];
			$datalist->zhufutitle=$userdata['zhufutitle'];
			$datalist->hengfu=$userdata['hengfu'];
			$datalist->shanglian=$userdata['shanglian'];
			$datalist->xialian=$userdata['xialian'];
			$datalist->barcode=$userdata['barcode'];
			$datalist->voicetime=$userdata['voicetime'];
			
			$result_am = $con->query("SELECT  headimgurl FROM user where   invitecode='$invitecode' "  );
			if($result_am->num_rows>0)
			{
				$userdatam =$result_am->fetch_array();
				$datalist->headimg=$userdatam['headimgurl'];
			}
			$result_am->free();

		}	 
		else
		{
			$datalist->message='红包不存在！';
		}

		$result_p->free();
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
 

	public $headimg='uicon/default.png';//用户头像
	public $hongbao='9.2';
	public $zhufutitle='祝您2020年幸福安康';//祝福语
	public $hengfu='万事如意';// 
	public $shanglian='年年如意发大财';//上联
	public $xialian='岁岁平安行大运';//下联
	public $barcode='barcode/barcode.png';//抢红包二维码
	public $voicetime=0;
	 
}

?>