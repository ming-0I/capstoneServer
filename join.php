<?php
	require('/var/www/html/conn.php');

	mysqli_select_db($conn, $dbname);
	$user_id = $_POST['id'];
	$user_pw = $_POST['pw'];
	$user_name = $_POST['name'];
	//overlap test
	$sql = "SELECT * FROM user where id='$user_id'";
	$result = mysqli_query($conn, $sql);
	$r = mysqli_num_rows($result);
	if($r == 1){
		$value = array("isSuccessed"=>false);
	}else{
		$s = "insert into user (id, pw, name) values ('$user_id', '$user_pw', '$user_name')";
		$res = mysqli_query($conn, $s);
		$value = array("isSuccessed"=>true);
	}
	echo json_encode($value);
	ini_set("display_errors", 1);
	mysqli_close($conn);
?>
