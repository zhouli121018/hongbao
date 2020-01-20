<?php 
//获取大家手气

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

		$result_p = $con->query("SELECT   invitecode,hongbao,hongbao_sy FROM flow_hongbao_kj where   hbid='$hbid' "  );
	 
		if($result_p->num_rows>0)
		{
			$datalist->errorcode=0;
 
			$userdata =$result_p->fetch_array();
			$datalist->hongbao=$userdata['hongbao'];
			$invitecode=$userdata['invitecode'];
			$datalist->hongbao_weiling=$userdata['hongbao_sy'];
			$result_am = $con->query("SELECT  headimgurl FROM user where   invitecode='$invitecode' "  );
			if($result_am->num_rows>0)
			{
				$userdatam =$result_am->fetch_array();
				$datalist->headimg=$userdatam['headimgurl'];
			}
			$result_am->free();

			$result_al =  $con->query("SELECT createtime ,hongbao,invitecode FROM flow_hongbao_qiang where hbid='$hbid'  order by id desc limit 40"  );
			while($row_cl = $result_al->fetch_assoc())
			{
				$dt = new DataItem ;

				$dt->createtime=$row_cl['createtime'];
				$dt->hongbao=$row_cl['hongbao'];
				$invitecode_u=$row_cl['invitecode'];
				$result_am = $con->query("SELECT  headimgurl,username FROM user where   invitecode='$invitecode_u' "  );
				if($result_am->num_rows>0)
				{
					$userdatam =$result_am->fetch_array();
					$dt->headimg=$userdatam['headimgurl'];
					$dt->username=$userdatam['username'];
				}
				$result_am->free();
				$datalist->list[]=$dt;

			}

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
	public $errorcode=0;//0表示正常
	public $message='';
	public $hongbao='11.11';//红包
    public $hongbao_weiling='11.11';//未领
    public $headimg='icon/xxx.jpg';//头像


	public $list=array();//祝福标题列表
 
 
}
class DataItem
{
	
	public $createtime='';//时间
	public $hongbao='';//红包
	public $username='';//用户名称
	public $headimg='';//用户头像

}

?>