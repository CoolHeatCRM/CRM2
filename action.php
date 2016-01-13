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

}else if ($mode=="CreateTasks"){

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