<?php
include_once 'Core.php';
chatHeadprint();
$AID=$_SESSION["idnum"];
if(isset($_POST["msg"])){
	$MSG=$_POST["msg"];
	$target=$_POST["Target"];
	if($target==0){
		AddtoChat($AID,$MSG,1,0);
	}else{
		AddtoChat($AID,$MSG,0,$target);
	}
}
echo "<chatsender>";
echo "<form action='chatSend.php' method='post'>
			<select name='Target'>
			<option value='0'>All</option>";
$Favorites=getChatFavorites($AID);
$FavoritesN=count($Favorites);
for($x=0;$x<$FavoritesN;$x++){
	echo "<option value='$Favorites[$x]'>";
	$x++;
	echo "$Favorites[$x]</option>";
}
echo"</select>
		<input type='text' name='msg' class='chatbox'>
		<input type='submit' value='Send' class='MediumButton'>
		</form>";
echo "</chatsender>";
?>