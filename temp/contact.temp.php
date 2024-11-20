<?php require 'partials/header.php'; ?>
<div class="container my-5">
	
	<h2>Contact Us</h2>

	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	consequat.</p>
	<br/>
	<br/>
	<form method="POST" action="<?=url('form/submit');?>">
		<p><input type="text" name="name"></p>
		<p><textarea name="message"></textarea></p>
		<p><input type="submit" name="send"></p>
	</form>
</div>
<?php require 'partials/footer.php'; ?>