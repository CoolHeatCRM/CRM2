<?php
echo "
	<html>
		<head>
			<title>CRM 2</title>
			<link rel='stylesheet' type='text/css' href='/Main.css'>
			<link rel='shortcut icon' href='icon.ico' />
		
		</head>
		<body>";
session_start();
$_SESSION["servername"]="localhost";
$_SESSION["Dusername"] = "web";
$_SESSION["Dpassword"] = "";
$_SESSION["dbname"] = "crm2";
$_SESSION["idnum"]=0;
$_SESSION["MINI"]="0";
$_SESSION["DISPLAY"]="1";
$conn = new mysqli($_SESSION["servername"], $_SESSION["Dusername"], $_SESSION["Dpassword"],$_SESSION["dbname"]);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST["Submit"])){
	$pnumConfirm=$_POST["Pnumconfirm"];
	$Name=$_POST["Name"];
	$Address=$_POST["Address"];
	$Pcode=$_POST["Pcode"];
	$num=$_POST["cusPnum"];
	$cusID=$_POST["cusID"];
	if($num==$pnumConfirm){
		$names=explode(" ",$Name,1);
		$fn=$names[0];
		$ln=$names[1];
		$locations=explode(", ON ",$Address);
		$locaitons2=explode("\r\n",$locations,-1);
		$pcode=$locations[1];
		$add=$locations2[0];

		$sql="INSERT INTO cusfields (IndexID,CusID) VALUES ('2','$fn'),('3','$ln'),('5','$add'),('6','$pcode')";
		mysqli_query($conn, $sql);
	}else{
		echo "Didnt match current Pnum, so scrapping it!";
		$sql="INSERT INTO cusfields (IndexID,CusID,Value) VALUES ('3','$cusID','Unlisted')";
		mysqli_query($conn, $sql);
	}
}


$sql="SELECT IDKey FROM customers WHERE  IDKey NOT IN (SELECT CusID FROM cusfields WHERE IndexID='3' AND IDKey>'17570000') LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = $result->fetch_assoc();
$cusID=$row["IDKey"];
$sql="SELECT * FROM cusfields WHERE IndexID='1' AND CusID='$cusID' LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = $result->fetch_assoc();
$cusPnum=$row["Value"];
echo "<a href='http://www.whitepages.com/phone/1-$cusPnum' target='_blank'>$cusPnum</a>";
echo "<form action='monkey.php' method='post'><br>
		<input type='text' value='$cusPnum' name='cusPnum' hidden>
		<input type='text' value='$cusID' name='cusID' hidden>
		<input type='text' value='' name='Pnumconfirm'><br><br>
		<input type='text' value='' name='Name'><br><br>
		<input type='text' value='' name='Address'><br><br>
		<input type='text' value='' name='Pcode'><br><br>
		<input type='submit' value='Submit' name='Submit'>
		</form>";

?>