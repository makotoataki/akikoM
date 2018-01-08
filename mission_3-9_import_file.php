<?php

	if(isset($_GET["target"]) && $_GET["target"] !== ""){
		$target = $_GET["target"];
	}
	else{
		header("Location: /toppage.php");
	}

//データベースへの接続
$dsn = '****';
$user = '****';
$password = '****';
$pdo = new PDO($dsn,$user,$password);

		//nameのファイルを選んで出力する
		$sql = "SELECT * FROM upfile WHERE name = :name;";
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(":name", $target, PDO::PARAM_STR);
		$stmt -> execute();
		$row = $stmt -> fetch(PDO::FETCH_ASSOC);
			header("Content-Type: ".$row["mime"]);
		echo ($row["file"]);
?>