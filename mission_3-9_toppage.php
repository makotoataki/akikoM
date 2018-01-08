<?php

header('Content-Type: text/html; charset=UTF-8');

//データベースへの接続
$dsn = ****;
$user = ****;
$password = ****;
$pdo = new PDO($dsn,$user,$password);

session_start();
	// ログインしているときヘッダーに
if (!empty($_SESSION["name"])) {
		$wellcame="ようこそ  ".$_SESSION["name"]."  さん!";
	}
session_start();
	// ログインしていなければlogin.phpに遷移
	if (empty($_SESSION["name"])) {
	header('Location: /login_mail.php');
	exit();
	}

//変数受け取り
$send= $_POST['send'];
$name= $_SESSION["name"];
$pass= $_SESSION["pass"];
$event_name = $_POST['event_name'];
$event_date = $_POST['event_date'];
$event_con = $_POST['event_con'];

$intime = date("Y/m/d H:i");

$toukouNum = $_POST['toukouNum'];

$doEdi = $_POST['doEdi'];
$edit = $_POST['edit'];

$ediNumber = $_POST['edit_num'];
$delNumber = $_POST['delete_num'];
$pass_edi = $_POST['pass_edi'];
$pass_del = $_POST['pass_del'];

$contents=file_get_contents($each_event_page.php);
$tbcontents=file_get_contents($create_event_tb.php)


//デバッグ
//echo $pass;


//書き込み---------------------------------------------------------------------------------
//名前・コメント・パスがあるとき書き込み
if(!empty($event_name) && !empty($event_date) && !empty($event_con) && empty($delNumber) && empty($doEdi)){

	//PDOでINSETを利用してカラムに値を代入する
	$sql = $pdo -> prepare("INSERT INTO event_tb (name,pass,event_name,event_date,event_con,toukoujikan) VALUES(:name,:pass,:event_name,:event_date,:event_con,:toukoujikan)");

	$sql->bindParam(':name',$name,PDO::PARAM_STR);
	$sql->bindParam(':pass',$pass,PDO::PARAM_STR);
	$sql->bindParam(':event_name',$event_name,PDO::PARAM_STR);
	$sql->bindParam(':event_date',$event_date,PDO::PARAM_STR);
	$sql->bindParam(':event_con',$event_con,PDO::PARAM_STR);
	$sql->bindValue(':toukoujikan',$intime,PDO::PARAM_STR);

	$sql->execute();
	
}

//ファイルアップロード------------------------------------------------------------------------

	//デバッグ
	//echo "FILS upfile|"; var_dump($_FILES['upfile']); echo "|";

if (isset($_FILES['upfile']['tmp_name']) && !empty($toukouNum)){
	
	$raw_data = file_get_contents($_FILES['upfile']['tmp_name']);
	
	//拡張子を格納
	$tmp = $_FILES['upfile']['type'];
	
	//デバッグ
	//echo "tmp|".$_FILES['upfile']['type']."|<hr/>";
	
	if($tmp == "image/jpeg" || $tmp == "image/png" || $tmp == "image/gif" || $tmp == "video/mp4"){
		//DBに格納するファイルネーム設定
		//サーバー側の一時的なファイルネームと結合
		$date = getdate();
		$fname = $_FILES['upfile']['tmp_name'];

		//画像・動画をDBに格納．
			$sql = "INSERT INTO upfile(name, file, mime, username, toukouNum) VALUES (:name, :file, :mime, :username, :toukouNum);";
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':name',$fname, PDO::PARAM_STR);
		$stmt -> bindValue(':file',$raw_data, PDO::PARAM_STR);
		$stmt -> bindValue(':mime',$tmp, PDO::PARAM_STR);
		$stmt -> bindValue(':username',$name, PDO::PARAM_STR);
		$stmt -> bindValue(':toukouNum',$toukouNum, PDO::PARAM_STR);
		$stmt -> execute();

	}else{
		echo "非対応ファイルです<br/>";
		exit(1);
	}

}
	
if(isset($_FILES['upfile']['tmp_name']) && empty($toukouNum)){
	$filenum_error = "イベント番号を入力してください";
}

//編集機能---------------------------------------------------------------------------------

