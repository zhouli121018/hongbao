<?php 
//提款

//session_start();
require("phpconnect.php");
//include 'phpqrcode.php'; 
 

 
$openid=$_POST["openid"];//
$hongbao=$_POST["hongbao"];//

 


if(isEmptyStr($openid) )
$ret='帐号不能为空';
 
if(isEmptyStr($hongbao) )
$ret='红包不能为空';

$hongbao=round(floatval($hongbao),2);

$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
//$ret=true;

if($ret===true)
{
	if($hongbao<0.3)
	$ret='提现金额最低0.3元';

	if($hongbao>200)
	$ret='提现金额最高200元';
}
if($ret===true)
{

	$result_a = $con->query("SELECT  id,invitecode,hongbao FROM user where   openid='$openid' "  );
		
	if($result_a->num_rows>0)
	{ 
		
		$userdata =$result_a->fetch_array();
		$invitecode=$userdata['invitecode'];
		$datalist->hongbao=$userdata['hongbao'];
		if($datalist->hongbao>=$hongbao)
		{
			$date_start=date('Y-m-d').' 00:00:00';

			$result_aq = $con->query("SELECT  id ,money FROM flow_hongbao_tikuan where   invitecode='$invitecode' and createtime > '$date_start' "  );
			if($result_aq->num_rows<10)
			{ 
				$sum_money=0;
				while($row_cl = $result_aq->fetch_assoc())
				{
					$sum_money += floatval($row_cl['money']);
				}
				if($sum_money+$hongbao>1000)
				{
					$datalist->message='您今日红包提现请求累计超过1000元，明日再提。';
				}
				else
				{
					$datalist->errorcode=0;
					$datalist->message='';
					$tsql = "INSERT INTO flow_hongbao_tikuan(invitecode,money) VALUES ('$invitecode','$hongbao')";
					$con->query($tsql);
					if($con->affected_rows>0)
					{
						$newhb=round($datalist->hongbao-$hongbao,2);
						$datalist->hongbao=$newhb;
						$con->query(" update user set hongbao='$newhb' where   openid='$openid' "  );
						if($con->affected_rows>0)
						{
							$datalist->message='提现申请成功，处理需要1-5个工作日';
						}
					}
					else
					{
						$datalist->message='请联系客服！';
					}
				}

				
			}
			else
			{
				$datalist->message='您今日红包提现请求次数超过10次，明日再提。';
			}
		}
		else
		{
			$datalist->message='提现金额超出红包金额！';
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
	public $message='默认错误';
	public $hongbao='11.11';//红包余额
 
}
 

?>