<?php 
//获取用户信息

//session_start();
require("phpconnect.php");
$hbid=$_POST["hbid"];//红包id 6位数 
$openid=$_POST["openid"];



$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
if(!isset($openid) || strlen($openid)<3 || $openid=='')
{
	$ret='用户不存在';
}
if(!isset($hbid) || strlen($hbid)<3 || $hbid=='')
{
	$ret='红包不存在';
}
 
if($ret===true)
{

	$result_a = $con->query("SELECT  id,invitecode,hongbao FROM user where   openid='$openid' "  );
		
	if($result_a->num_rows>0)
	{ 
		$userdata =$result_a->fetch_array();
		$invitecode=$userdata['invitecode'];
		$hongbao_u=$userdata['hongbao'];

		$result_b = $con->query("SELECT  id,endtime,hongbao_sy,renshu_sy,status FROM flow_hongbao_kj where   hbid='$hbid' "  );
		
		if($result_b->num_rows>0)
		{ 
			$hongbaoitem =$result_b->fetch_array();
			$hongbao_ssy=round($hongbaoitem['hongbao_sy'],2);
			$renshu_sy=$hongbaoitem['renshu_sy'];
			if(strtotime($hongbaoitem['endtime'])-time()>0)
			{

				if($hongbao_ssy>0)
				{
					if($hongbaoitem['status']==1)
					{
						$result_bpd = $con->query("SELECT  id FROM flow_hongbao_qiang where   hbid='$hbid' and invitecode='$invitecode'"  );
						$mtpn=$result_bpd->num_rows;
						$result_bpd->free();
						if($mtpn==0)
						{ 
							$hongbao_qiang=getRandMoney($hongbao_ssy,$renshu_sy);
							if($hongbao_qiang>0)
							{
								
								$hongbao_sy=round($hongbao_ssy-$hongbao_qiang,2);
								$renshu_sy=$renshu_sy-1;
								if($renshu_sy<0)
								$renshu_sy=0;

		
								$hongbao_user=round($hongbao_qiang+$hongbao_u,2);
		
								//抢红包记录
								$tsql = "INSERT INTO flow_hongbao_qiang(invitecode,hbid,hongbao,hongbao_sy,renshu_sy,hongbao_user) VALUES ('$invitecode','$hbid','$hongbao_qiang' ,'$hongbao_sy' ,'$renshu_sy','$hongbao_user')";
								$con->query($tsql);
		
								//更新用户红包
								$updatesql = "UPDATE user SET hongbao= '$hongbao_user'  where invitecode='$invitecode'";
								$con->query($updatesql);

								//更新红包剩余
								$updatesql = "UPDATE flow_hongbao_kj SET hongbao_sy= '$hongbao_sy',renshu_sy='$renshu_sy' where hbid='$hbid'";
								$con->query($updatesql);

								$datalist->ishit=1;
								$datalist->errorcode=0;
								$datalist->content='抢到'.$hongbao_qiang.'元红包';

							}
							else
							{
								
								$datalist->errorcode=0;
								$datalist->content='红包已抢完';
							}
						}
						else
						{
							$datalist->message='祝福红包已领';
						}
							
					}
					else
					{
						$datalist->message='红包未支付';
					}
				}
				else
				{
					//$datalist->message='红包已抢完';
					$datalist->errorcode=0;
					$datalist->content='红包已抢完';
				}
			}
			else
			{
				$datalist->message='红包已过期';
			}
		}
		else
		{
			$datalist->message='红包不存在！';
		}
		$result_b->free();
		
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

	public $ishit=0;//1抢到，0没抢到
	public $content='红包已抢完';//抢到内容或者‘红包已抢完’
}

function getRandMoney($remainmoney,$remainsize)
{
	$min=0.01;
	$max=$remainmoney/$remainsize*2;
	$money=randomFloat()*$max;
	$money=$money<=$min?0.1:$money;
	if($money>$remainmoney)
	$money=$remainmoney;
	return round($money,2);
}

?>