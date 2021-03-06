<?php
/* Appendix
 * $AID used to identify an agent, generally the current user
 * $CID used to identify a customer
 * $CFID used to identify a field attached to a customer, such as a Name, Phone Number or Address, or the price of a quote
 * $TID used to identify a task such as an appointment
 * $TFIDS used to identify a field attached to an assinment, such as the result of a booking, or the current state of an install
 * $IID identifies an interface
 * $ITD identifies an Item Template IDKey
 * $UID identifies a unit such as Grams, Boxes or Feet that are used to define the measurement of an Item
 * $CAT identifies a Category of items such as Gas Lines, Couches, Furnaces or Breakfast foods, every Item has an associated Category
 * $LID identifies a location
 * $PID used to identify a Particular Permission
 * $VID used to identify an Invoice
 * $CRID used to identify a Crew
 * $CIND used to identify a cusindex, these sort customer fields into their appropriate types for quick searching
 * $TEID used to identify a Task Template
 * $FID used to identify a Task Template Field from the fields table.
 */
//Set up functions
function Headprint(){
	echo "<html>
		<head>
			<title>CRM 2</title>
			<link rel='stylesheet' type='text/css' href='/Main.css'>
			<link rel='shortcut icon' href='icon.ico' />
			
		</head>
		<body>";
	session_start();
}
function chatHeadprint(){
	echo "<html>
		<head>
			<title>CRM 2</title>
			<link rel='stylesheet' type='text/css' href='/chat.css'>
		</head>
		<body>";
	session_start();
}
function NoSessionHeadprint(){
	echo "<html>
		<head>
			<title>CRM 2</title>
			<link rel='stylesheet' type='text/css' href='/Main.css'>
			<link rel='shortcut icon' href='/icon.ico' />
			
		</head>
		<body>";
}
function TailPrint(){
	echo "<div class='logout'>
			<form action='action.php' method='post'><input type='text' name='mode' value='0' hidden>
			<input type='submit' value='H' class='MediumButton'></form>
			</div>";
	echo "<div class='Mini'>
			<form action='action.php' method='post'><input type='submit' value='M' class='MediumButton'>
			<input type='text' name='mode' value='MINI' hidden></form></div>
			";
	echo "<div class='Close'>
			<form action='action.php' method='post'><input type='submit' value='C' class='MediumButton'>
			<input type='text' name='mode' value='DISPLAY' hidden></form>
			</div>";
	echo "<div class='Search'>
			<form action='action.php' method='post'>
			<input type='text' name='Terms' class='TailSearchBar'>
			<input type='submit' value='S' class='MediumButton'>
			<input type='text' name='mode' value='Search' hidden></form>
			</div>
			";
	echo "</body></html>";
}
function conDB(){
	$conn = new mysqli($_SESSION["servername"], $_SESSION["Dusername"], $_SESSION["Dpassword"],$_SESSION["dbname"]);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	return $conn;
}
function login($user,$pass){
	$conn=conDB();
	$sql="SELECT * FROM agents WHERE Username='$user' AND Password='$pass' AND Active='1'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	return $row["IDKey"];
}
function printSmartTaskUI($TID,$AID){
	$tasksfields=getTaskFieldsC($TID,$AID);
	$fieldsN=count($tasksfields);
	echo "<form action='action.php' method='post'>";
	echo "<iput type='number' value='$fieldsN/6' name='totalFields' hidden>";
	for($x=0;$x<$fieldsN;$x=$x+6){
		$x1=$x+1;
		$x2=$x+2;
		$x3=$x+3;
		$x4=$x+4;
		$x5=$x+5;
		echo "<br><input type='number' value='$tasksfields[$x5]' name='fieldID[]' hidden>";
		if($tasksfields[$x]){
			echo "$tasksfields[$x2]: ";
			if($tasksfields[$x1]){
				echo "<input type='$taskfields[$x3]' value='$tasksfields[$x4]' name='fields[]'>";
			}else{
				echo "<input type='$taskfields[$x3]' value='$tasksfields[$x4]' name='fields[]' disabled>";
			}
		}
		else{
			echo "<input type='text' value='PERMERROR' name='fields[]'>Unknown";
		}
	}
	echo "<input type='text' value='SubmitTask' name='mode' hidden><input type='submit' value='Submit'></form>";
}
function printListofTasks($AID){
	$tasks=getTasks($AID);
	$tasksN=count($tasks);
	echo "<form action='action.php' method='post'>";
	for($x=0;$x<$tasksN;$x=$x+4){
		//$x ==Task IDKey
		$x1=$x+1;//==Name
		$x2=$x1+1;//==Start Date
		$x3=$x2+1;//End Date
		echo "<button type='submit' name='TID' value='$tasks[$x]'>$tasks[$x1]</button>
		<input type='check' value='$tasks[$x]' name='TIDS[]' hidden>";
	}
	echo "<button type='submit' name='mode' value='assign' class='MediumButton'>Assign Tasks</button>";
	echo "</form>";
}
function printlistofCustomers($Terms,$AID){
	$array=searchCustomers($Terms);
	$arrayN=count($array);
	$Viewable=getPermittedIndexV($AID);
	$Editable=getPermittedIndexE($AID);
	$EN=count($Editable);
	$VN=count($Viewable);
	echo "<div class='SearchResults'>";
	echo "<table><tr>";
	for($v=0;$v<$VN;$v++){
		$v++;
		echo "<td>$Viewable[$v]</td>";
	}
	for($x=0;$x<$arrayN;$x++){
		$CusID=$array[$x];
		$sql="SELECT * FROM cusfields INNER JOIN cusindex ON cusfields.IndexID=cusindex.IDKey WHERE cusfields.CusID='$array[$x]'";
		$result = mysqli_query($conn, $sql);
		while($row = $result->fetch_assoc()){
			$indexID=$row["IndexID"];
			$view=0;
			for($x=0;$x<$EN;$x++){
				if($indexID=$Editable[$x]){
					$view=2;
					$x=$EN;
				}
			}
			if($view==0){
				for($x=0;$x<$VN;$x++){
					if($indexID=$Viewable[$x]){
						$view=1;
						$x=$VN;
					}
				}
			}
			if($view>0){
				
			}
		}
	}
	echo "</div>";
}
function printUI($AID,$mode,$TID,$MINI){
	echo "<div class='MainWindow'>";	
		echo "<div class='MenuButtons'>";
		if($mode=="0"){
			printTasksButtons();
		}else if ($mode=="ViewTask"){
			printSmartTaskUI($TID,$AID);
		}else if ($mode=="ViewTasklist"){
			printListofTasks($AID);
		}else if ($mode=="Search"){
			printlistofCustomers($TID,$AID);
		}else if ($mode=="CreateTemplate"){
			printTemplateMaker($AID,$MINI);
		}else if($mode=="CreateTasks"){
			
		}
		echo "</div>";//Closes MenuButtons
		if($MINI=="X"){
			echo "<div class='Invis'>";
		}
		if($MINI=="1"||$MINI=="0"){
			printchat($AID,$MINI);
			if($MINI=="1"){
				echo "<div class='minibottom'>";
			}else if($MINI=="0"){
				echo "<div class='bottom'>";
			}
			
			printCalendar($AID);
			echo "</div>";
		}
		echo "</div>";
	echo "</div>";//Closes MainWindow
}
function printTaskUI($AID){
	echo "<div class='MainWindow'>";
	printchat($AID);
	echo "<div class='MenuButtons'>";
	printTaskInterface();
	echo "</div>";//Closes MenuButtons
	echo "<div class='bottom'>";
	printCalendar($AID);
	echo "</div>";
	echo "</div>";//Closes MainWindow
}
function printchat($AID,$MINI){
	if($MINI){
		echo "<minichat>";//Creates the Chat positioner
	}else {
		echo "<chat>";//Creates the Chat positioner
	}
	
		echo "<iframe src='chat.php' class='chatframe' id='chatframe' frameBorder='0' marginwidth='0' marginheight='0'></iframe>";
		echo "<iframe src='chatSend.php' class='chatsendFrame' id='chatframe' frameBorder='0' marginwidth='0' marginheight='0'></iframe>";
	if($MINI){
		echo "</minichat>";//Closes the Chat positioner
	}else {
		echo "</chat>";//Closes the Chat positioner
	}
		
}
function printTasksButtons(){
	echo "<table class='MainTable'>
			<tr>
				<td><form action='action.php' method='post'><input type='submit' value='View Tasks' class='BigButton'><input type='text' name='mode' value='ViewTasklist' hidden></form></td>
				<td><form action='action.php' method='post'><input type='submit' value='Next Task' class='BigButton'><input type='text' name='mode' value='NextTasks' hidden></form></td>
			</tr>
			<tr>
				<td><form action='action.php' method='post'><input type='submit' value='Create Task' class='BigButton'><input type='text' name='mode' value='CreateTasks' hidden></form></td>
				<td><form action='action.php' method='post'><input type='submit' value='Create Template' class='BigButton'><input type='text' name='mode' value='CreateTemplate' hidden><input type='text' value='0' name='create' hidden></form></td>
			</tr>
			<tr>
				<td><form action='action.php' method='post'><input type='submit' value='Account/Stats' class='BigButton'><input type='text' name='mode' value='Account' hidden></form></td>
			</tr>
		</table>
		";
}
function printCalendar($AID){
	echo "<table class='CalendarTable'>";
		echo "<tr>";
		echo "<td class='CalendarHead'><b>Mon</td>";
		echo "<td class='CalendarHead'><b>Tue</td>";
		echo "<td class='CalendarHead'><b>Wed</td>";
		echo "<td class='CalendarHead'><b>Thu</td>";
		echo "<td class='CalendarHead'><b>Fri</td>";
		echo "</tr>";
		echo "<tr>";
		$today=0;
		$today-=getTodayinWeek()-1;
		$workingDate=getRelativeDate($today);
		for($x=0;$x<5;$x++){
			$workingDate=getRelativeDate($today+$x);
			$array=getTodaysTask($AID,$workingDate);
			$arrayN=count($array)/3;
			echo "<td class='CalendarBody'>";
			for($v=0;$v<$arrayN;$v=$v++){
				$z=$v*3;
				$CusN=$array[$z+2];
				$task=$array[$z+1];
				$taskN=$array[$z];
				echo "<form action='action.php' method='post'><input type='submit' value='$taskN $CusN'><input type='text' name='mode' value='ViewTask' hidden><input type='text' name='TID' value='$task' hidden></form>";
			}
			echo "</td>";
		}
		echo "</tr>";
		echo "<tr>";
		for($x=7;$x<12;$x++){
			$workingDate=getRelativeDate($today+$x);
			$array=getTodaysTask($AID,$workingDate);
			$arrayN=count($array)/3;
			echo "<td class='CalendarBody'>";
			for($v=0;$v<$arrayN;$v=$v++){
				$z=$v*3;
				$CusN=$array[$z+2];
				$task=$array[$z+1];
				$taskN=$array[$z];
				echo "<form action='action.php' method='post'><input type='submit' value='$taskN $CusN'><input type='text' name='mode' value='ViewTask' hidden><input type='text' name='TID' value='$task' hidden></form>";
			}
			echo "</td>";
		}
		echo "</tr>";
	echo "</table>";
}
function printTemplateMaker($Aid,$MINI){
	$Perms=getListOfPermissions();
	$permsN=count($Perms);
	echo "<form action='action.php' method='post'>";
	echo "
			<input type='text' value='CreateTemplate' name='mode' hidden>
			<input type='text' value='1' name='create' hidden>
			<div>";
		echo "<input type='text' name='Name' placeholder='Name'><input type='submit' value='Create' class='MediumButton'>";
	echo"</div>";
	echo "<div>";
		echo "<input type='text' name='Desc' placeholder='Description'>";
		echo "<select name='Perm'>";
		for($x=0;$x<$permsN;$x=$x+2){
			$x2=$x+1;
			echo "<option value='$Perms[$x]'>$Perms[$x2]</option>";
		}
		echo"</select>";
	echo"</div>";
	if($MINI=="1"){
		echo "<table class='TemplateTableS'>";
	}else{
		echo "<table class='TemplateTable'>";
	}
	echo "<tr>";
	echo "<td title='Whether to Require this Field'>Req</td><td title='Whether to Add this Field to the Template'>Use</td><td>Field</td>";
	echo "</tr>";
	$fields=getFieldIndex();
	$fieldsN=count($fields);
	for($v=0;$v<$fieldsN;$v++){
		$ID=$fields[$v];
		$v++;
		$Name=$fields[$v];
		echo "<tr><td title='Whether to Require this Field'><input type='checkbox' name='Req[]' value='$ID'></td>
				<td title='Whether to Add this Field to the Template'><input type='checkbox' name='Use[]' value='$ID'></td>
				<td>$Name</td>
			</tr>";
	}
	echo"</table>
	</form>";
}
function printTaskMaker($AID){
	echo "<form action='action.php' method='post'>";
	echo "<select name='TEID'>";
	
	echo"</select>";
	echo"</form>";
}
//Inventory Functions
//Creates a new Item Template
function CreateItem($desc,$CAT,$UID,$Price,$Stock){
	$conc=conDB();
	$sql="SELECT * FROM items WHERE Description='$desc'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) ==0) {
		$sql2="INSERT INTO items (Description,CatID,UnitID,Price) VALUES ('$desc','$CAT','$UID','$Stock')";
		mysqli_query($conn, $sql);
	}
}
//Adds a Category type
function CreateCategory($SYM,$Desc){
	$conn=conDB();
	$sql="INSERT INTO categories (Symbol,Description) VALUES ('$SYM','$Desc')";
	mysqli_query($conn, $sql);
}
//Adds a Unit type 
function CreateUnit($SYM,$Desc){
	$conn=conDB();
	$sql="INSERT INTO units (Symbol,Description) VALUES ('$SYM','$Desc')";
	mysqli_query($conn, $sql);
}
//Sets the Category of an Item to the Input
function setCategory($ITD,$CAT){
	$conn=conDB();
	$sql="UPDATE items SET CatID='$CAT' WHERE IDKey='$ITD'";
	mysqli_query($conn, $sql);
}
//Sets the Unit of Measurement of an Item to the Input
function setUnit($ITD,$Unit){
	$conn=conDB();
	$sql="UPDATE items SET UnitID='$Unit' WHERE IDKey='$ITD'";
	mysqli_query($conn, $sql);
}
//Takes in an Item ID, a Location ID and a quantity and will add stock to the appropriate Stock Pile or Create a new one if it's not found.
function AddtoStock($ITD,$quantity,$LID,$Price){
	$conn=conDB();
	$Cstock=getStock($ITD,$LID);
	if($Cstock=="None"){
		$sql="INSERT INTO stock (ItemID,LocID,Quantity) VALUES ('$ITD','$LID','$quantity')";
		mysqli_query($conn, $sql);
	}
	else{
		$Fstock=$Cstock+$quantity;
		$sql="UPDATE stock SET Quantity='$Fstock' WHERE ItemID='$ITD' AND LocID='$LID'";
		mysqli_query($conn, $sql);
	}
	$AID=getID();
	$name=getAgentName($AID);
	addToStockLog('1',$LID,$quantity,"$name successfully added $quantity to a stockpile",$ITD);
	$newprice=calculatePrice($ITD,$Price,$quantity);
	setPrice($ITD,$newprice);
}
//Takes in an Item ID a Location ID and a quantity and attempts to remove the items from the appropriate stockpile, or returns STOP if it cant
function RemoveStock($ITD,$quantity,$LID){
	$conn=conDB();
	$Cstock=getStock($ITD,$LID);
	$AID=getID();
	$name=getAgentName($AID);
	if($Cstock!="None"){
		if($Cstock>$quantity){
			$Fstock=$Cstock-$quantity;
			$sql="UPDATE stock SET Quantity='$Fstock' WHERE ItemID='$ITD' AND LocID='$LID'";
			mysqli_query($conn, $sql);
			addToStockLog($LID,'1',$quantity,"$name successfully removed $quantity from a stockpile",$ITD);
			return "DONE";
		}
	}
	addToStockLog($LID,'1',$quantity,"$name failed to remove items from a stockpile",$ITD);
	return "STOP";
}
//Attempts to Move stock from LID1 to LID2
function MoveStock($ITD,$LID1,$LID2,$quantity){
	$REM=RemoveStock($ITD,$LID1,$quantity);
	$AID=getID();
	$name=getAgentName($AID);
	addToStockLog($LID1,$LID2,$quantity,"$name Attempted to move items between Stockpiles",$ITD);
	if($REM="DONE"){
		AddtoStock($ITD,$LID2,$quantity);
		
	}
	
}
//Creates an Audit Record
function CreateAudit($ITD,$LID,$quant,$AUDNUM){
	$conn=conDB();
	$id=getID();
	$sql="INSERT INTO audits (ItemID,LocID,Quantity,AuditNUM,AgentID,Active) VALUES ('$ITD','$LID','$quant','$AUDNUM','$id','1')";
	mysqli_query($conn, $sql);
}
//Sets an Audits Active flag to 0, so it will not be included in final audits and views
function DeactivateAudit($AUID){
	$conn=conDB();
	$sql="UPDATE audits SET Active='0' WHERE IDKey='$AUID'";
	mysqli_query($conn, $sql);
}
function setCrew($AID,$CRID,$Reason){
	$conn=conDB();
	$sql="UPDATE agents SET CrewID='$CRID' WHERE IDKey='$AID'";
	mysqli_query($conn, $sql);
	addToCrewLog($AID,$CRID,$Reason);
}

