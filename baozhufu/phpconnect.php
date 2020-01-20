<?php
//$con = mysql_connect ("127.0.0.1:3306", "root","ybutFMCTsm3p5xM4");
$MYSQL_HOST_M='localhost';
$MYSQL_PORT='3306';
$MYSQL_USER='root';
$MYSQL_PASS='5QyZdRiv2U';
$MYSQL_DB='chuanyin';
//$con = mysql_connect( $MYSQL_HOST_M . ":" . $MYSQL_PORT, $MYSQL_USER,$MYSQL_PASS);
//$con = mysqli_connect($MYSQL_HOST_M, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
$con=new mysqli($MYSQL_HOST_M,$MYSQL_USER,$MYSQL_PASS,$MYSQL_DB);
//if (mysqli_connect_errno($con))
if(mysqli_connect_errno())
{
	//statusEcho('10086',"db error");
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
    return;
}

//mysqli_query($con,"set names 'utf8'");
$con->set_charset("utf8");
function isEmptyStr($value)
{

if(!isset($value)||is_null($value)  || $value=='' || $value=='null' || $value=='NULL' || strlen($value)<=0)
return true;
else
return false;

}
 

// $num为生成随机数字的个数
function getRandDiginum($num=4) 
{
    //$num=4;//验证码个数
	$invitecode='';
	for($i=0;$i<$num;$i++)//生成验证码
	{
		$invitecode=$invitecode . chr(rand(48,57));
	}
	return intval($invitecode);
}

// $num为生成汉字的数量
function getChar($num) 
{
   $b = '';
   for ($i=0; $i<$num; $i++) {
	   // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
	   $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
	   // 转码
	   $b .= iconv('GB2312', 'UTF-8', $a);
   }
   return $b;
}

function checkSignH5($con,$params)
{
	$token=$params['token'];
	$sign=$params['data'];
	//1判断是否已经存在数据库
	$tmprows=-1;
	if(isset($token)&&strlen($token)>0)
	{
		$result_ap = $con->query("SELECT id FROM signreq where  token=$token  " );
		if($result_ap)
		{
			$tmprows=$result_ap->num_rows;
			$result_ap->free();
		}
	}
	
	if($tmprows==0)
	{
		$con->query("insert into signreq(token) values('$token')" );
		$presign='token='.$token.'&key=lldu43d98382';
		//file_put_contents( "be.txt",$presign,FILE_APPEND );
		$signa=MD5($presign);
		if($signa===$sign)
		{
			 
			return true;
		}
		else
		{
		return '数据源错误';
		}
	}
	else
	{
	return '数据错误';
	}
	
}
function  genInviteCode( $num=6)
{
	$rad=array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z');
	shuffle($rad);
	//数字 chr(rand(48,57)
//大写字母 strtoupper(chr(rand(65,90)));

	//$num=6;//验证码个数
	$pass='';
	$siz=count($rad)-1;
	for($i=0;$i<$num;$i++)//生成验证码
	{
		$pass=$pass . $rad[rand(0,$siz)];
	}
	return $pass;

}
function randomFloat($min = 0, $max = 1) 
{  
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);  
}
 
?>