if(!empty($edit)){
//編集番号とパスワードがあるとき、編集する名前とコメントを取得する
if(!empty($ediNumber) && !empty($pass_edi)){
	//編集番号をidに
	$id = $_POST['edit_num'];
	//編集する内容を受け取る whereで探す
	$stmt =	$pdo->prepare("select name, pass, event_name, event_date, event_con from event_tb where id = :id");
	$stmt -> bindParam(':id',$id, PDO::PARAM_INT);
	$stmt -> execute();
	
	//$stmtの中の上で検索した一行をfetchで抜き出し、変数に格納
	$result = $stmt->fetch();
	
		$judgepass=$result['pass'];
		$judgename=$result['name'];
	
//編集パスワードがあっているならDBにある名前・コメントを変数に格納する
	if($pass_edi == $judgepass && $_SESSION["name"] == $judgename){
		$EdiName = $result['event_name'];
		$EdiDate = $result['event_date'];
		$EdiCon = $result['event_con'];
	}
	if($pass_edi !== $judgepass){
			$edi_error = "パスワードが間違っています";
			$ediNumber = "";
		}
		if($_SESSION["name"] !== $judgename){
			$edi_error2 = "ご自分の投稿以外は編集できません";
			$ediNumber = "";
	}
}
}

//名前・コメント・hiddenがある
if(!empty($doEdi)){
	//2-13
	//idを変数に入れる
	$id = $doEdi;
	//書き換える内容を変数に入れる	
	$edi_name = $event_name;
	$edi_date = $event_date;
	$edi_con = $event_con;

	//idを検索し、updateで変数入れ替える
	$sql = "update event_tb set event_name='$edi_name',event_date='$edi_date',event_con='$edi_con' where id = '$id';";

	$result = $pdo->query($sql);
}


//削除機能---------------------------------------------------------------------------------

//削除番号とパスワードが送信された
if(!empty($delNumber) && !empty($pass_del)){
	//削除番号と一致するidの行を抜き出す
	$id = $delNumber;
	$stmt =	$pdo->prepare("select name, pass, event_name, event_date, event_con from event_tb where id = :id");
	$stmt -> bindParam(':id',$id, PDO::PARAM_INT);
	$stmt -> execute();
	
	//$stmtの中の上で検索した一行をfetchで抜き出し、変数に格納
	$result = $stmt->fetch();
	
	if($pass_del == $result['pass'] && $_SESSION["name"] == $result['name']){
		$id = $delNumber;
		$sql = "delete from event_tb where id ='$id';";
		$result = $pdo->query($sql);
		
	}
	if($pass_del !== $result['pass']){
		$del_error = "パスワードが間違っています";
	}
	if($_SESSION["name"] == $result['name']){
		$del_error2 = "ご自分の投稿以外は削除できません";
	}
}

?>

<!DOCTYPE html>
<html>
		<head>
			<meta charset="utf-8">
			<link rel="stylesheet" href="/toppage.css">
			<title>このゆびとまれ！：イベント企画</title>
		</head>

<body>

<div class="midashi">
	<div class="heading">このゆびとまれ！</div>
</div>
		
		<div class ="h2">
<!- 名前表示 ->
<?php echo $wellcame; ?>   
<!-- ログアウトリンク -->
<a href="http://co-361.it.99sv-coco.com/logout.php" class="square_btn">ログアウト</a>
		</div>
		
