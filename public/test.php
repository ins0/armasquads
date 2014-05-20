<form method="post" action="test.php">
	<input type="submit" value="ButtonText" name="sent">
</form>
<?php 

print_R($_POST);
if(isset($_POST['sent'])){

	$text = $_POST['sent'];
	$dateiname = "meine.txt";
	$handler = fopen($dateiname , "a+");
	fwrite($handler , $text);
	fclose($handler);
	}
?>