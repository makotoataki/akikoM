<?php

session_start();

header('Content-Type: text/html; charset=UTF-8');

//データベースへの接続
$dsn = '****';
$user = '****';
$password = '****';
$pdo = new PDO($dsn,$user,$password);

//logout

// セッション変数を解除
$_SESSION['name'] = array();
$_SESSION['pass'] = array();

//cookie破棄
if (isset($_COOKIE["PHPSESSID"])) {
	setcookie("PHPSESSID", '', time() - 1800, '/');
}

// セッションを破棄
session_destroy();

?>

<!DOCTYPE html>
<html>
		<head>
			<meta charset="utf-8">
			<link rel="stylesheet" href="/toppage.css">
			<title>このゆびとまれ！：ログアウト</title>
		</head>

<div class="midashi">
	<div class="heading">このゆびとまれ！</div>
</div>
<div class = "h1">
ログアウトしました
		</div>
		</body>
</html>