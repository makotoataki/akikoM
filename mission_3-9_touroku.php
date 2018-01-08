<?php

//データベースへの接続
//データベースへの接続
$dsn = '****';
$user = '****';
$password = '****';
$pdo = new PDO($dsn,$user,$password);
$stmt = $pdo->query('SET NAMES utf8');


//変数受け取り
$name= $_POST['name'];
$pass = $_POST['pass'];
$mail = $_POST['mail'];


//ユーザー情報書き込み---------------------------------------------------------------------------------
//名前・パスがあるときのみ書き込み
if(!empty($_POST['send'])){
	
if(!empty($name) && !empty($pass) && !empty($mail)){
	
	$kari = true;
	$user_id = uniqid(rand());
	
	//PDOでINSETを利用してカラムに値を代入する
		$sql = $pdo -> prepare("INSERT INTO user_tb (name,pass,mail,kari,user_id) VALUES(:name,:pass,:mail,:kari,:user_id)");

	$sql->bindParam(':name',$name,PDO::PARAM_STR);
	$sql->bindParam(':pass',$pass,PDO::PARAM_STR);
	$sql->bindParam(':mail',$mail,PDO::PARAM_STR);
	$sql->bindParam(':kari',$kari,PDO::PARAM_STR);
	$sql->bindParam(':user_id',$user_id,PDO::PARAM_STR);

	$sql->execute();
	
	//URLつくる
		$URL = "http://co-361.it.99sv-coco.com/login_mail.php?user_id=$user_id";
	
	
	//メールで送る
	//メール日本語設定
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	
	//メール内容
	$to = "$mail";
	$subject = '【このゆびとまれ！】アカウント本登録URL';
	$message = "{$name}さん
【このゆびとまれ！】に仮登録いただきありがとうございます。
以下のURLをクリックすると本登録が完了します。

{$URL}";
		
	$headers = '0115akik@gmail.com' . "\r\n";
	
	//メール送信
	mb_send_mail($to, $subject, $message, $headers);
	
	
	$oshirase = "ご登録いただいたメールアドレス宛に本登録用のURLを添付したメールを送信いたしました。<br>メールに添付されたURLをクリックしていただくことで本登録が完了します。<br><br>";
}
}

?>

<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<head>
			<meta charset="utf-8">
			<link rel="stylesheet" href="/toppage.css">
			<title>このゆびとまれ！：新規登録</title>
		</head>
		<body>
		
<div class="midashi">
	<div class="heading">このゆびとまれ！</div>
</div>

		<div class ="h1">
		
		<font>&nbsp;</font>
		
		<!-入力フォームを表示->
		<form action="" method="post">
		<p><div class ="t1">&nbsp;&nbsp;■アカウント登録■</div><br><br>
		<!-フォームの内容->
		&nbsp;&nbsp;アカウント名：<input type="text" name="name">
		&nbsp;&nbsp;パスワード：<input type="password" name="pass"></p>
		<p>&nbsp;&nbsp;メールアドレス：<input type="text" name="mail">
		
		<!-送信ボタンをつくる->
		&nbsp;&nbsp;<input type="submit" name="send" value="送信">
		</form>
		</p>
		<!-- //空欄かどうか -->
			<?php if(!empty($_POST['send']) && empty($name)){ ?>
				<font color="red">&nbsp;&nbsp;アカウント名を入力してください</font><br>
			<?php } ?>
			<!-- //パスワードがあるか -->
			<?php if(!empty($_POST['send']) && empty($pass)){ ?>
				<font color="red">&nbsp;&nbsp;パスワードを入力してください</font><br>
			<?php } ?>
			<!-- //メアドがあるか -->
			<?php if(!empty($_POST['send']) && empty($mail)){ ?>
				<font color="red">&nbsp;&nbsp;メールアドレスを入力してください</font><br>
			<?php } ?>

		
		<br>
		
		<font color="red">&nbsp;&nbsp;<?php echo $oshirase; ?></font>
		
		&nbsp;&nbsp;<a href="http://co-361.it.99sv-coco.com/login_mail.php" class="square_btn">ログインはこちら</a>
		
		<br>
		<br>
		&nbsp;
		</div>
		</body>
		</html>

