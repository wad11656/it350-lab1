<?php
error_reporting(1);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);
ini_set('display_errors', true);

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

if (isset($_GET['table'])){
	$table = filter_var($_GET['table'], FILTER_SANITIZE_STRING);
} else {
	$table = 'N/A';
}

// Set global boolean for valid URL parameters
$validparams = TRUE;

if ((!isset($_GET['user'])) || (!isset($_GET['secretkey'])) || (!isset($_GET['table']))){
	echo "Oops! Parameter error:<br />\n";
	echo "All Puppies Unlimited&trade; URL queries require a 'user', 'secretkey' and 'table' parameter. Check you have at least these three in your URL.<br />\n";
	echo "<i>( Example: <b>http://192.168.50.92/it350site/read.php?user=my_user&secretkey=my_secretkey&table=puppy</b> )</i>";
	$validparams = FALSE;
	exit;
}

// Connecting to and selecting a MySQL database named puppies_unlimited
$mysqli = new mysqli('127.0.0.1', $user, $secretkey, 'puppies_unlimited');


// Oh no! A connect_errno exists so the connection attempt failed!
if ($mysqli->connect_errno) {
    // The connection failed
	echo "Oops! Database connection error:<br />\n";

	// ERROR 1045 - Wrong credentials
	if ($mysqli->connect_errno == 1045){
		echo "Incorrect credentials. Double-check your credentials and make sure you are authorized to access the Puppies Unlimited&trade; database.";
	}
	// ERROR 1049 - Unknown database
	else if ($mysqli->connect_errno == 1049){
		echo "Unknown database. Make sure the database you're trying to connect to exists.";
	} 
	// ERROR 2002 - Connection refused
	else if ($mysqli->connect_errno == 2002){
		echo "Connection refused. Make sure you're on the correct network to access the Puppies Unlimited&trade; database and that it's live.";
	} 
	$validparams = FALSE;
	exit;
}

// Check if Table exists in Database
$checktable = "DESCRIBE $table";
if ((!$mysqli->query($checktable)) && $validparams == TRUE){
	echo "Oops! Parameter error:<br />\n";
	echo "The table you specified for your 'table' parameter is not in the Puppies Unlimited&trade; database. Check your spelling and try again.";
	$validparams = FALSE;
	exit;
}

// Check if 'set' is set
if ((!isset($_GET['set']))) {
	echo "Oops! Parameter error:<br />\n";
	echo "All Puppies Unlimited&trade; <b>UPDATE</b> queries require a 'set', parameter.<br />\n";
	echo "<i>( Example: <b>http://192.168.50.92/it350site/update.php?user=my_user&set=my_secretkey&table=puppy&set=puppy_age='5'&conditions=id='17'</b> )</i>";
	$validparams = FALSE;
	exit;
}

// Perform an SQL query
if ($validparams == TRUE){
	// Append `UPDATE`
	$sql = 'UPDATE ' . $table;
	// Clean and append set
	if (isset($_GET['set'])){
		$clean_set = filter_var($_GET['set'], FILTER_SANITIZE_STRING);
		$revised_set = str_replace("%20"," ",$clean_set);
		$revised_set = preg_replace("!&#39;%?[a-zA-Z0-9]+%?&#39;!","?",$revised_set);
		$set_array = preg_match_all("!&#39;(%?[a-zA-Z0-9]+%?)&#39;!", $clean_set, $set_matches, PREG_PATTERN_ORDER);
		
		// Append `SET [set]`
		$sql .= ' SET ' . $revised_set;
	}

	// Clean and append conditions
	if (isset($_GET['conditions'])){
		$clean_conditions = filter_var($_GET['conditions'], FILTER_SANITIZE_STRING);
		$revised_conditions = str_replace("%20"," ",$clean_conditions);
		$revised_conditions = preg_replace("!&#39;%?[a-zA-Z0-9]+%?&#39;!","?",$revised_conditions);
		$conditions_array = preg_match_all("!&#39;(%?[a-zA-Z0-9]+%?)&#39;!", $clean_conditions, $condition_matches, PREG_PATTERN_ORDER);
		
		// Append `WHERE [conditions]`
		$sql .= ' WHERE ' . $revised_conditions;
	}

	$rows_affected = 0;
	// HAS CONDITIONS SQL Query:
	if (isset($_GET['conditions'])){
		if ($stmt = $mysqli->prepare($sql)){
			$parameters = [];
			$types = "";
			foreach ($set_matches[1] as $c) {
				if (preg_match("![0-9\.]+!",$c)) {
					$types .= "d";
				} else {
					$types .= "s";
				}
				$parameters[] = $c;
			}
			if ($clean_conditions != False) {
				foreach ($condition_matches[1] as $c) {
					if (preg_match("![0-9\.]+!",$c)) {
						$types .= "d";
					} else {
						$types .= "s";
					}
					$parameters[] = $c;
				}
				$stmt->bind_param($types, ...$parameters);
			}
			$stmt->execute();
			$rows_affected = $stmt->affected_rows;
		}
		else{
   			// Oh no! The query failed. 
			echo "Oops! Execution Error:<br />\n";
			echo "The <b>UPDATE</b> did not execute successfully. Please check your syntax.<br />\n";
			echo "<i>( Example: <b>http://192.168.50.92/it350site/update.php?user=my_user&set=my_secretkey&table=puppy&set=puppy_age='5'&conditions=id='17'</b> )</i>";
			$validparams = FALSE;
			exit;
		}

		if ($validparams == TRUE){
			// Print result of SQL query as JSON
			$result = $stmt->get_result();
			if($stmt->execute()){
				echo "<b>UPDATE</b> executed successfully!<br />\n";
				echo "<b>" . $rows_affected . "</b> row(s) affected.";
			}
		}
	}

	// WITHOUT CONDITIONS SQL query:
	else if (isset($_GET['set'])){
		if ($stmt = $mysqli->prepare($sql)){
			if ($clean_set != False) {
				$types = "";
				foreach ($set_matches[1] as $c) {
					if (preg_match("![0-9\.]+!",$c)) {
						$types .= "d";
					} else {
						$types .= "s";
					}
				}
				$stmt->bind_param($types, ...$set_matches[1]);
			}
			$stmt->execute();
			$rows_affected = $stmt->affected_rows;
		}
		else{
   			// Oh no! The query failed. 
			echo "Oops! Execution Error:<br />\n";
			echo "The <b>UPDATE</b> did not execute successfully. Please check your syntax.<br />\n";
			echo "<i>( Example: <b>http://192.168.50.92/it350site/update.php?user=my_user&set=my_secretkey&table=puppy&set=puppy_age='5'&conditions=id='17'</b> )</i>";
			$validparams = FALSE;
			exit;
		}

		if ($validparams == TRUE){
			// Print result of SQL query as JSON
			$result = $stmt->get_result();
			echo "<b>UPDATE</b> executed successfully!<br />\n";
			echo "<b>" . $rows_affected . "</b> row(s) affected.";
		}
	}
}

// The script will automatically free the result and close the MySQL
// connection when it exits, but let's just do it anyways
$mysqli->close();
?>