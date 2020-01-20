 

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微信支付样例-支付</title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			{
         "appId":"wx13665daee96a688b",     //公众号名称，由商户传入     
         "timeStamp":"1578655702",         //时间戳，自1970年以来的秒数     
         "nonceStr":"6f29846098909d611c75beff72fd12d4", //随机串     
         "package":"prepay_id=wx1019282215775705d41f9a9b1754136300",     
         "signType":"MD5",         //微信签名方式：     
         "paySign":"DADA52F809E854B2F1E4079220530C70" //微信签名 
      },
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_code+res.err_desc+res.err_msg);
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	function editAddress()
	{
		// WeixinJSBridge.invoke(
		// 	'editAddress',
		 
		// 	function(res){
		// 		var value1 = res.proviceFirstStageName;
		// 		var value2 = res.addressCitySecondStageName;
		// 		var value3 = res.addressCountiesThirdStageName;
		// 		var value4 = res.addressDetailInfo;
		// 		var tel = res.telNumber;
				
		// 		alert(value1 + value2 + value3 + value4 + ":" + tel);
		// 	}
		// );
		alert("editAddress");
	}
	
	window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	};
	
	</script>
</head>
<body>
    <br/>
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>
	<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
	</div>
</body>
</html>