//Logging Functions
function addToStockLog($LID1,$LID2,$Quantity,$Desc,$ITD){
	$conn=conDB();
	$AID=getID();
	$date=getDateStamp();
	$sql="INSERT INTO stocklog (AgentID,ItemID,LocID,LocID2,Quantity,Date,Description) VALUES ('$AID','$ITD','$LID1','$LID2','$Quantity','$date','$Desc')";
	mysqli_query($conn, $sql);
}
function addToCrewLog($AID,$CRID,$Reason){
	$conn=conDB();
	$id=getID();
	$sql="INSERT INTO crewlog (AgentID,Date,ChangedBy,Reason,CrewID) VALUES ('$AID','$date','$id','$Reason','$CRID')";
	mysqli_query($conn, $sql);
}

//UI functions
function printUI2($AID){
	$IIDS=getInterfaces($AID);
	$IIDSN=count($IIDS);
	for($x=0;$x<IIDSN;$x++){
		$Interface=getInterface($IIDS[$x]);
		printInterface($Interface);
	}
}

function printInterface($Interface){
	echo "<Interface>";
	echo $Interface[0];
	echo $Interface[1];
	echo $Interface[2];
	echo $Interface[3];
	echo $Interface[4];
	echo "</Interface>";
}
function AddtoChat($AID,$MSG,$GLO,$TAID){
	$conn=conDB();
	$date=getTimeStamp();
	$sql="INSERT INTO chat (AgentID,Message,TimeStamp,Global,TargetAID) VALUES ('$AID','$MSG','$date','$GLO','$TAID')";
	mysqli_query($conn, $sql);
}

