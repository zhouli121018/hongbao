<?php
require("phpconnect.php");

//---------------------------------------------------------
//即时到帐支付后台回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------
$postxml = file_get_contents('php://input');
/*$postxml ='<xml>
  <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
  <attach><![CDATA[支付测试]]></attach>
  <bank_type><![CDATA[CFT]]></bank_type>
  <fee_type><![CDATA[CNY]]></fee_type>
  <is_subscribe><![CDATA[Y]]></is_subscribe>
  <mch_id><![CDATA[10000100]]></mch_id>
  <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
  <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
  <out_trade_no><![CDATA[1409811653]]></out_trade_no>
  <result_code><![CDATA[SUCCESS]]></result_code>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
  <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
  <time_end><![CDATA[20140903131540]]></time_end>
  <total_fee>1</total_fee>
  <trade_type><![CDATA[JSAPI]]></trade_type>
  <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
</xml>';*/

$xml = simplexml_load_string($postxml);
  
$return_code =(string)$xml->return_code;

$returnpost='<xml>
			  <return_code>FAIL</return_code>
			  <return_msg>FAIL</return_msg>
			</xml>';

if($return_code=="SUCCESS"  )
{
	$req_info=(string)$xml->req_info;
	//$req_info=base64_decode($req_info);
	$decrypt = base64_decode($req_info, true);

	$pdata= openssl_decrypt($decrypt , 'aes-256-ecb', md5('9934e7d25453e97507ef794cf7b0529e'), OPENSSL_RAW_DATA);
	 
	$xml = simplexml_load_string($pdata);
	$refund_status= $xml->refund_status;
	 
	if($refund_status=="SUCCESS")
	{
		$transaction_id=(string)$xml->transaction_id;//微信订单号
		$out_trade_no=(string)$xml->out_trade_no;//商户系统内部的订单号

		$out_refund_no=(string)$xml->out_refund_no;//商户退款单号
		$refund_id=(string)$xml->refund_id;//微信退款单号
 
		$qureyctcsql="select id,hbid from flow_hongbao_tui where refund_no='$out_refund_no' and status=0 ";
		 
		$result_aba =  $con->query($qureyctcsql);
		$hongbao_tui =$result_aba->fetch_array();
		$hbid=$hongbao_tui['hbid'];

		$num_rows=$result_aba->num_rows;
		$result_aba->free();
		if($num_rows>0)
		{
			$updateurl="update flow_hongbao_tui set status=1,refund_no_wx='$refund_id' where refund_no='$out_refund_no'";
			$con->query($updateurl);
			if($con->affected_rows>0)
			{
				 
				$returnpost='<xml>
				<return_code>SUCCESS</return_code>
				<return_msg>OK</return_msg>
				</xml>';
				//echo $returnpost;
			}
			else
			{
				$returnpost='<xml>
				<return_code>FAIL</return_code>
				<return_msg>DB ERROR</return_msg>
				</xml>';
				//echo $returnpost;
			}

		}
		else
		{
			$returnpost='<xml>
			<return_code>FAIL</return_code>
			<return_msg>DB ERROR</return_msg>
			</xml>';
			//echo $returnpost;
		}
	}
	else
	{
		$returnpost='<xml>
			  <return_code>FAIL</return_code>
			  <return_msg>FAIL</return_msg>
			</xml>';
			//echo $returnpost;
	}
	 
}
else
{
	$returnpost='<xml>
			  <return_code>FAIL</return_code>
			  <return_msg>FAIL</return_msg>
			</xml>';
			
}	

echo $returnpost;	

?>