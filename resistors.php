<?php require_once('Connections/ohiofi.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "0,1,2";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to th class="resistorth"eir username. 
  // th class="resistorth"erefore, we know th class="resistorth"at a user is NOT logged in if th class="resistorth"at Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when th class="resistorth"ey login. 
    // Parse th class="resistorth"e strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on th class="resistorth"eir username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotdefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotdefinedValue;
      break;
  }
  return $theValue;
}
}






/*

----------------------------------------------HERE IS thE SURVEY STUFF----------------------------------------------

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO survey (entryNumber, userName, gameNumber, questionNumber, q1, q2, q3, q4, q5) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['entry'], "int"),
                       GetSQLValueString($_SESSION['MM_Username'], "text"),
                       GetSQLValueString($_POST['gamenum'], "int"),
                       GetSQLValueString($_POST['ques'], "int"),
                       GetSQLValueString($_POST['RadioGroup1'], "text"),
                       GetSQLValueString($_POST['RadioGroup2'], "text"),
                       GetSQLValueString($_POST['RadioGroup3'], "text"),
                       GetSQLValueString($_POST['RadioGroup4'], "text"),
                       GetSQLValueString($_POST['RadioGroup5'], "text"));

  mysql_select_db($database_ohiofi, $ohiofi);
  $Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());

  $insertGoTo = "mainmenu.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}*/


$editFormAction = $_SERVER['PHP_SELF'];

if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_rsgame4 = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsgame4 = $_SESSION['MM_Username'];
}
mysql_select_db($database_ohiofi, $ohiofi);
$query_rsgame4=sprintf("SELECT game4responses.entryNumber, game4responses.questionNumber FROM game4responses WHERE userName=%s AND game4responses.tally=1 ORDER BY game4responses.questionNumber DESC",
  GetSQLValueString($colname_rsgame4, "text")); 
$rsgame4 = mysql_query($query_rsgame4, $ohiofi) or die(mysql_error());
$row_rsgame4 = mysql_fetch_assoc($rsgame4);
$totalRows_rsgame4 = mysql_num_rows($rsgame4);




mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScore4=sprintf("SELECT game4responses.entryNumber, game4responses.questionNumber FROM game4responses WHERE userName=%s",
  GetSQLValueString($colname_rsgame4, "text")); 
$rsScore4 = mysql_query($query_rsScore4, $ohiofi) or die(mysql_error());
$row_rsScore4 = mysql_fetch_assoc($rsScore4);
$totalRows_rsScore4 = mysql_num_rows($rsScore4);


/* ------------------------------------- rsgame4_check is the fail safe -----------------------------------------*/

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsgame4_check=sprintf("SELECT game4responses.entryNumber, game4responses.questionNumber FROM game4responses WHERE userName=%s AND game4responses.questionNumber=%s",
  GetSQLValueString($colname_rsgame4, "text"), GetSQLValueString($_POST['questionNumber'], "int")); 
$rsgame4_check = mysql_query($query_rsgame4_check, $ohiofi) or die(mysql_error());
$row_rsgame4_check = mysql_fetch_assoc($rsgame4_check);
$totalRows_rsgame4_check = mysql_num_rows($rsgame4_check);


mysql_select_db($database_ohiofi, $ohiofi);
$query_rsUser=sprintf("SELECT users.userID, users.game4 FROM users WHERE userName=%s ",
  GetSQLValueString($colname_rsgame4, "text")); 
$rsUser = mysql_query($query_rsUser, $ohiofi) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);




$maxRows_rsScoreboard = 10;
$pageNum_rsScoreboard = 0;
if (isset($_GET['pageNum_rsScoreboard'])) {
  $pageNum_rsScoreboard = $_GET['pageNum_rsScoreboard'];
}
$startRow_rsScoreboard = $pageNum_rsScoreboard * $maxRows_rsScoreboard;

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScoreboard = "SELECT userName, game4 FROM users ORDER BY game4 DESC";
$query_limit_rsScoreboard = sprintf("%s LIMIT %d, %d", $query_rsScoreboard, $startRow_rsScoreboard, $maxRows_rsScoreboard);
$rsScoreboard = mysql_query($query_limit_rsScoreboard, $ohiofi) or die(mysql_error());
$row_rsScoreboard = mysql_fetch_assoc($rsScoreboard);

