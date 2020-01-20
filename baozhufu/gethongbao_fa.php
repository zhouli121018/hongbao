<?php 
//获取我发的红包

//session_start();
require("phpconnect.php");
//include 'phpqrcode.php'; 
 

 
$openid=$_POST["openid"];//
 
if(isEmptyStr($openid) )
$ret='帐号不能为空';
 
 
 
$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
//$ret=true;
if($ret===true)
{

	$result_a = $con->query("SELECT  id,invitecode,hongbao FROM user where   openid='$openid' "  );
		
	if($result_a->num_rows>0)
	{ 
		$datalist->errorcode=0;
		$userdata =$result_a->fetch_array();
		$invitecode=$userdata['invitecode'];
		$datalist->hongbao=$userdata['hongbao'];
		$result_al = $con->query("SELECT hbid, createtime,hongbao,renshu,renshu_sy,endtime,istui FROM flow_hongbao_kj where  status=1 and invitecode='$invitecode' order by id desc  "  );
	 
		while($row_cl = $result_al->fetch_assoc())
		{
			$dt = new DataItem ;
			 
			$dt->createtime=substr(date('Y年m月d日 H:i',strtotime($row_cl['createtime'])),2);//;

			$dt->hongbao=$row_cl['hongbao'];
			$dt->hbid=$row_cl['hbid'];
			$renshu=$row_cl['renshu'];
			$renshu_sy=$row_cl['renshu_sy'];
			$endtime=$row_cl['endtime'];
			$istui=$row_cl['istui'];


			if($renshu_sy==0)
			$dt->status=$renshu.'/'.$renshu.'已领完';
			else
			{
				$dt->status=($renshu-$renshu_sy).'/'.$renshu.'领取中';
				if(strtotime($endtime)<time() && $istui==1)
				{
					$dt->status=($renshu-$renshu_sy).'/'.$renshu.'未领完【金额已退到微信钱包】';
				}

			}
			
			$datalist->list[]=$dt;



		}
		$result_al->free();
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
	public $errorcode=0;//0表示正常
	public $message='';
	public $hongbao='11.11';//红包余额


	public $list=array();//祝福标题列表
 
 
}
class DataItem
{
	
	public $createtime='';//时间
	public $hongbao='';//红包
	public $status='1/10领取中';
	public $hbid='';//


}

?>