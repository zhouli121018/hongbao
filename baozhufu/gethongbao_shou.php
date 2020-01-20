<?php 
//获取我收的红包

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
		$result_al = $con->query("SELECT createtime,hongbao,hbid FROM flow_hongbao_qiang where   invitecode='$invitecode' order by id desc  "  );
	 
		while($row_cl = $result_al->fetch_assoc())
		{
			$dt = new DataItem ;
			$dt->createtime=$row_cl['createtime'];//;
			$dt->hongbao=$row_cl['hongbao'];
			$hbid=$row_cl['hbid'];
			$result_att = $con->query("SELECT invitecode FROM flow_hongbao_kj where   hbid='$hbid'  "  );
			$hbf =$result_att->fetch_array();
			$invitecode=$hbf['invitecode'];
			
			$result_am = $con->query("SELECT  headimgurl,username FROM user where   invitecode='$invitecode' "  );
			if($result_am->num_rows>0)
			{
				$userdatam =$result_am->fetch_array();
				$dt->headimg=$userdatam['headimgurl'];
				$dt->username=$userdatam['username'];
			}
			$result_am->free();
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
	public $headimg='uicon/aa.jpg';
    public $username='昵称';



}

?>