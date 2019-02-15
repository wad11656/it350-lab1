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

if (isset($_GET['table'])){
	$table = filter_var($_GET['table'], FILTER_SANITIZE_STRING);
} else {
	$table = 'N/A';
}

$validparams = TRUE;

if ((!isset($_GET['user'])) || (!isset($_GET['secretkey'])) || (!isset($_GET['table']))){
	echo "Oops! Parameter error:<br />\n";
	echo "All Puppies Unlimited&trade; URL queries require a 'user', 'secretkey' and 'table' parameter. Check you have at least these three in your URL.<br />\n";
	echo "( Example: <b>http://40.117.58.200/it350site/delete.php?user=my_user&secretkey=my_secretkey&table=puppy&id='6'</b> )";
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

// Check if Table exists in Database
$checktable = "DESCRIBE $table";
if ((!$mysqli->query($checktable)) && $validparams == TRUE){
	echo "Oops! Parameter error:<br />\n";
	echo "The table you specified for your 'table' parameter is not in the Puppies Unlimited&trade; database. Check your spelling and try again.";
	$validparams = FALSE;
	exit;
}

if (!isset($_GET['conditions'])){
	echo "Oops! Parameter error:<br />\n";
	echo "All Puppies Unlimited&trade; <b>DELETE</b> queries require a 'conditions' parameter.<br />\n";
	echo "<i>( Example: <b>http://40.117.58.200/it350site/delete.php?user=my_user&secretkey=my_secretkey&table=puppy&id='6'</b> )</i>";
	$validparams = FALSE;
}

// Perform an SQL query
if ($validparams == TRUE){
	$sql = 'DELETE FROM ' . $table;
	if (isset($_GET['conditions'])){

		$clean_conditions = filter_var($_GET['conditions'], FILTER_SANITIZE_STRING);
		$revised_conditions = str_replace("%20"," ",$clean_conditions);
		$revised_conditions = preg_replace("!&#39;%?[a-zA-Z0-9\-\@\. ]+%?&#39;!","?",$revised_conditions);
		$conditions_array = preg_match_all("!&#39;(%?[a-zA-Z0-9\-\@\. ]+%?)&#39;!", $clean_conditions, $condition_matches, PREG_PATTERN_ORDER);

		$sql .= ' WHERE ' . $revised_conditions;
	}
echo $sql;
	// Set rows_affected
	$rows_affected = 0;
	// Print result of SQL query as JSON
	if ($stmt = $mysqli->prepare($sql)){
		if ($clean_conditions != False) {
			$types = "";
			foreach ($condition_matches[1] as $c) {
				if (preg_match("!^[0-9\.]+$!",$c)) {
					$types .= "d";
				} else {
					$types .= "s";
				}
			}
			$stmt->bind_param($types, ...$condition_matches[1]);
		}
		$stmt->execute();
		$rows_affected = $stmt->affected_rows;
		if ($rows_affected < 0){
								echo "Errno: " . $mysqli->errno . "<br />\n";
		echo "Error: " . $mysqli->error . "<br />\n";

		}
	}
	else{
   			// Oh no! The query failed. 
		echo "Oops! Execution Error:<br />\n";
		echo "The <b>DELETE</b> did not execute successfully. User <b>" . $user . "</b> may not have authority to <b>DELETE</b>. Requires elevated credentials.<br />\n";
		echo "If you are sure your credentials have <b>DELETE</b> privileges, double-check your syntax (Use <b>single quotes</b> around your non-column 'conditions' values).<br />\n";
							echo "Errno: " . $mysqli->errno . "<br />\n";
		echo "Error: " . $mysqli->error . "<br />\n";

		echo "<i>( Example: <b>http://40.117.58.200/it350site/insert.php?user=my_user&secretkey=my_secretkey&table=puppy&id='6'</b> )</i>";
		$validparams = FALSE;
		exit;
	}

	if ($validparams == TRUE){
		// Print result of SQL query as JSON
		$result = $stmt->get_result();
		echo "<b>DELETE</b> executed successfully!<br />\n";
		echo "<b>" . $rows_affected . "</b> row(s) affected.";
	}
}

// The script will automatically free the result and close the MySQL
// connection when it exits, but let's just do it anyways
$mysqli->close();
?>