//Admin Functions
function createPKEY($POWID,$PID){
	$conn=conDB();
	$sql="INSERT INTO pkeys (PowID,PerID) VALUES ('$POWID','$PID')";
	mysqli_query($conn, $sql);
}
function createpower($Name,$Desc,$IID){
	$conn=conDB();
	$sql="INSERT INTO powers (Name,Description,InterfaceID) VALUES ('$Name','$Desc','$IID')";
	mysqli_query($conn, $sql);
}
function createInterface($L1,$L2,$L3,$L4,$L5){
	$conn=conDB();
	$sql="INSERT INTO interfaces (Line1,Line2,Line3,Line4,Line5) VALUES ('$L1','$L2','$L3','$L4','$L5')";
	mysqli_query($conn, $sql);
}

function createPermission($GrantBy,$Desc){
	$conn=conDB();
	$sql="INSERT INTO permissions (GrantableBy,Description) VALUES ('$GrantBy','$Desc')";
	mysqli_query($conn, $sql);
}
function GrantPermission($AID,$PID){
	$conn=conDB();
	$CID=getID();
	$canGrant=getGrantable($CID,$PID);
	if($canGrant=="TRUE"){
		$sql="INSERT INTO agentpermissions (AgentID,PermissionGranted) VALUES ('$AID','$PID')";
		mysqli_query($conn, $sql);
	}
}
function RemovePermission($AID,$PID){
	$conn=conDB();
	$CID=getID();
	$canGrant=getGrantable($CID,$PID);
	if($canGrant=="TRUE"){
		$sql="UPDATE agentpermissions SET PermissionGranted='0' WHERE AgentID='$AID' AND PermissionGranted='$PID'";
		mysqli_query($conn, $sql);
	}
}
function CreateUser($ID,$fn,$ln,$points,$userN,$pass,$ANUM){
	$conn=conDB();
	$sql="INSERT INTO agents (Fname,Lname,CPoints,APoints,Username,Password,Active) VALUES ('$fn','$ln','$points','$points','$userN','$pass','1')";
	mysqli_query($conn, $sql);
	$inuse=checkAgentNUM($ANUM);
	if($inuse=="TRUE"){
		return "FALSE";
	}
	$sql2="UPDATE agents SET AgentNum='$ANUM' WHERE Fname='$fn' AND Lname='$ln' AND Password='$pass'";
	mysqli_query($conn, $sql2);
	return "TRUE";
}
function DeleteUser($AID){
	$conn=conDB();
	$sql="UPDATE agents SET Active='0' WHERE IDKey='$AID'";
	mysqli_query($conn, $sql);
}
function CreateDealer($Name,$Description){
	$conn=conDB();
	$sql="INSERT INTO dealers (Name,Description) VALUeS ('$Name','$Description')";
	mysqli_query($conn, $sql);
}
function setPrice($ITD,$Price){
	$conn=conDB();
	$sql="UPDATE items SET Price='$Price' WHERE IDKey='$ITD'";
	mysqli_query($conn, $sql);
}
function createInvoice($Total,$HST,$DealerID,$CusID){
	$conn=conDB();
	$sql="INSERT INTO invoices (CusID,DealerID,HST,Total) VALUES ('$CusID','$DealerID','$HST','$Total')";
	mysqli_query($conn, $sql);
}
function setItemDealer($ITD,$DID){
	$conn=conDB();
	$sql="UPDATE items SET DealerID='$DID' WHERE IDKey='$ITD'";
	mysqli_query($conn, $sql);
}
function createCrew($Name,$Desc){
	$conn=conDB();
	$sql="INSERT INTO crews (Name,Description) VALEUS ('$Name','$Desc')";
	mysqli_query($conn, $sql);
}
function assignTask($AID,$TID){
	$conn=conDB();
	$sql="UPDATE task SET AgentID='$AID' WHERE IDKey='$TID'";
	mysqli_query($conn, $sql);
}
function assignTasks($AID,$TIDS){
	$total=count($TIDS);
	for ($x=0;$x<$total;$x++){
		assignTask($AID,$TIDS[$x]);
	}
}
//Creates a Task form a given Template and Assigns it to an Agent starting on the Given Start Date, and Requiring a Power
function createTask($CID,$TEID,$Agent,$Start,$Name,$PowID){
	$conn=conDB();
	$PowID=getTemplatePow($TEID);
	$sql="INSERT INTO tasks (AgentID,Start,Name,PowID) VALUES ('$Agent','$Start','$Name','$PowID')";
	mysqli_query($conn, $sql);
}
//Takes in a Name, Description Power IDKey and Arrays of Fields, then creates the appropriate Template according to the Given Information
function createTaskTemplate($Name,$Desc,$Fields,$Req,$PowID){
	$conn=conDB();
	$sql="INSERT INTO tasktemplates (Name,Description,PowID) VALUES ('$Name','$Desc','$PowID')";
	mysqli_query($conn, $sql);
	$TEID=getTemplateByNameDesc($Name,$Desc);
	createTaskTemplateFields($Fields,$Req,$TEID);
	
}