if (isset($_GET['totalRows_rsScoreboard'])) {
  $totalRows_rsScoreboard = $_GET['totalRows_rsScoreboard'];
} else {
  $all_rsScoreboard = mysql_query($query_rsScoreboard);
  $totalRows_rsScoreboard = mysql_num_rows($all_rsScoreboard);
}
$totalPages_rsScoreboard = ceil($totalRows_rsScoreboard/$maxRows_rsScoreboard)-1;$maxRows_rsScoreboard = 10;
$pageNum_rsScoreboard = 0;
if (isset($_GET['pageNum_rsScoreboard'])) {
  $pageNum_rsScoreboard = $_GET['pageNum_rsScoreboard'];
}
$startRow_rsScoreboard = $pageNum_rsScoreboard * $maxRows_rsScoreboard;

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScoreboard = "SELECT userName, game4 FROM users WHERE game4 > 0 ORDER BY game4 DESC";
$query_limit_rsScoreboard = sprintf("%s LIMIT %d, %d", $query_rsScoreboard, $startRow_rsScoreboard, $maxRows_rsScoreboard);
$rsScoreboard = mysql_query($query_limit_rsScoreboard, $ohiofi) or die(mysql_error());
$row_rsScoreboard = mysql_fetch_assoc($rsScoreboard);

if (isset($_GET['totalRows_rsScoreboard'])) {
  $totalRows_rsScoreboard = $_GET['totalRows_rsScoreboard'];
} else {
  $all_rsScoreboard = mysql_query($query_rsScoreboard);
  $totalRows_rsScoreboard = mysql_num_rows($all_rsScoreboard);
}
$totalPages_rsScoreboard = ceil($totalRows_rsScoreboard/$maxRows_rsScoreboard)-1;

$queryString_rsScoreboard = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsScoreboard") == false && 
        stristr($param, "totalRows_rsScoreboard") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsScoreboard = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsScoreboard = sprintf("&totalRows_rsScoreboard=%d%s", $totalRows_rsScoreboard, $queryString_rsScoreboard);





