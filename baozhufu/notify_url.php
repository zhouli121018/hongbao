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
file_put_contents( "xxy.txt",$postxml . "\r\n",FILE_APPEND);
$return_code =(string)$xml->return_code;

$returnpost='<xml>
			  <return_code>FAIL</return_code>
			  <return_msg>FAIL</return_msg>
			</xml>';

if($return_code=="SUCCESS"  )
{
	$result_code=(string)$xml->result_code;
	if($result_code=="SUCCESS")
	{
		$parameters = array();
		foreach ($xml->children() as $child)
		{
			 $parameters[$child->getName()]=(string)$child;
		}
	  
		ksort($parameters);
		$signPars = "";
		foreach($parameters as $k => $v) {
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
				//echo $signPars .'</br>';
			}
		}
		$signPars .= "key=" . '9934e7d25453e97507ef794cf7b0529e';
		
		$mysign = strtoupper(md5($signPars));
			
		$transaction_id=(string)$xml->transaction_id;//微信订单号
		$out_trade_no=(string)$xml->out_trade_no;//商户订单号
		//$nonce_str=(string)$xml->nonce_str;// 
		//$mch_id=(string)$xml->mch_id;// 
		//$appid=(string)$xml->appid;// 
		$sign=(string)$xml->sign;// 
		if($sign==$mysign)
		{
			$userid="";
			$coin="0";
			$cash="0";
			$channel='';
			$parentagent=null;
			$qureyctcsql="select id from flow_hongbao_kj where trade_no='$out_trade_no' and status=0 ";
			$result_aba =  $con->query($qureyctcsql);
			$num_rows=$result_aba->num_rows;
			$result_aba->free();
			if($num_rows>0)
			{
				$updateurl="update flow_hongbao_kj set status=1,trade_no_wx='$transaction_id' where trade_no='$out_trade_no'";
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
			  <return_msg>SIGN FAILL</return_msg>
			</xml>';
		//	echo $returnpost;
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