//Sub Functions, These are generaly not called directly

function calculatePrice($ITD,$Price,$newStock){
	$CPrice=getItemPrice($ITD);
	$Cstock=getStockALL($ITD);
	$StockTotal=$Cstock+$newStock;
	$total=$CPrice*$Cstock+$Price*$newStock;
	$final=$total/$StockTotal;
	return $final;
}
function createTaskTemplateFields($IndexID,$Required,$TEID){
	$conn=conDB();
	$sql="INSERT INTO fields (CusIndexID,Required,TemplateID) VALUES ";
	$Indexes=count($IndexID);
	$ReqN=count($Required);
	for($x=0;$x<$Indexes;$x++){
		$dex=$IndexID[$x];
		$req=0;
		for($y=0;$y<$ReqN;$y++){
			if($Required[$y]==$IndexID[$x]){
				$req=$Required[$y];
			}
		}
		$sql="$sql ('$dex','$req','$TEID')";
		if($x+1!=$Indexes){
			$sql="$sql ,";
		}
	}
	echo $sql;
	mysqli_query($conn, $sql);
}
function searchCustomers($Term){
	$conn=conDB();
	$pieces=explode(" ",$Term);
	$piecesN=count($pieces);
	$returns=array();
	$v=0;
	$sql="SELECT * FROM cusfields WHERE";
	if($piecesN>0){
		for($x=0;$x<$piecesN;$x++){
			$sql="$sql VALUE='$pieces[$x]' AND ";
		}
		$sql="$sql Value IS NOT NULL";
	}else{
		$sql="$sql IndexID='0'";
	}
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$cusID=$row["CusID"];
		$returns[$v]=$cusID;
		$v++;
	}
	return $returns;
}