/*
mysql_select_db($database_ohiofi, $ohiofi);
$query_rsSurvey=sprintf("SELECT survey.questionNumber FROM survey WHERE survey.userName=%s AND survey.gameNumber=3 ORDER BY survey.questionNumber DESC",
  GetSQLValueString($colname_rsgame4, "text")); 
$rsSurvey = mysql_query($query_rsSurvey, $ohiofi) or die(mysql_error());
$row_rsSurvey = mysql_fetch_assoc($rsSurvey);
$totalRows_rsSurvey = mysql_num_rows($rsSurvey);


*/


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
	if ($totalRows_rsgame4_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game4=%s, game4total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['points'], "int"),
							   GetSQLValueString($_POST['game4total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form4")) {
	if ($totalRows_rsgame4_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game4total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['game4total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
	if ($totalRows_rsgame4_check == 0) {
		///if ($row_rsScore4['questionNumber'] == $totalRows_rsScore4){
			
					   
	  		$insertSQL = sprintf("INSERT INTO game4responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['entryNumber'], "int"),
						   GetSQLValueString($_SESSION['MM_Username'], "text"),
						   GetSQLValueString($_POST['questionNumber'], "int"),
						   GetSQLValueString($_POST['question1'], "text"),
						   GetSQLValueString($_POST['response1'], "text"),
						   GetSQLValueString($_POST['tally'], "int"));
					   
	  		mysql_select_db($database_ohiofi, $ohiofi);
	 		$Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());
		///}
	}
	header('Location: ' . $_SERVER['PHP_SELF']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
	if ($totalRows_rsgame4_check == 0) {
		///if ($row_rsScore4['questionNumber'] == $totalRows_rsScore4){
	  		$insertSQL = sprintf("INSERT INTO game4responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['entryNumber'], "int"),
						   GetSQLValueString($_SESSION['MM_Username'], "text"),
						   GetSQLValueString($_POST['questionNumber'], "int"),
						   GetSQLValueString($_POST['question2'], "text"),
						   GetSQLValueString($_POST['response2'], "text"),
						   GetSQLValueString($_POST['tally'], "int"));
	
	  		mysql_select_db($database_ohiofi, $ohiofi);
	  		$Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());
		///}
	}
	header('Location: ' . $_SERVER['PHP_SELF']);
}



$points = $totalRows_rsgame4 + 1;
$currentQuestion = $totalRows_rsScore4 + 1;
//print_r($currentQuestion . "vs" . $rsSurvey['questionNumber'] );
?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Resistors web app</title>

<link rel="stylesheet" type="text/css" href="musictechwebapps.css" />
<style>
#resistorTable{
	position:relative;
	float:left;
	margin-top:20px;
}
#resistorTable, .resistorth, .resistortd{
	background-color:#ffffff;
	border:3px solid #c3c3c3;
	border-collapse:collapse;
	padding:0px 10px 0px 10px;
	font-size:14px;
}
.textcenter{
	text-align:center;
}
.row{
	position:relative;
	float:left;
	width:100%;
	margin-top:20px;
}
form{
	position:relative;
	float:left;
	height:200px;
	margin-right:20px;
}
select{
	position:relative;
	float:left;
	/*left:22px;
	top:46px;
	z-index:100;*/
}
#clickMe {
	position:relative;
	float:left;
	border:2px solid black;
	padding:5px 15px 5px 15px;
	margin:20px;
	background:rgba(100,60,140,0.7);
	border:solid #999999 3px;
	color:#999999;
	text-align:center;
	-webkit-border-radius:10px;
	-moz-border-radius:10px;
	background-image:url(img/brushedMetal.jpg);
	-webkit-box-shadow: 2px 2px 6px rgba(0,0,0,0.2);
}
#clickMe:hover {
	border:3px solid black;
	color:black;
	-webkit-box-shadow: 2px 2px 6px rgba(0,0,0,0.6);
	background:#643c8c;
	background:rgba(100,60,140,0.9);
}
#resistorsmain {
	position:relative;
	width:1000px;
	margin:30px auto 30px auto;
	padding:0 20px 20px 20px;
	background:#FFF;
	/*border:3px solid black;*/
	-webkit-border-radius:10px;
	-moz-border-radius:10px;
	/*-webkit-box-shadow:0 0 8px rgba(67,71,60,0.9);
	-moz-box-shadow:0 0 8px rgba(67,71,60,0.9);*/
	height:460px;
}
</style>

