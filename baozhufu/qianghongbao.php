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
		$datalist->hongbao=$hongbao_u;
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
						if($mtpn==0)//未抢过
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
								$tikuanok=0;
		
								

								//更新红包剩余
								$updatesql = "UPDATE flow_hongbao_kj SET hongbao_sy= '$hongbao_sy',renshu_sy='$renshu_sy' where hbid='$hbid'";
								$con->query($updatesql);
								if($hongbao_qiang>=1 && $hongbao_qiang<=200)
								{

									$date_start=date('Y-m-d').' 00:00:00';
									$sum_money=0;
									$result_aq = $con->query("SELECT  id ,money FROM flow_hongbao_tikuan where  status=1 and  invitecode='$invitecode' and createtime > '$date_start' "  );
									$tinum=$result_aq->num_rows;
									if($result_aq->num_rows<10)
									{ 
										while($row_cl = $result_aq->fetch_assoc())
										{
											$sum_money += floatval($row_cl['money']);
										}
									}
									if($tinum<10 && $sum_money+$hongbao_qiang<1000)
									{
										$mch_billno=time() . rand(100,999);
										$rrt=fahongbao($hongbao_qiang,$openid,$mch_billno,$con);
										if($rrt===true)
										{
											//直接发送给用户红包 ，不需要累加
											$time_tikuan=date('Y-m-d H:i:s' );
											$tsql = "INSERT INTO flow_hongbao_tikuan(invitecode,money,status,time_tikuan,orderid) VALUES ('$invitecode','$hongbao_qiang','1','$time_tikuan','$mch_billno')";
											$con->query($tsql);
	
											//更新用户红包
											//$updatesql = "UPDATE user SET hongbao= '$hongbao_user'  where invitecode='$invitecode'";
											//$con->query($updatesql);
											$tikuanok=1;
											//$datalist->tipflag=1;
										//$datalist->tipup='您的红包系统已发出，预计很快达到请注意查收';
											$datalist->tipdown="红包已发出，请注意查收<br />扫码关注公众号管理红包";
											$datalist->ishit=1;
											$datalist->errorcode=0;
											$datalist->content='抢到'.$hongbao_qiang.'元红包';
											$datalist->hongbao=$hongbao_u;
										}
										 
									}
									  
								}
								
								if($tikuanok==0)
								{

									//更新用户红包
									$updatesql = "UPDATE user SET hongbao= '$hongbao_user'  where invitecode='$invitecode'";
									$con->query($updatesql);

									$datalist->ishit=1;
									$datalist->errorcode=0;
									$datalist->content='抢到'.$hongbao_qiang.'元红包';
									$datalist->hongbao=$hongbao_user;

									//$datalist->tipup='您的红包已入账户';
									$datalist->tipdown="红包已入账户<br />扫码去公众号手动提现";
								}

							}
							else
							{
								
								$datalist->errorcode=0;
								$datalist->content='红包已抢完';
							}
						}
						else
						{
							$datalist->content='祝福红包已领';
							$datalist->errorcode=0;
							//$datalist->content='红包已抢完';
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
				$datalist->content='红包已过期';
				$datalist->errorcode=0;
				 
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

	//public $tipflag=0;//0 提示红包已如账户，1提示红包已发出
	//public $tipup='';
	public $tipdown='扫码关注公众号可管理红包';
	public $ishit=0;//1抢到，0没抢到
	public $content='';//抢到内容或者‘红包已抢完’
	public $hongbao='0';
}

function getRandMoney($remainmoney,$remainsize)
{
	$min=0.01;
	$max=$remainmoney/$remainsize*2;
	$money=randomFloat()*$max;
	$money=$money<=$min?0.1:$money;
	if($money>$remainmoney)
	$money=$remainmoney;
	if($remainsize==1)
	$money=$remainmoney;
	
	return round($money,2);
}

function fahongbao($money,$openid,$mch_billno,$con)
{
	$md_key='9934e7d25453e97507ef794cf7b0529e';

	
	
	$nonce_str = md5(rand());
	//$total_fee='';
	$clientip='118.31.109.150';
	$send_name='春节代发';
	$wishing='祝2020心想事成';
	$act_name='春节红包-代发';
	$remark='2020年包祝福';
	$total_amount=intval($money*100) ;

	//http://118.31.109.150/phpMyAdmin/sql.php?server=1&db=chuanyin&table=flow_hongbao_kj&pos=0
	//$notifyurl="http://118.31.109.150/baozhufu/notify_url.php";
	$body='传音包祝福红包-送亲朋好友';
	$detail='传音包祝福红包-送亲朋好友';
	
	//$clientip=$_SERVER['REMOTE_ADDR'];
	$stringA="act_name=$act_name&client_ip=$clientip&mch_billno=$mch_billno&mch_id=1573874181&nonce_str=$nonce_str&re_openid=$openid&remark=$remark&scene_id=PRODUCT_2&send_name=$send_name&total_amount=$total_amount&total_num=1&wishing=$wishing&wxappid=wx13665daee96a688b";
	$stringSignTemp=$stringA."&key=".$md_key;
	 

	$sign=strtoupper(MD5($stringSignTemp));
	  
	$postdata="<xml>
	<act_name><![CDATA[$act_name]]></act_name>
	<client_ip><![CDATA[$clientip]]></client_ip>
	<mch_billno><![CDATA[$mch_billno]]></mch_billno>
	<mch_id><![CDATA[1573874181]]></mch_id>
	<nonce_str><![CDATA[$nonce_str]]></nonce_str>
	<re_openid><![CDATA[$openid]]></re_openid>
	<remark><![CDATA[$remark]]></remark>
	<scene_id><![CDATA[PRODUCT_2]]></scene_id>
	<send_name><![CDATA[$send_name]]></send_name>
	<total_amount><![CDATA[$total_amount]]></total_amount>
	<total_num><![CDATA[1]]></total_num>
	<wishing><![CDATA[$wishing]]></wishing>
	<wxappid><![CDATA[wx13665daee96a688b]]></wxappid>
	<sign><![CDATA[$sign]]></sign>

	</xml>";
//
	//echo $postdata;
	 
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack");
	curl_setopt ($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 10);

	$apiclient_cert= "/data/wwwroot/default/baozhufu/tcacert/ap_cert.pem";
	$apiclient_key= "/data/wwwroot/default/baozhufu/tcacert/ap_key.pem";
	//$apiclient_ca = getcwd()."/tcacert/ap_cert.pem";
	//echo $apiclient_cert."<br />";
	//echo $apiclient_key;
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

	//curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
	curl_setopt($ch,CURLOPT_SSLCERT,$apiclient_cert);    	
	//curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
	curl_setopt($ch,CURLOPT_SSLKEY,$apiclient_key);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	//echo $file_contents ;
	$xml = simplexml_load_string($file_contents);
	$return_code =(string)$xml->return_code;
	if($return_code=="SUCCESS")
	{
		$result_code =(string)$xml->result_code;
		if($result_code=="SUCCESS")//这里应该验证服务器过来的签名
		{
			$tsql = "INSERT INTO flow_sendhb_status(openid,status) VALUES ('$openid','1' )";
			$con->query($tsql);
			return true;
		}
		else
		{
			$err_code_des=(string)$xml->err_code_des;
			$tsql = "INSERT INTO flow_sendhb_status(openid,detail) VALUES ('$openid','$err_code_des' )";
			$con->query($tsql);
		}
	}	
	return false;
}

?>