//Get Functions
//Takes in an agentID and returns an Array of Permissions granted
function GetPermissions($AID){
	$conn=conDB();
	$PIDS=array();
	$x=0;
	$sql="SELECT * FROM agentpermissions WHERE AgentID='$AID'";
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$PAID=$row["PermissionGranted"];
		$PIDS[$x]=$PAID;
		$x++;
	}
	return $PAID;
}
//Returns wether an Agent has a Permission
function checkPermission($AID,$PID){
	$conn=conDB();
	$sql="SELECT * FROM agentpermissions WHERE AgentID='$AID' AND PermissionGranted='$PID'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) !=0) {
		return TRUE;
	}
	return FALSE;
}
function checkPower($AID,$POW){
	
}
function checkIndexPer(){
	$conn=conDB();
	$sql="";
}
//Takes in an Interface IDKey and returns an Array of Interface lines
function getInterface($IID){
	$conn=conDB();
	$sql="SELECT * FROM interfaces WHERE IDKey='$IID'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$L1=$row["Line1"];
	$L2=$row["Line2"];
	$L3=$row["Line3"];
	$L4=$row["Line4"];
	$L5=$row["Line5"];
	$return=array();
	$return[0]=$L1;
	$return[1]=$L2;
	$return[2]=$L3;
	$return[3]=$L4;
	$return[4]=$L5;
	return $return;
}
//Takes in an AgentID and returns the list of Powers available
function getPowers($AID){
	$conn=conDB();
	$perms=GetPermissions($AID);
	$permsN=count($perms);
	$y=0;
	$pows=array();
	for($x=0;$x<$permsN;$x++){
		$Cperm=$perms[$x];
		$sql="SELECT * FROM pkeys WHERE PerID='$Cperm'";
		$result = mysqli_query($conn, $sql);
		while($row = $result->fetch_assoc()){
			$pows[$y]=$row["PowID"];
			$y++;
		}
	}
	return $pows;
}
//Takes in an AgentID and returns a list of Interface ID's
function getInterfaces($AID){
	$conn=conDB();
	$POWS=getPowers($AID);
	$powN=count($POWS);
	$IIDs=array();
	for($x=0;$x<$powN;$x++){
		$cPOW=$POWS[$x];
		$sql="SELECT * FROM powers WHERE IDKey='$cPOW'";
		$result = mysqli_query($conn, $sql);
		$row = $result->fetch_assoc();
		$IID=$row["InterfaceID"];
		$IIDs[$x]=$IID;
	}
	return $IIDs;
}
//Takes in an AgentId and returns a list of incomplete Tasks assigned to the user.
function getCurrentTasks($AID){
	$conn=conDB();
	$returns=array();
	$x=0;
	$sql="SELECT * FROM tasks WHERE AgentID='$AID' AND Complete='0'";
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$returns[$x]=$row["IDKey"];
		$x++;
	}
	return $returns;
}
//Returns the ID for an agents next incomplate, assigned Task, by chronological order assigned
function getNextTask($AID){
	$conn=conDB();
	$returns=array();
	$sql="SELECT * FROM tasks WHERE AgentID='$AID' AND Complete='0'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	return$row["IDKey"];
}
//Finds all FieldID's related to a Task ID and returns an array
function getTaskFields($TID){
	$conn=conDB();
	$sql="SELECT * FROM taskfields WHERE TaskID='$TID'";
	$result = mysqli_query($conn, $sql);
	$returns=array();
	$x=0;
	while($row = $result->fetch_assoc()){
		$returns[$x]=$row["FieldID"];
		$x++;
	}
	return $returns;
}
//A specialized version of get Task Fields, designed for the Smart Task UI (Probably overkill for most other purposes.)
function getTaskFieldsC($TID,$AID){
	$conn=conDB();
	$returns=array();
	$x=0;
	$sql="SELECT cusindex.ExpectedType,cusindex.Name,cusindex.PermIDV,cusindex.PermIDE,cusfields.IDKey,cusfields.Value FROM cusfields INNER JOIN cusindex ON cusfields.IndexID=cusindex.IDKey 
	WHERE cusfields.TaskID='$TID'";
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$PermIDV=$row["PermIDV"];
		$PermIDE=$row["PermIDE"];
		$returns[$x]=checkPermission($AID,$PermIDV);
		$x++;
		$returns[$x]=checkPermission($AID,$PermIDE);
		$x++;
		$returns[$x]=$row["Name"];
		$x++;
		$returns[$x]=$rows["ExpectedType"];
		$x++;
		$returns[$x]=$row["Value"];
		$x++;
		$returns[$x]=$row["IDKey"];
		$x++;
	}
	/*$returns index is as follows,
	 * 0=Permission to View True/False
	 * 1=Permission to Edit True/False
	 * 2=Name of the Customer Index
	 * 3=Expected Type of the Index
	 * 4=Current Value of the Field
	 * 5=IDKey of the CusField
	 */
	return $returns;
}
//Returns the currents Stock of an Item at a particular Location
function getTasks($AID){
	$conn=conDB();
	$sql="SELECT * FROM tasks WHERE AgentID='$AID' AND (Complete IS NULL OR Complete='0') ORDER BY Start";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$returns[$x]=$row["IDKey"];
		$x++;
		$returns[$x]=$row["Name"];
		$x++;
		$returns[$x]=$row["Start"];
		$x++;
		$returns[$x]=$row["DateofCompletion"];
		$x++;
	}
	return $returns;
	
}
//Returnst he current stock of an item at a certain location
function getStock($ITD,$LID){
	$conn=conDB();
	$sql="SELECT * FROM stock WHERE ItemID='$ITD' AND LocID='$LID'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	if (mysqli_num_rows($result) ==0) {
		return "None";
	}
	return $row["Quantity"];
}
//Returns the Total quantity in all stockpiles of a requested Item
function getStockALL($ITD){
	$conn=conDB();
	$sql="SELECT SUM(Quantity) as Q FROM stock WHERE ItemID='$ITD' AND LocID<>'1'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	return $row["Q"];
}
//Returns an array of the Current Stock, and Location ID of each item
function getStockPiles($ITD){
	$conn=conDB();
	$returns=array();
	$x=0;
	$sql="SELECT * FROM stock WHERE ItemID='$ITD' ORDER BY Quantity DESC";
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$returns[$x]=$row["Quantity"];
		$x++;
		$returns[$x]=$row["LocID"];
		$x++;
	}
	return $returns;
}
// Takes an Item Template ID and returns the associated array of Features of the item
// 0=Description 1=Category 2=Unit 3= Price
function getItem($ITD){
	$conn=conDB();
	$sql="SELECT * FROM items WHERE IDKey='$ITD'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$returns=array();
	$returns[0]=$row["Description"];
	$returns[1]=$row["CatID"];
	$returns[2]=$row["UnitID"];
	$returns[3]=$row["Price"];
	return $returns;
}
//Returns information about the requested Unit
function getUnit($UID){
	$conn=conDB();
	$sql="SELECT * FROM units WHERE IDKey='$UID'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$sym=$row["Symbol"];
	$desc=$row["Description"];
	$returns=array();
	$returns[0]=$sym;
	$returns[1]=$desc;
	return returns;
}
//Returns information about the Requested Category
function getCategory($CAT){
	$conn=conDB();
	$sql="SELECT * FROM categories WHERE IDKey='$CAT'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$sym=$row["Symbol"];
	$desc=$row["Description"];
	$returns=array();
	$returns[0]=$sym;
	$returns[1]=$desc;
	return returns;
}
//Returns the Crew Log for a Specific Crew
function getCrewLogC($crew){
	$conn=conDB();
	$returns=array();
	$x=0;
	$sql="SELECT * FROM WHERE CrewID='$crew'";
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$returns[$x]=$row["Reason"];
		$x++;
		$returns[$x]=$row["Date"];
		$x++;
		$AID=$row["ChangedBy"];
		$returns[$x]=getAgentName($AID);
		$x++;
	}
	return $returns;
}
//Returns the Crew Log for a specific Agent
function getCrewLogA($AID){
	$conn=conDB();
	$returns=array();
	$x=0;
	$sql="SELECT * FROM WHERE AgentID='$AID'";
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$returns[$x]=$row["Reason"];
		$x++;
		$returns[$x]=$row["Date"];
		$x++;
		$AID=$row["ChangedBy"];
		$returns[$x]=getAgentName($AID);
		$x++;
	}
	return $returns;
}
//Returns a String of the First and Last Name of the associated Agent ID
function getAgentName($AID){
	$conn=conDB();
	$sql="SELECT Fname,Lname FROM agents WHERE IDKey='$AID'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$Name=$row["Fname"]." ".$row["Lname"];
	return $Name;
}
//Returns the Current Agent's IDKey
function getID(){
	return $_SESSION["idnum"];
}
//Returns the current Date and Time
function getDateStamp(){
	$date = date('Y/m/d H:i:s');
	$date=str_replace('/', '-', $date);
	return $date;
}
//Modifies the current Date relative to a given date, and returns the day of the result
function getRelativeDate($days){
	$date=date('Y-m-d');
	$datetime=new DateTime($date);
	if($days>0){
		$datetime->add(new DateInterval("P".$days."D"));
	}
	if($days<0){
		$days=$days*-1;
		$datetime->sub(new DateInterval("P".$days."D"));
	}
	$FDate=$datetime->format('Y-m-d');
	return $FDate;
}
//Gets the current Date
function getToday(){
	$date=date('D');
	return $date;
}
//Gets the current day of the week
function getTodayinWeek(){
	$date=date('w');
	return $date;
}
//Gets the current day of the month
function getTodayinMonth(){
	$date=date('j');
	return $date;
}
//Returns the total Stock of an item globally according to the given Audit Number
function getAuditItemG($ITD,$AUDNUM){
	$conn=conDB();
	$sql="SELECT SUM(Quantity) AS total FROM audits WHERE ItemID='$ITD'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$quant=$row["total"];
	return $quant;
}
//Returns the Total stock of an item according to the Given Audit Number and Location IDKey
function getAuditItem($ITD,$AUDNUM,$LID){
	$conn=conDB();
	$sql="SELECT SUM(Quantity) AS total FROM audits WHERE ItemID='$ITD' AND LocID='$LID'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$quant=$row["total"];
	return $quant;
}
//Returns the total Difference between the Current Stock and the Audit Stock
function getAuditDifferences($ITD,$AUDNUM,$LID){
	$Aquant=getAuditItem($ITD,$AUDNUM,$LID);
	$Squant=getStock($ITD,$LID);
	$Fquant=$Squant-$Aquant;
	return $Fquant;
}
//Returns wether an Agent has the ability to grant a Permission
function getGrantable($AID,$PID){
	$conn=conDB();
	$sql="SELECT GrantableBy FROM permissions WHERE IDKey='$PID'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$grantP=$row["GrantableBy"];
	$canGrant=checkPermission($AID,$grantP);
	return $canGrant;
}
//Returns a boolean for wether an Agent Number is in use
function checkAgentNUM($ANUM){
	$conn=conDB();
	$sql="SELECT Count(*) FROM agents WHERE AgentNum='$ANUM'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	if($row["Count(*)"]==0){
		return "FALSE";
	}
	return "TRUE";
}
//Returns an Agent ID Key based on the given Agent Number
function getAgentID($ANUM){
	$conn=conDB();
	$sql="SELECT * FROM agents WHERE AgentNum='$ANUM'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	return $row["IDKey"];
}
//Returns an Agent Number based on the given Agent ID Key
function getAgentNUM($AID){
	$conn=conDB();
	$sql="SELECT * FROM agents WHERE IDKey='$AID'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	return $row["IDKey"];
}
//Returns an Items current Price
function getItemPrice($ITD){
	$conn=conDB();
	$sql="SELECT Price FROM items WHERE IDKey='$ITD'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	return $row["Price"];
}
//Returns an array with information about an invoice
function getInvoice($VID){
	$conn=conDB();
	$sql="SELECT * FROM invoices WHERE IDKey='$VID'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$returns=array();
	$x=0;
	$returns[$x]=$row["Total"];
	$x++;
	$returns[$x]=$row["HST"];
	$x++;
	$returns[$x]=$row["CusID"];
	$x++;
	$returns[$x]=$row["DealerID"];
	$x++;
	return $returns;
}
//returns an Array of Invoice ID's for a customer
function getInvoicesC($CID){
	$con=conDB();
	$sql="SELECT * FROM invoices WHERE CusID='$CID'";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$returns[$x]=$row["IDKey"];
		$x++;
		$returns[$x]=$row["Total"];
		$x++;
		$returns[$x]=$row["HST"];
		$x++;
	}
	return $returns;
}
//returns an array of invoice ID's for a Dealer
function getInvoicesD($DID){
	$con=conDB();
	$sql="SELECT * FROM invoices WHERE DealerID='$DID'";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$returns[$x]=$row["IDKey"];
		$x++;
		$returns[$x]=$row["Total"];
		$x++;
		$returns[$x]=$row["HST"];
		$x++;
	}
	return $returns;
}
//returns an array containing the Value and IDKey of all cusfields of the given type for the given task and customer
function getCusFieldT($CFID,$CID,$TID){
	$conn=conDB();
	$sql="SELECT * FROM cusfields WHERE TaskID='$TID AND IndexID='$CFID' AND CusID='$CID'";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row = $result->fetch_assoc()){
		$returns[$x]=$row["IDKey"];
		$x++;
		$returns[$x]=$row["Value"];
		$x++;
	}
	return $returns;
}
//Returns an array with the current Chat log inside of it.
function getChatLog($AID){
	$conn=conDB();
	$sql="SELECT agents.Fname,chat.Message,chat.TimeStamp FROM chat INNER JOIN agents ON chat.AgentID=agents.IDKey WHERE chat.Global='1' OR chat.TargetAID='$AID' OR chat.AgentID='$AID' ORDER BY chat.IDKey DESC LIMIT 100";
	$result = mysqli_query($conn, $sql);
	$return=array();
	$x=0;
	while($row= $result->fetch_assoc()){
		$message=$row["Message"];
		$player=$row["Fname"];
		$time=substr($row["TimeStamp"],0,-3);
		$final="$time:$player:$message";
		$return[$x]=$final;
		$x++;
	}
	return $return;
}
//Returns  timestamp for the current time
function getTimeStamp(){
	$date= date("H:i:s");
	return $date;
}
//returns an array containing the Value and IDKey of all cusfields for the given type and customer
function getCusFieldC($CID,$CIND){
	$conn=conDB();
	$sql="SELECT * FROM cusfields WHERE IndexID='$CIND' AND CusID='$CID'";
	$result = mysqli_query($conn, $sql);
	$row= $result->fetch_assoc();
	return $row["Value"];
}
//returns are array of Chat Favorites Agent Names and IDKey's for the given Agent
function getChatFavorites($AID){
	$conn=conDB();
	$sql="SELECT chatfavorite.TargetID,agents.Fname FROM chatfavorite INNER JOIN agents ON chatfavorite.TargetID=agents.IDKey WHERE AgentID='$AID'";
	$result = mysqli_query($conn, $sql);
	$return=array();
	$x=0;
	if (mysqli_num_rows($result) ==1) {
		while($row= $result->fetch_assoc()){
			$TID=$row["TargetID"];
			$AName=$row["Fname"];
			$return[$x]=$TID;
			$x++;
			$return[$x]=$AName;
			$x++;
		}
		return $return;
	}
	$return[0]=FALSE;
	$return[1]=FALSE;
	return $return;
}
//Returns a list of all tasks for a day given
function getTodaysTask($AID,$Date){
	$conn=conDB();
	$sql="SELECT * FROM tasks WHERE AgentID='$AID' AND Start='$Date' AND Stacking='0'";
	$result = mysqli_query($conn, $sql);
	$return=array();
	$x=0;
	while($row= $result->fetch_assoc()){
		$task=$row["IDKey"];
		$sql2="SELECT CusID FROM taskfields WHERE TaskID='$task' LIMIT 1";
		$result2 = mysqli_query($conn, $sql2);
		$row2= $result2->fetch_assoc();
		$CID=$row2["CusID"];
		$CusFName=getCusFieldC($CID,"2");
		$CusLName=getCusFieldC($CID,"3");
		$return[$x]=$row["Name"];
		$x++;
		$return[$x]=$task;
		$x++;
		$return[$x]="$CusFName $CusLName";
		$x++;
	}
	return $return;
}
//Returns the name of a given Task Tempalte
function getTemplateName($TEID){
	$conn=conDB();
	$sql="SELECT Name FROM tasktemplates WHERE IDKey='$TEID'";
	$result = mysqli_query($conn, $sql);
	$row= $result->fetch_assoc();
	return $row["Name"];
}
//Returns the Description of  a given task template
function getTemplateDescription($TEID){
	$conn=conDB();
	$sql="SELECT Description FROM tasktemplates WHERE IDKey='$TEID'";
	$result = mysqli_query($conn, $sql);
	$row= $result->fetch_assoc();
	return $row["Description"];
}
//returns the Fields related to a certain task template
function getTemplateFields($TEID){
	$conn=conDB();
	$returns=array();
	$x=0;
	$sql="SELECT IDKey FROM fields WHERE TemplateID='$TEID'";
	$result = mysqli_query($conn, $sql);
	while($row= $result->fetch_assoc()){
		$returns[$x]=$row["IDKey"];
		$x++;
	}
	return $returns;
}
//Returns the Customer ID of a Field
function getTemplatePow($TEID){
	$conn=conDB();
	$sql="SELECT PowID FROM tasktemplates WHERE IDKey='$TEID'";
	$result = mysqli_query($conn, $sql);
	return $row["PowID"];
}
function getfieldCIND($FID){
	$conn=conDB();
	$sql="SELECT CusIndexID FROM fields WHERE IDKey='$TEID'";
	$result = mysqli_query($conn, $sql);
	$row= $result->fetch_assoc();
	return $row["CusIndexID"];
}
//Returns the Requirements to View/Edit a field
function getfieldReq($FID){
	$conn=conDB();
	$sql="SELECT Required FROM fields WHERE IDKey='$TEID'";
	$result = mysqli_query($conn, $sql);
	$row= $result->fetch_assoc();
	return $row["Required"];
}
//Returns the Template that a field is associated with
function getfieldTEID($FID){
	$conn=conDB();
	$sql="SELECT TemplateID FROM fields WHERE IDKey='$TEID'";
	$result = mysqli_query($conn, $sql);
	$row= $result->fetch_assoc();
	return $row["TemplateID"];
}
//Returns an array of Cusfields that contain a given String
function searchCFields($Term){
	
}
//Returns the an Array of all Indexes [0]IDKey's [1]Name's
function getFieldIndex(){
	$conn=conDB();
	$sql="SELECT * FROM cusindex";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row= $result->fetch_assoc()){
		$returns[$x]=$row["IDKey"];
		$x++;
		$returns[$x]=$row["Name"];
		$x++;
	}
	return $returns;
}

