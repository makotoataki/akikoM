<?php

//データベースへの接続
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn,$user,$password);
$stmt = $pdo->query('SET NAMES utf8');

//2-8テーブルをつくる 名前・コメント・パスが入る
$sql="CREATE TABLE utau".
		"("."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"."name char(32),"."comment TEXT,"."pass TEXT,"."time TIMESTAMP".");";
$stmt=$pdo->query($sql);

//2-9
/*//テーブル一覧を表示する
$sql = 'SHOW TABLES;';
$result = $pdo->query($sql);
foreach($result as $row){
	echo $row[0];
	echo $row[1];
	echo $row[2];
	echo '<br>';
	}
	
echo "<hr>";*/

//2-10
/*//show create table
$sql ='SHOW CREATE TABLE utau;';
$result = $pdo->query($sql);
foreach($result as $row){
	print_r($row);
}

echo "<hr>";*/

//2-11 書き込み

//変数受け取り
$name= $_POST['name'];
$comment = $_POST['com1'];
$pass = $_POST['pass'];

$intime = date("Y/m/d H:i");

$doEdi = $_POST['doEdi'];

$ediNumber = $_POST['edit_num'];
$delNumber = $_POST['delete_num'];
$pass_edi = $_POST['pass_edi'];
$pass_del = $_POST['pass_del'];

/*//変数デバッグ
echo "name|".$name."|";
echo "com|".$comment."|";
echo "pass|".$pass."|";
echo "time|".$intime."|<hr>";*/

//書き込み---------------------------------------------------------------------------------
//名前・コメント・パスがあるとき書き込み
if(!empty($name) && !empty($comment) && !empty($pass) && empty($ediNumber) && empty($delNumber) && empty($doEdi)){

	//PDOでINSETを利用してカラムに値を代入する
	$sql = $pdo -> prepare("INSERT INTO utau (name,comment,pass,time) VALUES(:name,:comment,:pass,:time)");

	$sql->bindParam(':name',$name,PDO::PARAM_STR);
	$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
	$sql->bindParam(':pass',$pass,PDO::PARAM_STR);
	$sql->bindValue(':time',$intime,PDO::PARAM_STR);

	$sql->execute();
	
	//デバッグ
	//echo "書き込み内容|".$name.",".$comment.",".$intime."|<hr>";
}

//編集機能---------------------------------------------------------------------------------

//編集番号とパスワードがあるとき、編集する名前とコメントを取得する
if(!empty($ediNumber) && !empty($pass_edi)){
	//編集番号をidに
	$id = $_POST['edit_num'];
	//編集する内容を受け取る whereで探す
	$stmt =	$pdo->prepare("select name, comment, pass from utau where id = :id");
	$stmt -> bindParam(':id',$id, PDO::PARAM_INT);
	$stmt -> execute();
	
	//$stmtの中の上で検索した一行をfetchで抜き出し、変数に格納
	$result = $stmt->fetch();

//編集パスワードがあっているならDBにある名前・コメントを変数に格納する
if($pass_edi == $result['pass']){
	$EdiName = $result['name'];
	$EdiCom = $result['comment'];
	/*//デバッグ
	echo "Ediname|".$EdiName."|";
	echo "Edicom|".$EdiCom."|<hr>";*/
}
	if($pass_edi !== $result['pass']){
		
		$edi_error = "パスワードが間違っています";
	}
}

//名前・コメント・hiddenがある
if(!empty($doEdi)){
	//2-13
	//idを変数に入れる
	$id = $doEdi;
	//書き換える内容を変数に入れる	
	$edi_name = $name;
	$edi_com = $comment;

	//idを検索し、updateで変数入れ替える
	$sql = "update utau set name='$edi_name',comment='$edi_com' where id = '$id';";

	$result = $pdo->query($sql);
}


//削除機能---------------------------------------------------------------------------------

