<?php
//Initial Setup
session_start();
$_SESSION["servername"]="localhost";
$_SESSION["Dusername"] = "web";
$_SESSION["Dpassword"] = "";
$_SESSION["dbname"] = "crm2";
$_SESSION["username"]=$_POST["user"];
$_SESSION["password"]=$_POST["pass"];
$_SESSION["idnum"]=0;
$_SESSION["MINI"]="0";
$_SESSION["DISPLAY"]="1";
//Grant access to Core
include_once 'Core.php';
//Print out the header
NoSessionHeadprint();
$conn=conDB();
$user=$_SESSION["username"];
$pass=$_SESSION["password"];
$_SESSION["idnum"]=login($user,$pass);
$AID=$_SESSION["idnum"];
printUI($AID,"0","0",FALSE);
//Print out the Tail section, possibly to be discontinued soon
TailPrint();
?>