<div class = "h1">
		
		<!-入力フォームを表示->
		<form action="" method="post">
		<p><div class ="t1">■イベント企画■</div><br><?php if(!empty($ediNumber)){ ?><font color="orange">＊編集モードです＊<br></font><?php } ?>
		<!-フォームの内容->
		イベント名：<input type="text" name="event_name" value = "<?php echo $EdiName; ?>" >
		イベント日時・期間：<input type="text" name="event_date" value = "<?php echo $EdiDate; ?>" >
		<p>イベント概要：<textarea cols="30" rows="5" name="event_con"><?php echo $EdiCon; ?></textarea>
		<!-送信ボタンをつくる->
		<input type="submit" value="企画する！">
		<!-- 編集モードのとき、編集であるとわかるhiddenを送る -->
		<?php if(!empty($ediNumber)){ ?>
			<input type='hidden' name='doEdi' value="<?php echo $ediNumber; ?>"></p>
		<?php } ?>
				<!-- //空欄かどうか -->
			<?php if(!empty($send) && empty($comment) && empty($delNumber) && empty($ediNumber)){ ?>
				<font color="red">お名前・コメントか削除番号もしくは編集番号を入力してください</font><br>
			<?php } ?>
			<!-- //パスワードがあるか -->
			<?php if(empty($pass) && !empty($name) && !empty($comment)){ ?>
				<font color="red">パスワードを入力してください</font><br>
		<?php } ?>
		</form>
		
		
		<!-- ファイルアップロード -->
		<p>
		<form action="" enctype="multipart/form-data" method="post">
		<div class="t1">■画像・動画アップロード■</div><br>
			イベント番号：<input type="text" name="toukouNum" size="5">
			<input type='file' name='upfile'>
			<input type="submit" value="アップロード">
		<br>※画像はjpeg/png/gif、動画はmp4方式のみ対応しています<br>
		※ひとつのイベントにつき1ファイルのみアップロードできます
		</form>
		<font color="red"><?php echo $filenum_error; ?></font><br>
		</p>

		
		<!-編集フォームを表示->
		<form action="" method="post">
		<p><div class ="t1">＊投稿編集＊</div><br>
		<!-フォームの内容->
		編集対象番号：<input type="text" name="edit_num" size="5">
		パスワード：<input type="password" name="pass_edi">
		<!-編集ボタンをつくるhiddenで->
		<input type="submit" name='edit' value="編集する"></p>
		
		<!-- //編集したいけどパスがない -->
			<?php if(!empty($ediNumber) && empty($pass_edi)){ ?>
				<font color="red">パスワードを入力してください</font><br>
			<?php } ?>
		<!-- パスワードが間違っている -->
				<font color="red"><?php echo $edi_error; ?><?php echo $edi_error2; ?></font><br>
</form>
	
		<!-削除フォームを表示->
		<form action="" method="post">
		<p><div class ="t1">＊投稿削除＊</div><br>
		<!-フォームの内容->
		削除対象番号：<input type="text" name="delete_num" size="5">
		パスワード：<input type="password" name="pass_del">
			<!-削除ボタンをつくる->
		<input type="submit" value="削除する"></p>		
	
		<!-- //削除したいけどパスがない -->
			<?php if(!empty($delNumber) && empty($pass_del)){ ?>
		<font color="red">パスワードを入力してください</font><br>
		<?php } ?>
		<!-- パスワードが間違っている -->
		<?php if(!empty($del_error)){ ?>
				<font color="red"><?php echo $del_error; ?><?php echo $del_error2; ?></font><br>
		<?php } ?>
		</form>
		



		<?php //2-12応用 書き込み内容の表示
		$sql = 'SELECT * FROM event_tb ORDER BY id ASC;';
		$results = $pdo->query($sql); //実行・結果取得

		foreach($results as $row){
			echo '<br>--------------------------------------------------------------------------------------------------------------------------------<br>';
			//$rowの中にはテーブルのカラム名が入る
			echo '<div class="t1">'.$row['id'].'  ';
			echo $row['event_name'].' </div>';
			echo "主催者：".$row['name'].'<br>';
			echo "イベント日時：".$row['event_date'].'<br>';
			echo "イベント概要：".$row['event_con'].'<br>';
			
			//idと一致する画像・動画を取得して表示する
			$stmt =	$pdo->prepare("SELECT * FROM upfile where toukouNum = :id");
			$stmt -> bindParam(':id', $row['id'], PDO::PARAM_INT);
			$stmt -> execute();
			
			$media = $stmt->fetch();

			//動画と画像で場合分け
			$target = $media['name'];
			
			//デバッグ
			//echo "taeget|".$target."|";
			
			if($media["mime"] == "video/mp4"){
				echo "<br><video src='/import_file.php?target=$target' width='426' height='240' controls></video><br>";
			}
				elseif($media["mime"] == "image/jpeg" || $media["mime"] == "image/png" || $media["mime"] == "image/gif"){
					echo "<br><img src='/import_file.php?target=$target' width='300' height='200'></img><br>";
				}
			
		}
		?>
		

		
		</div>
		</body>
</html>