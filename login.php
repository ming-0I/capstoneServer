<?php
	require('/var/www/html/conn.php');
	require('/var/www/html/jwt.php');

	mysqli_select_db($conn, $dbname);
	$user_id = $_POST['id'];
	$user_pw = $_POST['pw'];
	$name = "";
	$sql = "SELECT * FROM user where id='$user_id' AND pw='$user_pw'";

	$result = mysqli_query($conn, $sql);
	$r = mysqli_num_rows($result);

	//login Success
	if($r == 1){
		//name get
		while($row=mysqli_fetch_assoc($result)){
			$name=$row['name'];
		}


		//token init
		$payload_test=array('iss'=>'admin','iat'=>time(),'exp'=>time()+7200,'nbf'=>time(),'sub'=>$user_id,'jti'=>md5(uniqid('JWT').time()));

		//token create
		$token_test=Jwt::getToken($payload_test);

		$value = array("name"=>$name,"isSuccessed"=>true,"token"=>$token_test);
  
	}else{//login fail
		$value = array("name"=>$name,"isSuccessed"=>false,"token"=>"");
	}
	//token send
	echo json_encode($value);
	ini_set("display_errors", 1);
	mysqli_close($conn);
?>
