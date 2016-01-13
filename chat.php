<meta http-equiv="refresh" content="15">
<?php
include_once 'Core.php';
chatHeadprint();
$AID=$_SESSION["idnum"];

echo "<div class='chatholder' id='log'>";
$chat=getChatLog($AID);
$chatN=count($chat);
for($x=$chatN-1;$x>-1;$x--){
	$message=$chat[$x];
	echo "$message<br>";
}
echo "</div>";
echo "<script>
var log = document.getElementById('log');
log.scrollTop = log.scrollHeight;
</script>";
?>