<?php
	require('/var/www/html/conn.php');
	require('/var/www/html/jwt.php');
	
	mysqli_select_db($conn, $dbname);
	$user_token = $_POST['token'];
	$user_date = $_POST['date'];
	$isExpired = false;
	$tokens = explode('.', $user_token);
    	list($base64header, $base64payload, $sign) = $tokens;
	$payload = base64_decode($base64payload);
	$json_data = json_decode($payload, true);
	$user_id = $json_data['sub'];
	$getPayload_test=Jwt::verifyToken($user_token);
        // 토큰 만료
	if($getPayload_test == false){
		//token init
                $payload_test=array('iss'=>'admin','iat'=>time(),'exp'=>time()+7200,'nbf'=>time(),'sub'=>$user_id,'jti'=>md5(uniqid('JWT').time()));
                //token create
                $user_token=Jwt::getToken($payload_test);
		$isExpired=true;
  	} 
	$sql = "SELECT * FROM history where id='$user_id' AND date='$user_date'";
	$result = mysqli_query($conn, $sql);
	$r = mysqli_num_rows($result);
	if($r >= 1){
		$data = mysqli_fetch_assoc($result);
 		
		$value = array("pushUpSet"=>(int)$data['pushUpSet'], "pushUpCount"=>(int)$data['pushUpCount'], "pullUpSet"=>(int)$data['pullUpSet'], "pullUpCount"=>(int)$data['pullUpCount'], "squatSet"=>(int)$data['squatSet'], "squatCount"=>(int)$data['squatCount'], "lungeSet"=>(int)$data['lungeSet'], "lungeCount"=>(int)$data['lungeCount'], "isTokenExpired"=>$isExpired, "isSuccessed"=>true,"token"=>$user_token);
	}else{
		$value = array("history"=>array(), "isTokenExpired"=>$isExpired, "isSuccessed"=>false,"token"=>$user_token);
	}
	
	//token send
	echo json_encode($value);
	ini_set("display_errors", 1);
	mysqli_close($conn);
?>

