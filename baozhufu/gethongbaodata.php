<?php 
//获取用户信息

//session_start();
require("phpconnect.php");
//include 'phpqrcode.php'; 
$code=$_POST["code"];//微信授权返回的code
//$code='061VaMUp0VwvGk1nNUXp05IPUp0VaMU7';
//$code=$_GET["code"];//微信授权返回的code
$device=$_POST["device"];//0android ，1ios
$pid=$_POST["pid"];//推荐人
$hbid=$_POST["hbid"];//红包id 6位数
$openid_tmp=$_POST["openid"];//openid

 
//file_put_contents("222.txt", $code.'_'.$channel.'_'.$token, FILE_APPEND);
if(!isset($pid) || strlen($pid)<3 || $pid=='')
{
	$pid='';
}

if(!isset($device))
{
	$device=0;
}
 
 
$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
//$ret=true;
if($ret===true)
{
	$openid='';
	$myhongbao=0;
	//$openid_exit=0;
	if(isset($openid_tmp)&&strlen($openid_tmp)>4)
	{
		$openid=$openid_tmp;
		$result_a = $con->query("SELECT  id,hongbao FROM user where   openid='$openid_tmp' "  );
		
		if($result_a->num_rows>0)
		{ 
			
			$userdata =$result_a->fetch_array();
			$myhongbao=round($userdata['hongbao'],2);
		}
		$result_a->free();

	}
	else if(isset($code)&&strlen($code)>4 )//授权过来
	{
		//AppID：wx039d71c6949b4804
		$appid="wx13665daee96a688b";//广州小蚯
		$secret="b3e2127e043848a38eda12952dfc9147";//
		
		//file_put_contents("222.txt", 'step1', FILE_APPEND);

		$ch = curl_init("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
		$output = curl_exec($ch) ;
		//file_put_contents( "11.txt",$output);
		//$exposurelist->message=$output;
		curl_close($ch);
		//echo $output;

		/*	
		{"access_token":"28_d_8nr8AKkN-rMme90Ycg_o1StKdwCOuyaU9cnJqYrY8O8pIsEb_9TAFNYMLSdVdRZciz7jeMuy8BsoKoZXj1l60LVn9IymP7xxfEzhMIQ7o",
			"expires_in":7200,"refresh_token":"28_TpFX-EHHy9NeiJQDZdiPx2D5_ihHMC-HzD501o7GMZBslMDhqchkXdbl2TWTEAOjlQ5UKYiGE4ubZSJyER8suuW1C3JnzH0x8uMUMEH4BLA",
			"openid":"oHmFm6BOF4okrdNKvjk2BXmKritM","scope":"snsapi_userinfo"}*/
		//	echo $output;
		$tokenjs=json_decode($output);
		if(!is_null($tokenjs->openid) && strlen($tokenjs->openid)>1)
		{
			
			$access_token=$tokenjs->access_token;
			$openid=$tokenjs->openid;
			 
			//file_put_contents($openid.".txtt",$output." \r\n", FILE_APPEND);

			//判断微信用户是否已经注册过
			$result_a = $con->query("SELECT  id,hongbao FROM user where   openid='$openid' "  );
			//$ishave=0;
			
			if($result_a)//已经注册过直接返回注册信息
			{ 
				if($result_a->num_rows>0)
				{ 
					$userdata =$result_a->fetch_array();
					$myhongbao=round($userdata['hongbao'],2);
				}
				else
				{

					/*{   
						"openid":" OPENID",
						" nickname": NICKNAME,
						"sex":"1",
						"province":"PROVINCE"
						"city":"CITY",
						"country":"COUNTRY",
						"headimgurl":       "http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/46",
						"privilege":[ "PRIVILEGE1" "PRIVILEGE2"     ],
						"unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"

						{"openid":"oHmFm6BOF4okrdNKvjk2BXmKritM","nickname":"徐XX","sex":1,"language":"zh_CN","city":"","province":"","country":"阿尔巴尼亚",
							"headimgurl":"http:\/\/thirdwx.qlogo.cn\/mmopen\/vi_32\/2DjqOWmyMVuXIqQibLvUfygbhwhDQ3SPtHM55S9Rws06UXKiaI94icdKTwPZG1P1O1RvMibVLicebLUCbg1thicUVeDQ\/132","privilege":[]}

					}*/
					
					$ch = curl_init("https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
					curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
					$output = curl_exec($ch) ;
					//file_put_contents( "161.txt",$output);
					//$exposurelist->message=$output;
					//echo "<br /><br />".$output;
					curl_close($ch);
					$userjs=json_decode($output);
					$username=$userjs->nickname;
					$username = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $username);
					//$pass=''.rand(10000,999999);
					//---------------生成随机6位数密码------------------
					$num=6;//验证码个数
					$pass='';
					for($i=0;$i<$num;$i++)//生成验证码
					{
						$pass=$pass . chr(rand(48,57));
					}
					//---------------生成随机6位数密码-------------
					$sex=$userjs->sex;
					$province=$userjs->province;
					$city=$userjs->city;
					$country=$userjs->country;
					$unionid='';
					if(!is_null($userjs->unionid))
					$unionid=$userjs->unionid;

					$headimgurl=$userjs->headimgurl;
					//file_put_contents($openid.".txt",$output." \r\n", FILE_APPEND);

					//用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空
					$icon="";
					if(!is_null($headimgurl) && strlen($headimgurl)>5)
					{
						$poss= strripos($headimgurl,'/');
						if($poss>0)
						{
						$headimgurl = substr($headimgurl,0,$poss+1).'132';
						$icon='uicon/'.(time()+rand(1,100)).'.jpg';
						//$curpath=dirname(__FILE__);
						//file_put_contents($curpath."/112.txt",$headimgurl." \r\n", FILE_APPEND);					
						dlfile($headimgurl, $icon);
						}
					}
					
					$ivcode=genInviteCode(6);
					$result_aba =  $con->query("SELECT id  FROM user where invitecode='$ivcode'  "  );
					while($result_aba->num_rows>0)
					{
						$ivcode=genInviteCode(6);
						$result_aba =  $con->query("SELECT id  FROM user where invitecode='$ivcode'  "  );
					}

				/*	$tinyurl='http://www.sscby.com/mp/test.html?pid='.$ivcode;

					//$tinyurl=$html;	
					//$value = $tinyurl; //二维码内容 
					$errorCorrectionLevel = 'L';//容错级别 
					$matrixPointSize = 6;//生成图片大小 
					//生成二维码图片 
					$barcodepath='barcode/' . $ivcode . '.png';
					QRcode::png($tinyurl, $barcodepath, $errorCorrectionLevel, $matrixPointSize, 2);

					

					$nums=array('5000000','10000000','50000000');
					$mt=$nums[rand(0,2)];
					$money=round((randomFloat()*$mt)/1.0,1);
					 
					$money_wan=round($money/10000,1);*/

					$tsql = "INSERT INTO user(country,unionid,pass,invitecode,username,headimgurl,pid,device,sex,province,city,openid) VALUES (  '$country', '$unionid',  '$pass', '$ivcode', '$username','$icon','$pid','$device' ,'$sex','$province','$city','$openid')";
					//	file_put_contents( "162.txt",$tsql);
					//echo "<br /><br />".$tsql;
					$con->query($tsql);
	 
				}
				
				$result_a->free();
			}
		}
	}
	if(isset($hbid) && strlen($hbid)>0 && isset($pid) && strlen($pid)>0)
	{
		$result_a = $con->query("SELECT  voicetime,invitecode,hongbao,serverid,zhufutitle,hengfu,shanglian,xialian FROM flow_hongbao_kj where   hbid='$hbid' "  );
	 
		if($result_a->num_rows>0)
		{
			$datalist->errorcode=0;
			$userdata =$result_a->fetch_array();
			$datalist->hbid=$hbid;
			$invitecode=$userdata['invitecode'];
			$datalist->hongbao=$userdata['hongbao'];
			$datalist->serverid=$userdata['serverid'];
			$datalist->zhufutitle=$userdata['zhufutitle'];
			$datalist->hengfu=$userdata['hengfu'];
			$datalist->shanglian=$userdata['shanglian'];
			$datalist->xialian=$userdata['xialian'];
			$datalist->voicetime=$userdata['voicetime'];
			$datalist->openid=$openid;
			$datalist->myhongbao=$myhongbao;
			$result_a->free();

			$result_am = $con->query("SELECT  headimgurl FROM user where   invitecode='$invitecode' "  );
			if($result_am->num_rows>0)
			{
				$userdatam =$result_am->fetch_array();
				$datalist->headimg=$userdatam['headimgurl'];
			}
			$result_am->free();

		}	 

	}

}
else
{
$datalist->message=$ret;
}
$con->close();
$persiondata = json_encode($datalist);
 
//file_put_contents($openid."-.txttt",$persiondata." \r\n", FILE_APPEND);
echo $persiondata ;

function dlfile($file_url, $save_to)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 0); 
	curl_setopt($ch,CURLOPT_URL,$file_url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$file_content = curl_exec($ch);
	curl_close($ch);
	$downloaded_file = fopen($save_to, 'w');
	fwrite($downloaded_file, $file_content);
	fclose($downloaded_file);
}
 
//列表
class Datalist 
{
	public $errorcode=1;//0表示正常
	public $message='';

	public $hbid='0';//红包id
	public $headimg='uicon/default.png';//用户头像
	public $myhongbao='0';
	public $hongbao='0';
	public $serverid='';//获取录音需要的id
	public $zhufutitle='祝您2020年幸福安康';//祝福语
	public $hengfu='万事如意';// 
	public $shanglian='年年如意发大财';//上联
	public $xialian='岁岁平安行大运';//下联
	public $gongzhonghao='gongzhonghao.jpg';//公众号二维码
	public $voicetime=0;
	public $openid='';//返回的openid，有的话需要保存起来。

	 
}

?>