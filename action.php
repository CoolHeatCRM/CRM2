<?php
include_once 'Core.php';
Headprint();

$AID=$_SESSION["idnum"];
$mode=$_POST["mode"];
$TID=0;
$MINI=$_SESSION["MINI"];
$DISPLAY=$_SESSION["DISPLAY"];

if ($mode=="ViewTask"){
	$TID=$_POST["TID"];
}
else if ($mode=="ViewTasklist"){
}
else if ($mode=="NextTasks"){
	
	$TID=getNextTask($AID);
	printSmartTaskUI($TID,$AID);
}else if ($mode=="CreateTasks"){
	
}
else if($mode=="CreateTemplate"){
	$create=$_POST["create"];
	if($create==1){
		$Req=$_POST["Req[]"];
		$Fields=$_POST["Use[]"];
		$Name=$_POST["Name"];
		$Desc=$_POST["Desc"];
		$Perm=$_POST["Perm"];
		createTaskTemplate($Name,$Desc,$Fields,$Req,$Perm);
		echo"<div class='confirmation'>A Template named $Name<br>Has been created successfully!</div>";
	}
}
else if ($mode=="AssignTasks"){
	$TIDS=$_POST["TIDS"];
	assignTasks($AID, $TIDS);
}
else if ($mode=="Search"){
	$TID=$_POST["Terms"];
}
else if ($mode=="Account"){

}else if($mode=="MINI") {
	if($MINI=="1"){
		$MINI="0";
	}else{
		$MINI="1";
	}
	$_SESSION["MINI"]=$MINI;
	$_SESSION["DISPLAY"]="1";
	$DISPLAY="1";
	$mode=0;
}else if ($mode=="DISPLAY"){
	if($DISPLAY=="1"){
		$DISPLAY="0";
		$MINI="2";
	}else{
		$DISPLAY="1";
	}
	$_SESSION["DISPLAY"]=$DISPLAY;
	$mode=0;
}
printUI($AID,$mode,$TID,$MINI);
TailPrint();
?>