//削除番号とパスワードが送信された
if(!empty($delNumber) && !empty($pass_del)){
	//削除番号と一致するidの行を抜き出す
	$id = $delNumber;
	$stmt =	$pdo->prepare("select name, comment, pass from utau where id = :id");
	$stmt -> bindParam(':id',$id, PDO::PARAM_INT);
	$stmt -> execute();
	
	//$stmtの中の上で検索した一行をfetchで抜き出し、変数に格納
	$result = $stmt->fetch();
	
	if($pass_del == $result['pass']){
		$id = $delNumber;
		$sql = "delete from utau where id ='$id';";
		$result = $pdo->query($sql);
		
	}else{
		$del_error = "パスワードが間違っています";
	}
}

?>

<!DOCTYPE html>
<html>
		<head>
			<meta charset="utf-8">
			<title>歌を歌いたい</title>
		</head>
		<body>
			<!-見出し->
			<font color="green"><h2>歌を歌いたい</h2></font>
		</script>
		
		<!-入力フォームを表示->
		<form action="" method="post">
		<p>＊コメント<br><?php if(!empty($ediNumber)){ ?><font color="blue">！編集モードです！<br></font><?php } ?>
		<!-フォームの内容->
		お名前：<input type="text" name="name" size="40" value = "<?php echo $EdiName; ?>" >
		<p>コメント：<textarea cols="30" rows="5" name="com1"><?php echo $EdiCom; ?></textarea>
		編集・削除用パスワード：<input type="password" name="pass"></p>
		<!-- //空欄かどうか -->
			<?php if(empty($name) && empty($comment) && empty($delNumber) && empty($ediNumber)){ ?>
				<font color="red">お名前・コメントか削除番号もしくは編集番号を入力してください</font><br>
			<?php } ?>
			<!-- //パスワードがあるか -->
			<?php if(empty($pass) && !empty($name) && !empty($comment)){ ?>
				<font color="red">パスワードを入力してください</font><br>
		<?php } ?>
		
		<!-送信ボタンをつくる->
		<p><input type="submit" value="送信する">
		<!-- 編集モードのとき、編集であるとわかるhiddenを送る -->
		<?php if(!empty($ediNumber)){ ?>
			<input type='hidden' name='doEdi' value="<?php echo $ediNumber; ?>"></p>
		<?php } ?>
		
		
		
		<!-編集フォームを表示->
		<form action="" method="post">
		<p>＊投稿編集<br>
		<!-フォームの内容->
		編集対象番号：<input type="text" name="edit_num" size="5">
		パスワード：<input type="password" name="pass_edi"></p>
		<!-- //編集したいけどパスがない -->
			<?php if(!empty($ediNumber) && empty($pass_edi)){ ?>
				<font color="red">パスワードを入力してください</font><br>
			<?php } ?>
		<!-- パスワードが間違っている -->
		<?php if(!empty($edi_error)){ ?>
				<font color="red"><?php echo $edi_error; ?></font><br>
		<?php } ?>
			
		<!-編集ボタンをつくるhiddenで->
		<input type="submit" value="編集する"></p>
	
	
	
		<!-削除フォームを表示->
		<form action="" method="post">
		<p>＊投稿削除<br>
		<!-フォームの内容->
		削除対象番号：<input type="text" name="delete_num" size="5">
		パスワード：<input type="password" name="pass_del"></p>
		<!-- //削除したいけどパスがない -->
			<?php if(!empty($delNumber) && empty($pass_del)){ ?>
				<font color="red">パスワードを入力してください</font><br>
			<?php } ?>
		<!-- パスワードが間違っている -->
		<?php if(!empty($del_error)){ ?>
				<font color="red"><?php echo $del_error; ?></font><br>
		<?php } ?>
		<!-削除ボタンをつくる->
		<input type="submit" value="削除する"></p>
		
		<hr>
		
		<?php //2-12応用 書き込み内容の表示
		$sql = 'SELECT * FROM utau ORDER BY id ASC;';
		$results = $pdo->query($sql); //実行・結果取得

		foreach($results as $row){
			//$rowの中にはテーブルのカラム名が入る
			echo "投稿番号:".$row['id'].' ';
			echo "お名前：".$row['name'].' ';
			echo "コメント：".$row['comment'].' ';
			echo "投稿時間：".$row['time'].'<br>';
		}
 ?>
		
		</body>
</html>