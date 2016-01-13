<?php 
include_once 'Core.php';
HeadPrint();
?>

<form action="login.php" method="post">
Name: <input type="text" name="user"><br>
Password: <input type="password" name="pass"><br><input type="text" name="mode" value="0" hidden>
<input type="submit" value="Login">
</form>

</body></html>