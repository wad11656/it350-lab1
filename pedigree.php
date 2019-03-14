<?php
//error_reporting(0);

// Set $_GET variables
if (isset($_GET['user'])){
	$user = filter_var($_GET['user'], FILTER_SANITIZE_STRING);
} else {
	$user = 'N/A';
}

if (isset($_GET['secretkey'])){
	$secretkey = filter_var($_GET['secretkey'], FILTER_SANITIZE_STRING);
} else {
	$secretkey = 'N/A';
}

if (isset($_GET['puppy'])){
	$puppy = filter_var($_GET['puppy'], FILTER_SANITIZE_STRING);
} else {
	$puppy = 'N/A';
}

// Set global boolean for valid URL parameters
$validparams = TRUE;

if ((!isset($_GET['user'])) || (!isset($_GET['secretkey'])) || (!isset($_GET['puppy']))){
	echo "Oops! Parameter error:<br />\n";
	echo "All <b>pedigree.php</b> URL queries require a 'user', 'secretkey' and 'puppy' parameter. Check you have at least these three in your URL.<br />\n";
	echo "<i>( Example: <b>http://40.117.58.200/it350site/pedigree.php?user=my_user&secretkey=my_secretkey&puppy=Christina</b> )</i>";
	$validparams = FALSE;
	exit;
}

// Connecting to and selecting a MySQL database named puppies_unlimited
$mysqli = new mysqli('127.0.0.1', $user, $secretkey, 'puppies_unlimited');


// Oh no! A connect_errno exists so the connection attempt failed!
if ($mysqli->connect_errno) {
    // The connection failed
	echo "Oops! Database connection error";

	// ERROR 1045 - Wrong credentials
	if ($mysqli->connect_errno == 1045){
		echo ":<br />\nIncorrect credentials. Double-check your credentials and make sure you are authorized to access the Puppies Unlimited&trade; database.";
	}
	// ERROR 1049 - Unknown database
	else if ($mysqli->connect_errno == 1049){
		echo ":<br />\nUnknown database. Make sure the database you're trying to connect to exists.";
	} 
	// ERROR 2002 - Connection refused
	else if ($mysqli->connect_errno == 2002){
		echo ":<br />\nConnection refused. Make sure you're on the correct network to access the Puppies Unlimited&trade; database and that it's live.";
	} 
	$validparams = FALSE;
	exit;
}

// Check if one of the parameters has more than 1 value
if ($validparams == TRUE){
	$params_arr = ['puppy'];
	for ($k = 0; $k < count($params_arr); $k++){
		$param =  filter_var($_GET[$params_arr[$k]], FILTER_SANITIZE_STRING);
		$param_arr = explode(",",$param);
		if (count($param_arr) > 1){
			echo "Oops! Parameter error:<br />\n";
			echo "You specified more than one value for your <b>'$params_arr[$k]'</b> parameter. <b>pedigree.php</b> accepts only 1 value for each parameter.<br />\n";
			echo "<i>( Example: <b>http://40.117.58.200/it350site/pedigree.php?user=my_user&secretkey=my_secretkey&puppy=Christina</b> )</i><br />\n";
			$validparams = FALSE;
		}
		// If all parameters only have 1 value each, check if each value exists in its respective table.
		else{
			$error_columns = "";
			for ($i = 0; $i < count($param_arr); $i++){
				if ($stmt = $mysqli->query("SELECT * FROM $params_arr[$k] WHERE $params_arr[$k]_name = '$param_arr[$i]'")) {
					if ($stmt->num_rows == 1){
					// Valid parameter value
					}
					else{
						$error_columns .= $param_arr[$i] . " ";
						$validparams = FALSE;
					}
				}
				else{
					echo "Oops! Execution Error:<br />\n";
					echo "The <b>SELECT</b> statement did not execute successfully. Please check your syntax.<br />\n";
					echo "Errno: " . $mysqli->errno . "<br />\n";
					echo "Error: " . $mysqli->error . "<br />\n";
					echo "<i>( Example: <b>http://40.117.58.200/it350site/pedigree.php?user=my_user&secretkey=my_secretkey&puppy=Christina</b> )</i>";
					$validparams = FALSE;
					exit;
				}
			}
			if ($error_columns !== ""){
				echo "Oops! Parameter error:<br />\n";
				echo "The value <b>" . $error_columns . "</b>";
				echo " that you specified for your <b>'$params_arr[$k]'</b> parameter is not in the <b>$params_arr[$k]</b> table. Check your spelling and try again.<br />\n";
				$validparams = FALSE;
			}
		}
	}
}

function getParent($child,$mysqli){
	$parents = [];
	$query = "SELECT id FROM puppy WHERE puppy_name = '$child'";
	$childId = "";
	if($result = $mysqli->query($query)){
		while($row=$result->fetch_row()){
			$childId = $row[0];
		}
		$result->free_result();
	}
	else{
		// Oh no! The query failed. 
		echo "Oops! Execution Error:<br />\n";
		echo "<b>query</b>'s <b>READ</b> did not execute successfully. Please check your syntax.<br />\n";
		echo "Errno: " . $mysqli->errno . "<br />\n";
		echo "Error: " . $mysqli->error . "<br />\n";
		echo "<i>( Example: <b>http://40.117.58.200/it350site/pedigree.php?user=my_user&secretkey=my_secretkey&puppy=Christina</b> )</i>";
		$validparams = FALSE;
	}

	$query2 = "SELECT puppy_name FROM (SELECT puppy_parent_id FROM parent WHERE puppy_child_id = '$childId') sub LEFT JOIN puppy ON sub.puppy_parent_id = puppy.id";

	if($result=$mysqli->query($query2)){
		while($row=$result->fetch_row()){
			array_push($parents, getParent($row[0],$mysqli));
		}
		$result->free_result();
	}
	else{
		// Oh no! The query failed. 
		echo "Oops! Execution Error:<br />\n";
		echo "<b>query2</b>'s <b>READ</b> did not execute successfully. Please check your syntax.<br />\n";
		echo "Errno: " . $mysqli->errno . "<br />\n";
		echo "Error: " . $mysqli->error . "<br />\n";
		echo "<i>( Example: <b>http://40.117.58.200/it350site/pedigree.php?user=my_user&secretkey=my_secretkey&puppy=Christina</b> )</i>";
		$validparams = FALSE;
	}
	return [$child => $parents];
}

// Perform an SQL query
if ($validparams == TRUE){

	$result_array = getParent($puppy, $mysqli);
	echo json_encode($result_array);
}
else {
   			// Oh no! The query failed. 
	echo "Oops! Execution Error:<br />\n";
	echo "The <b>READ</b> did not execute successfully. Please check your syntax.<br />\n";
	echo "<i>( Example: <b>http://40.117.58.200/it350site/pedigree.php?user=my_user&secretkey=my_secretkey&puppy=Christina</b> )</i>";
	exit;
}

// The script will automatically free the result and close the MySQL
// connection when it exits, but let's just do it anyways
$mysqli->close();
?>