</head>
<body onload="preloader('win1')" />
<?php include("_includes/header.php"); ?>
<div id="resistorsmain">  
    <div id="stage">

    <table id="resistorTable">
    <tr>
    <th class="resistorth">Color</th>
    <th class="resistorth">Figures</th>
    <th class="resistorth">Multiplier</th>
    <th class="resistorth">Tolerance</th>
    </tr>
    <tr>
    <td class="resistortd">Black</td>
    <td class="resistortd" class="textcenter">0</td>
    <td class="resistortd" class="textcenter">x10<sup>0</sup></td>
    <td class="resistortd"></td>
    </tr>
    <td class="resistortd">Brown</td>
    <td class="resistortd" class="textcenter">1</td>
    <td class="resistortd" class="textcenter">x10<sup>1</sup></td>
    <td class="resistortd" class="textcenter">&plusmn;1&#37;</td>
    </tr>
    <td class="resistortd">Red</td>
    <td class="resistortd" class="textcenter">2</td>
    <td class="resistortd" class="textcenter">x10<sup>2</sup></td>
    <td class="resistortd" class="textcenter">&plusmn;2&#37;</td>
    </tr>
    <td class="resistortd">Orange</td>
    <td class="resistortd" class="textcenter">3</td>
    <td class="resistortd" class="textcenter">x10<sup>3</sup></td>
    <td class="resistortd" class="textcenter"></td>
    </tr>
    <td class="resistortd">Yellow</td>
    <td class="resistortd" class="textcenter">4</td>
    <td class="resistortd" class="textcenter">x10<sup>4</sup></td>
    <td class="resistortd"></td>
    </tr>
    <td class="resistortd">Green</td>
    <td class="resistortd" class="textcenter">5</td>
    <td class="resistortd" class="textcenter">x10<sup>5</sup></td>
    <td class="resistortd" class="textcenter">&plusmn;0.5&#37;</td>
    </tr>
    <td class="resistortd">Blue</td>
    <td class="resistortd" class="textcenter">6</td>
    <td class="resistortd" class="textcenter">x10<sup>6</sup></td>
    <td class="resistortd" class="textcenter">&plusmn;0.25&#37;</td>
    </tr>
    <td class="resistortd">Violet</td>
    <td class="resistortd" class="textcenter">7</td>
    <td class="resistortd" class="textcenter">x10<sup>7</sup></td>
    <td class="resistortd" class="textcenter">&plusmn;0.1&#37;</td>
    </tr>
    <td class="resistortd">Gray</td>
    <td class="resistortd" class="textcenter">8</td>
    <td class="resistortd" class="textcenter">x10<sup>8</sup></td>
    <td class="resistortd" class="textcenter">&plusmn;0.05&#37;</td>
    </tr>
    <td class="resistortd">White</td>
    <td class="resistortd" class="textcenter">9</td>
    <td class="resistortd" class="textcenter">x10<sup>9</sup></td>
    <td class="resistortd"></td>
    </tr>
    <td class="resistortd">Gold</td>
    <td class="resistortd"></td>
    <td class="resistortd" class="textcenter">x10<sup>-1</sup></td>
    <td class="resistortd" class="textcenter">&plusmn;5&#37;</td>
    </tr>
    <td class="resistortd">Silver</td>
    <td class="resistortd"></td>
    <td class="resistortd" class="textcenter">x10<sup>-2</sup></td>
    <td class="resistortd" class="textcenter">&plusmn;10&#37;</td>
    </tr>
    </table>
    
    <?php
    if (($row_rsUser["game4"]%2)==1)
    {
        ?>
        <div class="row">
        What four color bands would symbolize a <a id="myAnchor1"></a><font size="-1">&Omega;</font> resistor with a tolerance of &plusmn;<a id="myAnchor2"></a>&#37;?<br />
        </div>
        <div class="row">
        <form id="form1" name="form1" method="post" action="">
          
          <select name="digit1" id="Question01">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option disabled>Black</option>
            <option value="10">Brown</option>
            <option value=20>Red</option>
            <option value="30">Orange</option>
            <option value="40">Yellow</option>
            <option value="50">Green</option>
            <option value="60">Blue</option>
            <option value="70">Violet</option>
            <option value="80">Gray</option>
            <option value="90">White</option>
            <option disabled>Gold</option>
            <option disabled>Silver</option>
          </select>
          
          <select name="digit2" id="Question02">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="0">Black</option>
            <option value="1">Brown</option>
            <option value="2">Red</option>
            <option value=3>Orange</option>
            <option value="4">Yellow</option>
            <option value="5">Green</option>
            <option value="6">Blue</option>
            <option value="7">Violet</option>
            <option value="8">Gray</option>
            <option value="9">White</option>
            <option disabled>Gold</option>
            <option disabled>Silver</option>
          </select>
          
          <select name="multiplier" id="Question03">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="0">Black</option>
            <option value="10">Brown</option>
            <option value="100">Red</option>
            <option value="1000">Orange</option>
            <option value="10000">Yellow</option>
            <option value="100000">Green</option>
            <option value="1000000">Blue</option>
            <option value="10000000">Violet</option>
            <option value="100000000">Gray</option>
            <option value="1000000000">White</option>
            <option value="0.1">Gold</option>
            <option value="0.01">Silver</option>
          </select>
        
          <select name="tolerance" id="Question04">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option disabled>Black</option>
            <option value="1">Brown</option>
            <option value="2">Red</option>
            <option disabled>Orange</option>
            <option disabled>Yellow</option>
            <option value="0.5">Green</option>
            <option value="0.25">Blue</option>
            <option value="0.1">Violet</option>
            <option value="0.05">Gray</option>
            <option disabled>White</option>
            <option value="5">Gold</option>
            <option value="10">Silver</option>
          </select><br />
          <!---<button onclick="checkAnswer(this.form1)">Submit</button>--->
        </form>
        <?php
        }
        else
        {
        ?>
            <div class="row">
            What is the value of a resistor with <a id="myAnchor1"></a>, <a id="myAnchor2"></a>, <a id="myAnchor3"></a>, and <a id="myAnchor4"></a> color bands?
            </div>
            <div class="row">
            <form id="form1" name="form1" method="post" action="">
                <input type="text" name="text1" />&Omega; with a tolerance of &plusmn;<input type="text" name="text2" />&#37;
            </form>
            <?php
            }
            ?>
				<div id="clickMe" onclick="checkAnswer();">Click Me!</div>
			</div>
		</div>

	</div>
