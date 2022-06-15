<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Form</title>
</head>
<body>
<?php 
if(isset($data)){
	//print_r($data);
	foreach($data as $rec){
		echo $rec['id']." -".$rec['post_title'];
	}
}
if(isset($_GET['me'])){
	echo '<p>'.$_GET['me'].'</p>';
}
?>
<form method="post" action="/amanda/form">
	<p><input type="text" name="name"></p>
	<p><input type="submit" name="send"></p>
</form>

</body>
</html>