<?php 
//获取用户信息

//session_start();
require("phpconnect.php");
  
$tmpopenid=$_POST["openid"];
$code =$_POST["code"];


$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
// if(!isset($openid) || strlen($openid)<3 || $openid=='')
// {
// 	$ret='用户不存在';
// }
 
if($ret===true)
{

	$datalist->errorcode=0;
	$result_b = $con->query("SELECT title FROM zhufutitle     order by id    limit 50"  );

	if($result_b)
	{
		while($row_a = $result_b->fetch_assoc())
		{
			$datalist->titlelist[]=$row_a['title'];	 
		}
		$result_b->free();
	}

	$result_b = $con->query("SELECT hengfu,shanglian,xialian FROM duilian     order by id    limit 50"  );

	if($result_b)
	{
		while($row_a = $result_b->fetch_assoc())
		{
			$di=new DataItem;
			$di->hengpi=$row_a['hengfu'];
			$di->shanglian=$row_a['shanglian'];
			$di->xialian=$row_a['xialian'];
			$datalist->duilianlist[]=$di;	 

		}
		$result_b->free();
	}
	if(isset($tmpopenid) && strlen($tmpopenid)>3)
	{
		$datalist->openid=$tmpopenid;
	}
	$openid='';
	if(isset($code) && strlen($code)>4  )
	{
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
			if($result_a->num_rows==0)	 
			{

				 
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

		 

				$tsql = "INSERT INTO user(country,unionid,pass,invitecode,username,headimgurl,pid,device,sex,province,city,openid) VALUES (  '$country', '$unionid',  '$pass', '$ivcode', '$username','$icon','$pid','$device' ,'$sex','$province','$city','$openid')";
				//	file_put_contents( "162.txt",$tsql);
				//echo "<br /><br />".$tsql;
				$con->query($tsql);
 
			}
			$result_a->free();
		}
	}
	if(strlen($openid)>3)
	{
		$datalist->openid=$openid;
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
 
 
//列表
class Datalist 
{
	public $errorcode=1;//0表示正常
	public $message='';
	public $openid='';
	public $titlelist=array();//祝福标题列表
	public $duilianlist=array();//对联列表
 
}
class DataItem
{
	
	public $hengpi='';//横批
	public $shanglian='';//上联
	public $xialian='';//下联

}
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

?>