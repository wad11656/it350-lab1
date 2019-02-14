<?php
error_reporting(0);

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

if (isset($_GET['immunization'])){
	$immunization = filter_var($_GET['immunization'], FILTER_SANITIZE_STRING);
} else {
	$immunization = 'N/A';
}

if (isset($_GET['veterinarian'])){
	$veterinarian = filter_var($_GET['veterinarian'], FILTER_SANITIZE_STRING);
} else {
	$veterinarian = 'N/A';
}

$validparams = TRUE;

if ((!isset($_GET['user'])) || (!isset($_GET['secretkey'])) || (!isset($_GET['puppy'])) || (!isset($_GET['immunization'])) || (!isset($_GET['veterinarian']))){
	echo "Oops! Parameter error:<br />\n";
	echo "<b>give_immunization.php</b> requires a 'user', 'secretkey', 'puppy', 'immunization', and 'veterinarian' parameter. Check you have all of these in your URL.<br />\n";
	echo "<i>( Example: <b>http://40.117.58.200/it350site/give_immunization.php?user=my_user&secretkey=my_secretkey&puppy=Alfred&immunization=Measles&veterinarian=Steve</b> )</i>";
	$validparams = FALSE;
	exit;
}

// Connecting to and selecting a MySQL database named sakila
// Hostname: 127.0.0.1, username: your_user, password: your_pass, db: sakila
$mysqli = new mysqli('127.0.0.1', $user, $secretkey, 'puppies_unlimited');


// Oh no! A connect_errno exists so the connection attempt failed!
if ($mysqli->connect_errno) {
    // The connection failed. What do you want to do? 
    // You could contact yourself (email?), log the error, show a nice page, etc.
    // You do not want to reveal sensitive information

    // Let's try this:
	echo "Oops! Database connection error";

    // Something you should not do on a public site, but this example will show you
    // anyways, is print out MySQL error related information -- you might log this
	echo "Errno: " . $mysqli->connect_errno . "<br />\n";
	echo "Error: " . $mysqli->connect_error . "<br />\n";
	if ($mysqli->connect_errno == 1045){
		echo ":<br />\nIncorrect credentials. Double-check your credentials and make sure you are authorized to access the Puppies Unlimited&trade; database.";
	}
	else if ($mysqli->connect_errno == 1049){
		echo ":<br />\nUnknown database. Make sure the database you're trying to connect to exists.";
	} 
	else if ($mysqli->connect_errno == 2002){
		echo ":<br />\nConnection refused. Make sure you're on the correct network to access the Puppies Unlimited&trade; database and that it's live.";
	} 
	$validparams = FALSE;
    // You might want to show them something nice, but we will simply exit
	exit;
}

// Check if one of the parameters has more than 1 value
if ($validparams == TRUE){
	$params_arr = ['puppy','immunization','veterinarian'];
	for ($k = 0; $k < count($params_arr); $k++){
		$param =  filter_var($_GET[$params_arr[$k]], FILTER_SANITIZE_STRING);
		$param_arr = explode(",",$param);
		if (count($param_arr) > 1){
			echo "Oops! Parameter error:<br />\n";
			echo "You specified more than one value for your <b>'$params_arr[$k]'</b> parameter. <b>give_immunization.php</b> accepts only 1 value for each parameter.<br />\n";
			echo "<i>( Example: <b>http://40.117.58.200/it350site/give_immunization.php?user=my_user&secretkey=my_secretkey&puppy=Alfred&immunization=Measles&veterinarian=Steve</b> )</i><br />\n";
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
					echo "<i>( Example: <b>http://40.117.58.200/it350site/give_immunization.php?user=my_user&secretkey=my_secretkey&puppy=Alfred&immunization=Measles&veterinarian=Steve</b> )</i>";
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


// Perform an SQL query
if ($validparams == TRUE){
	echo $puppy;
	echo $veterinarian;
	echo $immunization;
	$sql = "INSERT INTO puppy_immunization (puppy_id, immu_id, vet_id) VALUES ((SELECT puppy_id FROM puppy WHERE puppy_name = '$puppy'),(SELECT immu_id FROM immunization WHERE immunization_name = '$immunization'),(SELECT vet_id FROM veterinarian WHERE veterinarian_name = '$veterinarian'))";
	// Set rows_affected
	$rows_affected = 0;
	// Execute 1st SQL Statment ($sql)
	if ($stmt = $mysqli->prepare($sql)){
		$stmt->execute();
		$rows_affected = $stmt->affected_rows;
	}
	else{
   			// Oh no! The query failed. 
		echo "Oops! Execution Error:<br />\n";
		echo "The <b>SELECT</b> statement did not execute successfully. Please check your syntax.<br />\n";
		echo "Errno: " . $mysqli->errno . "<br />\n";
		echo "Error: " . $mysqli->error . "<br />\n";
		echo "<i>( Example: <b>http://40.117.58.200/it350site/unadoptedbreedcounts.php?user=my_user&secretkey=my_secretkey</b> )</i>";
		$validparams = FALSE;
		exit;
	}
	// Print result of SQL query as JSON
	if ($validparams == TRUE){
		// Print result of SQL query as JSON
		$result = $stmt->get_result();
		echo "<b>INSERT</b> executed successfully!<br />\n";
		echo "<b>" . $rows_affected . "</b> row(s) affected.";
	}
}

// The script will automatically free the result and close the MySQL
// connection when it exits, but let's just do it anyways
$mysqli->close();
?>