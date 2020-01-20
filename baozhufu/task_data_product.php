<?php 
//获取用户信息

//session_start();
require("phpconnect.php");
$clientip='118.31.109.150';//$_SERVER['REMOTE_ADDR'];

//-------------------------检查7天红包未认领退款----------------
$nowtime=date('Y-m-d H:i:s');
$result_b = $con->query("SELECT  hongbao,id,hbid,hongbao_sy,invitecode,trade_no_wx FROM flow_hongbao_kj where status=1 and istui=0 and endtime<'$nowtime'  and hongbao_sy>0 limit 1"  );
		
while($row_cl = $result_b->fetch_assoc())
{
	 
	$hongbao_sy=$row_cl['hongbao_sy'];//;
	$id=$row_cl['id'];
	$invitecode=$row_cl['invitecode'];
	$hongbao=$row_cl['hongbao'];
	$trade_no_wx=$row_cl['trade_no_wx'];
	$hbid=$row_cl['hbid'];

	//--------------微信退款-----------

	$md_key='9934e7d25453e97507ef794cf7b0529e';
 
	$appid='wx13665daee96a688b';
	$mch_id='1573874181';
	$nonce_str=md5(rand());
	$notify_url="http://118.31.109.150/baozhufu/notify_url_tuikuan.php";
	$out_refund_no=$id.'-'.time().'-'.rand(100,999);
	$refund_desc='语音祝福红包超7天未领退款';
	$refund_fee=round($hongbao_sy,2)*100;
	$transaction_id=$trade_no_wx;//微信支付成功的微信订单号
	$total_fee=round($hongbao,2)*100;
	$stringA="appid=$appid&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_refund_no=$out_refund_no&refund_desc=$refund_desc&refund_fee=$refund_fee&sign_type=MD5&total_fee=$total_fee&transaction_id=$transaction_id";
	$stringSignTemp=$stringA."&key=".$md_key;
	 
	$sign=strtoupper(MD5($stringSignTemp));

	$postdata="<xml>
	<appid><![CDATA[$appid]]></appid>
	<mch_id><![CDATA[$mch_id]]></mch_id>
	<nonce_str><![CDATA[$nonce_str]]></nonce_str>
	<notify_url><![CDATA[$notify_url]]></notify_url>
	<out_refund_no><![CDATA[$out_refund_no]]></out_refund_no>
	<refund_desc><![CDATA[$refund_desc]]></refund_desc>
	<refund_fee><![CDATA[$refund_fee]]></refund_fee>
	<sign_type><![CDATA[MD5]]></sign_type>
	<total_fee><![CDATA[$total_fee]]></total_fee>
	<transaction_id><![CDATA[$transaction_id]]></transaction_id>	
	<sign><![CDATA[$sign]]></sign>
	</xml>";

	//echo $postdata;
	 
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/secapi/pay/refund");
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
	//echo $file_contents.'---';
	  
	 // file_put_contents( "xx.txt",$file_contents,FILE_APPEND);
	$xml = simplexml_load_string($file_contents);
	$return_code =(string)$xml->return_code;
	if($return_code=="SUCCESS")
	{
		$result_code =(string)$xml->result_code;
		if($result_code=="SUCCESS")//这里应该验证服务器过来的签名
		{
			 //--------------微信退款---------------

			$tsql = "INSERT INTO flow_hongbao_tui(refund_no,trade_no_wx,invitecode,hbid,money) VALUES ('$out_refund_no','$transaction_id','$invitecode','$hbid','$hongbao_sy'  )";
			 
			$con->query($tsql);
			//$nowtime=date('Y-m-d H:i:s');
			$con->query("update flow_hongbao_kj set istui=1,time_tuikuan='$nowtime' where hbid= '$hbid'");

			// if($con->affected_rows>0)
			// {
			// 	$con->query("update flow_hongbao_kj set istui=1 where id= $id");

			// 	//微信退款
			// }
		}
	}


	
}
//-------------------------检查7天红包未认领退款----------------end



//-------------------------检查1-5天红包提款----------------
//$nowtime=date('Y-m-d H:i:s');
$starttime=date('Y-m-d H:i:s',strtotime('-5 days'));
//  
$result_b = $con->query("SELECT  id,money,invitecode FROM flow_hongbao_tikuan where status=0  and createtime<'$starttime' limit 1 "  );
		
while($row_cl = $result_b->fetch_assoc())
{
	//time_tikuan
	$money=$row_cl['money'];//;
	$id=$row_cl['id'];
	$invitecode=$row_cl['invitecode'];
	 
	$result_ap = $con->query("SELECT openid FROM user where   invitecode='$invitecode' "  );
		
	$userdata =$result_ap->fetch_array();
	$openid=$userdata['openid'];
	$result_ap->free();

	
	$tikuantime=date('Y-m-d H:i:s' );

	$md_key='9934e7d25453e97507ef794cf7b0529e';

	$mch_billno=$id.time() . rand(100,999);
	
	$nonce_str = md5(rand());
	//$total_fee='';

	$send_name='祝福包包-提款';
	$wishing='祝2020心想事成';
	$act_name='春节红包-祝福包包';
	$remark='2020年祝福包包提款';
	$total_amount=intval($money*100) ;

	//http://118.31.109.150/phpMyAdmin/sql.php?server=1&db=chuanyin&table=flow_hongbao_kj&pos=0
	//$notifyurl="http://118.31.109.150/baozhufu/notify_url.php";
	$body='传音包祝福红包-送亲朋好友';
	$detail='传音包祝福红包-送亲朋好友';
	
	//$clientip=$_SERVER['REMOTE_ADDR'];
	$stringA="act_name=$act_name&client_ip=$clientip&mch_billno=$mch_billno&mch_id=1573874181&nonce_str=$nonce_str&re_openid=$openid&remark=$remark&send_name=$send_name&total_amount=$total_amount&total_num=1&wishing=$wishing&wxappid=wx13665daee96a688b";
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
	<send_name><![CDATA[$send_name]]></send_name>
	<total_amount><![CDATA[$total_amount]]></total_amount>
	<total_num><![CDATA[1]]></total_num>
	<wishing><![CDATA[$wishing]]></wishing>
	<wxappid><![CDATA[wx13665daee96a688b]]></wxappid>
	<sign><![CDATA[$sign]]></sign>

	</xml>";

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
//	echo $file_contents ;
	$xml = simplexml_load_string($file_contents);
	$return_code =(string)$xml->return_code;
	if($return_code=="SUCCESS")
	{
		$result_code =(string)$xml->result_code;
		if($result_code=="SUCCESS")//这里应该验证服务器过来的签名
		{
			$con->query("update flow_hongbao_tikuan set status='1',time_tikuan='$tikuantime' where id= $id");


			if($con->affected_rows>0)
			{
				//微信提款到红包
				echo "<br />提款成功";

			}
		}
	}

	
}
//-------------------------检查1-5天红包提款----------------end



 

?>