function getPermittedIndexV($AID){
	$conn=conDB();
	$sql="SELECT PermIDV,Name,IDKey FROM cusindex";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row= $result->fetch_assoc()){
		$IDKey=$row["PermIDV"];
		if(checkPermission($AID,$IDKey)){
			$returns[$x]=$row["IDKey"];
			$x++;
			$returns[$x]=$row["Name"];
			$x++;
		}
	}
	return $returns;
}
function getPermittedIndexE($AID){
	$conn=conDB();
	$sql="SELECT PermIDE,Name,IDKey FROM cusindex";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row= $result->fetch_assoc()){
		$IDKey=$row["PermIDE"];
		if(checkPermission($AID,$IDKey)){
			$returns[$x]=$row["IDKey"];
			$x++;
			$returns[$x]=$row["Name"];
			$x++;
		}
	}
	return $returns;
}
function getListOfPermissions(){
	$conn=conDB();
	$sql="SELECT * FROM permissions";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row= $result->fetch_assoc()){
		$IDKey=$row["IDKey"];
		$Desc=$row["Description"];
		$returns[$x]=$IDKey;
		$x++;
		$returns[$x]=$Desc;
		$x++;
	}
	return $returns;
}
function getTemplateByNameDesc($Name,$Desc){
	$conn=conDB();
	$sql="SELECT * FROM tasktemplates WHERE Name='$Name' AND Description='$Desc'";
	$result = mysqli_query($conn, $sql);
	$row= $result->fetch_assoc();
	return $row["IDKey"];
}
function getListofTemplates($AID){
	$conn=conDB();
	$sql="SELECT * FROM tasktemplates";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row= $result->fetch_assoc()){
		$PID=$row["PowID"];
		$x++;
	}
}
function getPKeys(){
	$conn=conDB();
	$sql-"SELECT * FROM pkeys";
	$returns=array();
	$x=0;
	$result = mysqli_query($conn, $sql);
	while($row= $result->fetch_assoc()){
		$PID=$row["PowID"];
		$returns[$x]=$row["IDKey"];
		$x++;
		$returns[$x]=$row["PowID"];
		$x++;
		$returns[$x]=$row["PerID"];
		$x++;
	}
	return $returns;
}
//Returns an array with the Permissions required to [0]Read and [1]Edit a specific Customer Index
function getIndexPermissions($IndexID){
	$conn=conDB();
	$sql="SELECT PermIDV,PermIDE FROM cusindex WHERE IDKey='$IndexID'";
	$result = mysqli_query($conn, $sql);
	$row= $result->fetch_assoc();
	$returns=array();
	$returns[0]=$row["PermIDV"];
	$returns[1]=$row["PermIDE"];
	return $returns;
}


















?>