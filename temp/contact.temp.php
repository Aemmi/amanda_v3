<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?=$title ?? '';?></title>
	</head>
	<body>
	
		<h2>Contact Us</h2>

		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
		tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
		quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
		consequat.</p>
		<br/>
		<br/>
		<form method="POST" action="<?=url('contact');?>">
			<p><input type="text" name="fname"></p>
			<p><textarea name="message"></textarea></p>
			<p><input type="submit" name="send"></p>
		</form>

	</body>
</html>