<div>
    
                <div id="greatJob" class="popup">
                    <h2>
                        <?php $my_array = array(0 => "Great Job!", 1 => "Nicely Done!", 2 => "That's Right!");
                        shuffle($my_array);
                        echo($my_array[0]);
                        ?>               
                    </h2>
                  <form id="form3" name="form3" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question1" id="question1" value=""><input type="hidden" name="response1" id="response1" value=""><input name="tally" type="hidden" value="1" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="points" type="hidden" value="<?=$points ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game4total" type="hidden" value="<?php echo $totalRows_rsScore4; ?>" /><input class="submit button popupbutton" type="submit" name="Submit" id="Submit" value="Continue"><input type="hidden" name="MM_insert" value="form3" /><input type="hidden" name="MM_update" value="form3" /></form></div>
                <div id="tryAgain" class="popup">
                    <h2>Incorrect</h2>
                  <form id="form4" name="form4" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question2" id="question2" value=""><input type="hidden" name="response2" id="response2" value=""><input name="tally" type="hidden" value="0" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game4total" type="hidden" value="<?php echo $totalRows_rsScore4; ?>" /><input class="submit button popupbutton" type="submit" name="Submit" id="Submit" value="Continue"><input type="hidden" name="MM_insert" value="form3" /><input type="hidden" name="MM_update" value="form3" /></form>
				  <? if ($currentQuestion % 1 == 0) { ?>
                  <h2>Your current score is<br /><? echo $row_rsUser["game4"] ?> <? if ($row_rsUser["game4"] != 1)
                                echo " pts";
                            else
                                echo " pt";
                            ?></h2>
                    
                      
                      <hr />
                      
                      <p>High Scores</p>
                      <p>
                      <table border="0" STYLE="margin:15px;">
                        <?php
                        do { ?>
                          <tr>
                            <td><?php echo $row_rsScoreboard['userName']," "; ?></td>
                            <td><?php echo " "; ?></td>
                            <td><?php echo " ",$row_rsScoreboard['game4'];
                            if ($row_rsScoreboard['game4'] != 1)
                                echo " pts";
                            else
                                echo " pt";
                            ?>
                            
                            
                            </td>
                          </tr>
                          <?php 
                          } while ($row_rsScoreboard = mysql_fetch_assoc($rsScoreboard)); ?>
                          <tr>
                            <td><?php if ($pageNum_rsScoreboard > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rsScoreboard=%d%s", $currentPage, max(0, $pageNum_rsScoreboard - 1), $queryString_rsScoreboard); ?>"><font size="1">Previous</font></a><?php } // Show if not first page ?></td>
                            <td></td>
                            <td><?php if ($pageNum_rsScoreboard < $totalPages_rsScoreboard) { // Show if not last page ?>
                      <a href="<?php printf("%s?pageNum_rsScoreboard=%d%s", $currentPage, min($totalPages_rsScoreboard, $pageNum_rsScoreboard + 1), $queryString_rsScoreboard); ?>"><font size="1">Next</font></a>
                      <?php } // Show if not last page ?></td>
                          </tr>
                      </table>
                       
                      </p>
                      
                      <? } ?>
                      
                </div>
                
	<br />
    </div> 
<span id="blank"></span>



