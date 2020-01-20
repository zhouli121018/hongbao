<?php
 require("phpconnect.php");
	 $req_info='QaRBdVf9/jOAJjuEGR8J+fdjoO5kbxpCWZUuwVUaLflLTNaTQ4d1HfJ0efr3bT8CEh/NCh8fTEkIyXP9Ryr3MRJ7bP9RZu6pp67jJZ8PRaYLmHqMfgOtGu9xE5f6Ol4dDM4tArM0C3O8xWfBAHHQCAmGCfl6ysv0LldsShNSYjN7U2C1crAOQikggJgSZ5PBsk0dDpK52dbr8eiYwjKsOG3Hy4K+FET7ej+u3VwUWW4oUFNGbPzZx0zC+Oo3ou1Vx/CoxZ3ON1DX2XLVu8AMDEE28BNTnRQY6f/R0nxdaFrXTtLO5smwtXe0xoAO4O8hOz3EjVtY17s7ZNUrvfjlrNb4sptr8oZw5nl+esLrGnQifBUVDG24WGrHj8hTm1eES1BRKrfAQuf/D8HnGFjMBE17/8Qd4p/yokQEMOZqU3kDdDAo5YkpaGR1VulyKp6293MhTHaxpaUbgj0Qp5rcxF+GlxJwXnFwHIzSq0QRCou1LIHoH0NTcVsg7f3vrY81DX1vSGlSJSCQ8FZB55Z4l9Im9Ffg2CjwbZ3j+GWGDmcE/AXQlZlCguaoBlA1v6LdTOwHAt4Ww5Odt9IVwBsP/QVMZY7WYN/UVpVNgW7wgzDOIdBP2x0DqOdoLxxmbWW+6Kqy3BESMi8OQX/LZ+baUI3MxvHI7fYGtWLkgfmxe/HGx+8WZspVuIaLJg3DCIC2AbZGTOLVod6Kvx6x9b6tAF3Y6JNIF1KL4jcCM0QI1HWf+stJbbPNgr5qalEWksLwEsI1qYxRDcbF4IutaIH67Vs3UGIuAjUJ9ZXpKNttDEPfklvFQ0DFZyDcislCzfUyUy/FNaLXOOTvG1aRIOey9dbwCuh05C/jC7kog1PYiwX9KzOb3op8v4nk8seGwmraRwow41qvaHUn+qiZgiLztwBQzBzSe3Cw9LkKp16d8aVbN1BiLgI1CfWV6SjbbQxDFi6llK8f1RyrsdaCjThW0/dkjBUYHZeiy1v9wq4RTeiuBpbUnTYBzCmarh6dpCkCvVY9kJv17JtBOU63eRMNCNj8vRFyALJwAFgjnthtcXUFRTGWPNDSL2wsnSo22aQX';
	  
	 $decrypt = base64_decode($req_info, true);
	 
	  $pdata= openssl_decrypt($decrypt , 'aes-256-ecb', md5('9934e7d25453e97507ef794cf7b0529e'), OPENSSL_RAW_DATA);
	 //var_dump($pdata);
	 $xml = simplexml_load_string($pdata);
	 echo $xml->refund_status;

	 echo'-8888-'. $xml->refund_recv_accout;

	 $apiclient_cert=getcwd()."/tcacert/ap_cert.pem";
	 echo $apiclient_cert;
	 /*	<root>
<out_refund_no><![CDATA[8-1578713162-200]]></out_refund_no>
<out_trade_no><![CDATA[1-1578655701_1973733340]]></out_trade_no>
<refund_account><![CDATA[REFUND_SOURCE_RECHARGE_FUNDS]]></refund_account>
<refund_fee><![CDATA[100]]></refund_fee>
<refund_id><![CDATA[50300103052020011114086866821]]></refund_id>
<refund_recv_accout><![CDATA[招商银行借记卡8732]]></refund_recv_accout>
<refund_request_source><![CDATA[API]]></refund_request_source>
<refund_status><![CDATA[SUCCESS]]></refund_status>
<settlement_refund_fee><![CDATA[100]]></settlement_refund_fee>
<settlement_total_fee><![CDATA[1100]]></settlement_total_fee>
<success_time><![CDATA[2020-01-11 11:26:30]]></success_time>
<total_fee><![CDATA[1100]]></total_fee>
<transaction_id><![CDATA[4200000514202001108179917617]]></transaction_id>
</root>*/
	 

	//-------------------------检查7天红包未认领退款----------------
	/*
$nowtime=date('Y-m-d H:i:s');
$result_b = $con->query("SELECT  hongbao,id,hbid,hongbao_sy,invitecode,trade_no_wx FROM flow_hongbao_kj where status=1 and istui=0 and endtime<'$nowtime'  and hongbao_sy>0 "  );
		
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

	$apiclient_cert=getcwd()."/tcacert/ap_cert.pem";
	$apiclient_key=getcwd()."/tcacert/ap_key.pem";
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
 	echo $file_contents ;
	$xml = simplexml_load_string($file_contents);
	$return_code =(string)$xml->return_code;
	if($return_code=="SUCCESS")
	{
		$result_code =(string)$xml->result_code;
		if($result_code=="SUCCESS")//这里应该验证服务器过来的签名
		{
			 //--------------微信退款---------------

			$tsql = "INSERT INTO flow_hongbao_tui(refund_no,trade_no_wx,invitecode,hbid,money) VALUES ('$out_refund_no','$transaction_id','$invitecode','$hbid','$hongbao_sy'  )";
			//echo $tsql ;
			echo $tsql;
			$con->query($tsql);

			// if($con->affected_rows>0)
			// {
			// 	$con->query("update flow_hongbao_kj set istui=1 where id= $id");

			// 	//微信退款
			// }
		}
	}


	
}
*/
?>