<?php 
//获取提款列表

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
		 
		$result_al = $con->query("SELECT createtime,money ,status FROM flow_hongbao_tikuan where   invitecode='$invitecode' order by id desc  "  );
	 
		while($row_cl = $result_al->fetch_assoc())
		{
			$dt = new DataItem ;
			$dt->createtime=$row_cl['createtime'];//;
			$dt->hongbao=$row_cl['money'];
			$status=$row_cl['status'];
			if($status==0)
			$dt->status='审核中';
			else if($status==1)
			$dt->status='已提款到微信钱包';
 
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
	public $errorcode=1;//0表示正常
	public $message='默认错误';
	 
	public $list=array();//祝福标题列表
 
 
}
class DataItem
{
	
	public $createtime='';//时间
	public $hongbao='';//红包
    public $status='状态';
}

?>