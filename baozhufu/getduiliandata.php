<?php 
//获取用户信息

//session_start();
require("phpconnect.php");
  
$openid=$_POST["openid"];



$datalist=new Datalist;
$ret =checkSignH5($con,$_POST);
if(!isset($openid) || strlen($openid)<3 || $openid=='')
{
	$ret='用户不存在';
}
 
if($ret===true)
{

	$result_a = $con->query("SELECT  id FROM user where   openid='$openid' "  );
		
	if($result_a->num_rows>0)
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
	
	public $titlelist=array();//祝福标题列表
	public $duilianlist=array();//对联列表
 
}
class DataItem
{
	
	public $hengpi='';//横批
	public $shanglian='';//上联
	public $xialian='';//下联

}

?>