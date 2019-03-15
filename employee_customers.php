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

if (isset($_GET['query'])){
	$query = filter_var($_GET['query'], FILTER_SANITIZE_STRING);
} else {
	$query = 'N/A';
}

$validparams = TRUE;

if ((!isset($_GET['user'])) || (!isset($_GET['secretkey']))){
	echo "Oops! Parameter error:<br />\n";
	echo "This Puppies Unlimited&trade; URL query require a 'user' and 'secretkey' parameter. Check you have these two in your URL.<br />\n";
	echo "( Example: <b>http://40.117.58.200/it350site/popularcustomers.php?user=my_user&secretkey=my_secretkey</b> )";
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

// Perform an SQL query
if ($validparams == TRUE){
	$sql = "SELECT person_name FROM person pers INNER JOIN (SELECT emp.person_id FROM employee emp INNER JOIN customer cust ON emp.person_id = cust.person_id) AS cust_emp ON pers.id = cust_emp.person_id";
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
		echo "The <b>CREATE VIEW</b> statement did not execute successfully. Please check your syntax.<br />\n";
			echo "Errno: " . $mysqli->errno . "<br />\n";
	echo "Error: " . $mysqli->error . "<br />\n";
		echo "<i>( Example: <b>http://40.117.58.200/it350site/popularcustomers.php?user=my_user&secretkey=my_secretkey</b> )</i>";
		$validparams = FALSE;
		exit;
	}
	// Print result of SQL query as JSON
	if ($validparams == TRUE){
		// Print result of SQL query as JSON
		$result = $stmt->get_result();
		$result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
		echo json_encode($result_array);
	}
}

// The script will automatically free the result and close the MySQL
// connection when it exits, but let's just do it anyways
$mysqli->close();
?>