<?php include("_includes/footer.php"); ?>
</body>
<?php
if (($row_rsUser["game4"]%2)==1)
{
	?>
    <script>
var oneChance=0;
var question=new Array();
var yourAnswer=new Array();
function newQuestion(){
	question[0]=((Math.floor((Math.random()*90)+10)))* Math.pow(10,(Math.floor(Math.random()*12)-2))
	if (question[0]<=10){
		question[0]=question[0].toPrecision(2);
	}
	displayQuestion0=question[0].toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ",");
	question[1]=(Math.floor(Math.random( )*12)+1);
	if (question[1]<=1){
		question[1]=11;
	}
	if (question[1]==2){
		displayQuestion1=1;
	}
	else if (question[1]==3){
		displayQuestion1=2;
	}
	else if (question[1]==4){
		question[1]=11;
	}
	else if (question[1]==5){
		question[1]=11;
	}
	else if (question[1]==6){
		displayQuestion1=0.5;
	}
	else if (question[1]==7){
		displayQuestion1=0.25;
	}
	else if (question[1]==8){
		displayQuestion1=0.1;
	}
	else if (question[1]==9){
		displayQuestion1=0.05;
	}
	else if (question[1]==10){
		question[1]=12;
	}
	if (question[1]==11){
		displayQuestion1=5;
	}
	if (question[1]>=12){
		displayQuestion1=10;
	}
	///trythis=((Math.floor((Math.random()*90)+10)));
	///trythis=trythis * Math.pow(10,(Math.floor(Math.random()*12)-2));
	///trythis=trythis.toPrecision(2);
	///if (trythis<=10){
	///	trythis=trythis.toPrecision(2);
	///}
	///alert((trythis.toString()));
	document.getElementById('myAnchor1').innerHTML=displayQuestion0;
	document.getElementById('myAnchor2').innerHTML=displayQuestion1;	
}
newQuestion();


    <?php
}
else
{
	?>
    <script>
var oneChance=0;
var question=new Array();
var yourAnswer=new Array();
function newQuestion(){
	display1=(Math.floor(Math.random()*9)+1)/* number between 1 and 9 */;
	display2=(Math.floor(Math.random()*10))/* number between 0 and 9 */;
	display3=(Math.floor(Math.random()*12)-2)/* number between -2 and 9 */;
	question[0]=((display1*10)+display2)*Math.pow(10,display3)/* 10 to the power of between -2 and 9 */;
	if (question[0]<=10){
		question[0]=question[0].toPrecision(2);
	}
	display4=(Math.floor(Math.random()*12)-2)/* number between -2 and 9 */;
	if (display4==0){/// these turn blank tolerances into gold or silver
		display4=-1;
	}
	else if (display4==3){
		display4=-1;
	}
	else if (display4==4){
		display4=-2;
	}
	else if (display4>=9){
		display4=-2;
	}
	
	if (display4==1){
		question[1]=1;
	}
	else if (display4==2){
		question[1]=2;
	}
	else if (display4==5){
		question[1]=0.5;
	}
	else if (display4==6){
		question[1]=0.25;
	}
	else if (display4==7){
		question[1]=0.1;
	}
	else if (display4==8){
		question[1]=0.05;
	}
	else if (display4==-1){
		question[1]=5;
	}
	else if (display4<=-2){
		question[1]=10;
	}
	/*displayQuestion0=question[0].toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ",");*/
	/*question[1]=(Math.floor(Math.random( )*12)+1);*/
	document.getElementById('myAnchor1').innerHTML=colorEncode(display1);
	document.getElementById('myAnchor2').innerHTML=colorEncode(display2);
	document.getElementById('myAnchor3').innerHTML=colorEncode(display3);
	document.getElementById('myAnchor4').innerHTML=colorEncode(display4);
	
	///trythis=((Math.floor((Math.random()*90)+10)));
	///trythis=trythis * Math.pow(10,(Math.floor(Math.random()*12)-2));
	///trythis=trythis.toPrecision(2);
	///if (trythis<=10){
	///	trythis=trythis.toPrecision(2);
	///}
	///alert((trythis.toString()));
	///document.getElementById('myAnchor1').innerHTML=displayQuestion0;
	///document.getElementById('myAnchor2').innerHTML=displayQuestion1;	
}
newQuestion();

function colorEncode(x) {
	if (x==0){
		return "Black";
	}
	else if (x==1){
		return "Brown";
	}
	else if (x==2){
		return "Red";
	}
	else if (x==3){
		return "Orange";
	}
	else if (x==4){
		return "Yellow";
	}
	else if (x==5){
		return "Green";
	}
	else if (x==6){
		return "Blue";
	}
	else if (x==7){
		return "Violet";
	}
	else if (x==8){
		return "Gray";
	}
	else if (x==9){
		return "White";
	}
	else if (x==-1){
		return "Gold";
	}
	else if (x==-2){
		return "Silver";
	}
	else {
		return "ERROR";
	}
}



    <?php
}
	?>
	function checkAnswer() {
	if (oneChance == 0) {
		<?php
		if (($row_rsUser["game4"]%2)==1)
{
		?>
		yourMultiplier=((document.form1.multiplier.selectedIndex)-1);
		if (yourMultiplier==10){
			yourMultiplier=-1;
		}
		else if (yourMultiplier==11){
			yourMultiplier=-2;
		}
		yourAnswer[0]=(((((document.form1.digit1.selectedIndex)-1)*10)+((document.form1.digit2.selectedIndex)-1))*Math.pow(10,yourMultiplier));
		yourAnswer[1]=document.form1.tolerance.selectedIndex;
		<?php
	}
		else
		{
		?>
		yourAnswer[0]=parseFloat((document.form1.text1.value).replace(/\,/g,""));
		yourAnswer[1]=parseFloat((document.form1.text2.value).replace(/\,/g,""));
		<?php
	}
		?>
		
		
		document.form3.response1.value = yourAnswer;
		document.form4.response2.value = yourAnswer;
		document.form3.question1.value = question;
		document.form4.question2.value = question;
		if ((yourAnswer[0]==question[0]) && (yourAnswer[1]==question[1])){
			///document.getElementById("blank").innerHTML="<embed src=\"win1.mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
			///alert(yourAnswer[0]+" ohms plus or minus "+displayYourAnswer+" percent is CORRECT!");
			///setTimeout('document.getElementById("greatJob").className = "popup popupActive"',300);
			///setTimeout('alert(yourAnswer[0]+" ohms plus or minus "+yourAnswer[1]+" percent is CORRECT!")',60);
			///setTimeout('window.location.reload()',80);
			document.getElementById("blank").innerHTML="<embed src=\"<?php $my_array = array(0 => 'win1', 1 => 'win2', 2 => 'win3', 3 => 'win4', 4 => 'win5', 5 => 'win6', 6 => 'win7', 7 => 'win8');
					shuffle($my_array);
					echo($my_array[0]);
					?>.mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
			setTimeout('document.getElementById("greatJob").className = "popup popupActive"',300);
		}
		else {
			///document.getElementById("blank").innerHTML="<embed src=\"fail1.mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
			///alert("TRY AGAIN. "+yourAnswer[0]+" ohms plus or minus "+displayYourAnswer+" percent is WRONG.");
			///setTimeout('document.getElementById("tryAgain").className = "popup popupActive"',300);
			///setTimeout('alert("TRY AGAIN. "+yourAnswer[0]+" ohms plus or minus "+yourAnswer[1]+" percent is WRONG.")',60);
			///setTimeout('window.location.reload()',80);
			document.getElementById("blank").innerHTML="<embed src=\"<?php $my_array = array(0 => 'fail1', 1 => 'fail2', 2 => 'fail3', 3 => 'fail4', 4 => 'fail5', 5 => 'fail6', 6 => 'fail7', 7 => 'fail8');
					shuffle($my_array);
					echo($my_array[0]);
					?>.mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
			setTimeout('document.getElementById("tryAgain").className = "popup popupActive"',300);
		}
	}
	oneChance++;
}

function preloader(soundfile) {
 	document.getElementById("blank").innerHTML= "<embed src=\""+soundfile+".mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" volume=\"0\" />";
}






</script>
</html>