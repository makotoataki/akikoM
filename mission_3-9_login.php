<?php
header('Content-Type: text/html; charset=UTF-8');

//データベースへの接続
$dsn = ****;
$user = ****;
$password = ****;
$pdo = new PDO($dsn,$user,$password);

//GETでURLと一致するuser_idを抜き出し
if(isset($_GET['user_id'])){
	
	//変数にGETで格納
	$user_id = $_GET['user_id'];
	
	//kariをfalseにする
	$sql = "update user_tb set kari=false where user_id = '$user_id';";
	$result = $pdo->query($sql);
	//user_idと一致するnameの行を抜き出す
	$stmt =	$pdo->prepare("select * from user_tb where name = '$user_id'");
	$stmt -> bindParam(':name',$user_id, PDO::PARAM_STR);
	$stmt -> execute();
	
	//$stmtの中の上で検索した一行をfetchで抜き出し、変数に格納
	$result = $stmt->fetch();
	
	$onamae = $result['name'];
	
	$login_messege = "本登録が完了しました。以下のログインフォームからログインしてください。";
}

//普通のログイン
$send=$_POST['send'];

if(!empty($send)){

//フォームから値を受け取り変数に格納
$username=$_POST['username'];
$password=$_POST['password'];


//アカウント名とパスワードの合致を確認
if(!empty($username) && !empty($password)){
	//$usernameと一致するnameの行を抜き出す
	$stmt =	$pdo->prepare("select * from user_tb where name = '$username'");
	$stmt -> bindParam(':name',$username, PDO::PARAM_STR);
	$stmt -> execute();
	
	//$stmtの中の上で検索した一行をfetchで抜き出し、変数に格納
	$result = $stmt->fetch();
	
	if(empty($result)){
		$name_error = "アカウント名が間違っているか、アカウントが存在しません";
		}
		
		//デバッグ
		//echo "kari|".$result['kari']."|";
		
		if($result['kari'] == true){
			$yet = "メール認証が行われていません";
		}
		
		if($result['kari'] == false){
		
		if($password == $result['pass']){
			$loginuser = $result['name'];
			$loginpass = $result['pass'];
		session_start();
			$_SESSION['name']=$loginuser;
			$_SESSION['pass']=$loginpass;
			
			header('Location: /toppage.php');
			exit();
		}else{
				$pass_error = "パスワードが間違っています<br>";
			}
		}
	}

if(empty($username)){
	$nameblanc="アカウント名を入力してください<br>";
}
if(empty($password)){
	$passblanc="パスワードを入力してください<br>";
}
}



?>
<!DOCTYPE html>
<html>
		<head>
			<meta charset="utf-8">
			<title>このゆびとまれ！：ログイン</title>
			<link rel="stylesheet" href="/toppage.css">
		</head>
		<body>
		
<div class="midashi">
	<div class="heading">このゆびとまれ！</div>
</div>

<div class ="h1">

		<font color="red">&nbsp;&nbsp;<?php echo $login_messege; ?></font>

		<form method="post" action="">
		<p><div class ="t1">&nbsp;&nbsp;■ログイン■</div><br><br>
		&nbsp;&nbsp;アカウント名: <input type="text" name='username'>
		&nbsp;&nbsp;パスワード: <input type="password" name='password'>
		&nbsp;&nbsp;<input type="submit" name="send" value="ログイン">
		</form>

		<p style="color: red;">&nbsp;&nbsp;
		<?php echo $pass_error; ?><?php echo $name_error; ?>
		<?php echo $nameblanc; ?><?php echo $passblanc; ?><?php echo $yet; ?></p>

		<br>
		<font>&nbsp;</font>
		&nbsp;&nbsp;<a href="http://co-361.it.99sv-coco.com/touroku.php" class="square_btn">新規登録はこちら</a>
		
		<br>
		<br>
		&nbsp;
</div>
</body>
</html>