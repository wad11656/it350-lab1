<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set $_GET variables
if (isset($_GET['aid']) && is_numeric($_GET['aid'])) {
	$aid = (int) $_GET['aid'];
} else {
	$aid = 1;
}

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
	echo "<i>( Example: <b>http://192.168.50.92/it350site/insert.php?user=my_user&secretkey=my_secretkey&table=puppy&columns=puppy_name,puppy_location&values='Alfred','Mesa'</b> )</i>";
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

// Check if 'columns' and 'values' are set
if ((!isset($_GET['columns'])) || (!isset($_GET['values']))){
	echo "Oops! Parameter error:<br />\n";
	echo "All Puppies Unlimited&trade; <b>INSERT</b> queries require a 'columns' and 'values' parameter.<br />\n";
	echo "<i>( Example: <b>http://192.168.50.92/it350site/insert.php?user=my_user&secretkey=my_secretkey&table=puppy&columns=puppy_name,puppy_location&values='Alfred','Mesa'</b> )</i>";
	$validparams = FALSE;
}


// Check if 'columns' Column(s) exist(s) in Table
if (isset($_GET['columns']) && $validparams == TRUE){
	$columns =  filter_var($_GET['columns'], FILTER_SANITIZE_STRING);
	$columns_arr = explode(",",$columns);
	$error_columns = "";
	for ($i = 0; $i < count($columns_arr); $i++){
		if ($mysqli->query("SELECT $columns_arr[$i] FROM $table")){
			// Valid 'columns' column
		}
		else{
			$error_columns .= $columns_arr[$i] . " ";
			$validparams = FALSE;
		}
	}
	if ($error_columns !== ""){
		echo "Oops! Parameter error:<br />\n";
		echo "The column(s) <b>" . $error_columns . "</b>";
		echo " you specified for your 'columns' parameter is/are not in the <b>$table</b> table. Check your spelling and try again.";
		$validparams = FALSE;
	}
}

// Check if # of 'values' is same as # of 'columns'
if (isset($_GET['values']) && $validparams == TRUE){
	$values = filter_var($_GET['values'], FILTER_SANITIZE_STRING);
	$values_arr = explode(",",$values);
	if (count($columns_arr) != count($values_arr)){
		echo "Oops! Parameter error:<br />\n";
		echo "The number of values you specified (<b>" . count($values_arr) . "</b>) does not match the number of columns you specified (<b>" . count($columns_arr) ."</b>).";
		$validparams = FALSE;
	}
}

// Check if minimum requirements for 'columns' is met for 'puppy' table
if (($table == 'puppy') && (!in_array("puppy_name", $columns_arr)) && $validparams == TRUE) {
	echo "Oops! Parameter error:<br />\n";
	echo "Your <b>INSERT</b> 'columns' parameter for table <b>" . $table . "</b> needs to at least include a <b>puppy_name</b>.<br />\n";
	echo "<i>( Example: <b>http://192.168.50.92/it350site/insert.php?user=my_user&secretkey=my_secretkey&table=puppy&columns=puppy_name&values='Alfred'</b> )</i>";
	$validparams = FALSE;
}

// Check if minimum requirements for 'columns' is met for 'customer' table
if (($table == 'customer') && (!in_array("customer_name", $columns_arr)) && $validparams == TRUE) {
	echo "Oops! Parameter error:<br />\n";
	echo "Your <b>INSERT</b> 'columns' parameter for table <b>" . $table . "</b> needs to at least include a <b>customer_name</b>.<br />\n";
	echo "<i>( Example: <b>http://192.168.50.92/it350site/insert.php?user=my_user&secretkey=my_secretkey&table=customer&columns=customer_name&values='Bob'</b> )</i>";
	$validparams = FALSE;
}

// Check if 'customer_email' is valid
/*
if (($table == 'customer') && (in_array("customer_email", $columns_arr)) && $validparams == TRUE) {
	$email_key = array_search("customer_email", $columns_arr);
	$email_quoteless = substr($values_arr[$email_key],5,-5);
	echo $email_quoteless;
	if(!filter_var(($email_quoteless), FILTER_VALIDATE_EMAIL)){
		echo "Oops! Parameter error:<br />\n";
		echo "Your email value for your 'customer_email' column (<b>" . $values_arr[$email_key] . "</b>) is not a valid email address. Confirm your 'columns' and 'values' parameters are listed in the same comma-delimited order as each other in your URL.<br />\n";
		$validparams = FALSE;
	}
}
*/

// Check if 'customer_phone' is valid
if (($table == 'customer') && (in_array("customer_phone", $columns_arr)) && $validparams == TRUE) {
	$phone_key = array_search("customer_phone", $columns_arr);
	if((!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", substr($values_arr[$phone_key],5,-5))) && (!preg_match("/^[0-9]{3}-[0-9]{4}$/", substr($values_arr[$phone_key],5,-5))) && (!preg_match("/^[0-9]{7}$/", substr($values_arr[$phone_key],5,-5))) && (!preg_match("/^[0-9]{10}$/", substr($values_arr[$phone_key],5,-5)))) {
		echo "Oops! Parameter error:<br />\n";
		echo "Your phone value for your 'customer_phone' column (<b>" . $values_arr[$phone_key] . "</b>) is not a valid 7- or 10-digit American phone number. Confirm your 'columns' and 'values' parameters are listed in the same comma-delimited order as each other in your URL.<br />\n";
		$validparams = FALSE;
	}
}

// Check if 'customer_age' is positive INT
if (($table == 'customer') && (in_array("customer_age", $columns_arr)) && $validparams == TRUE) {
	$customer_age_key = array_search("customer_age", $columns_arr);
	$value = substr($values_arr[$customer_age_key],5,-5);
	if(!is_numeric($value) || !($value > 0) || !($value == round($value, 0))){
		echo "Oops! Parameter error:<br />\n";
		echo "Your age value for your 'customer_age' column (<b>" . $values_arr[$customer_age_key] . "</b>) is not a valid positive integer. Confirm your values are surrounded by single quotes.<br />\n";
		echo "Confirm 'customer_age' does not contain any non-numeric characters.<br />\n";
		echo "Confirm your 'columns' and 'values' parameters are listed in the same comma-delimited order as each other in your URL.<br />\n";
		$validparams = FALSE;
	}
}

// Check if 'puppy_age' is positive INT
if (($table == 'puppy') && (in_array("puppy_age", $columns_arr)) && $validparams == TRUE) {
	$puppy_age_key = array_search("puppy_age", $columns_arr);
	$value = substr($values_arr[$puppy_age_key],5,-5);
	if(!is_numeric($value) || !($value > 0) || !($value == round($value, 0))) {
		echo "Oops! Parameter error:<br />\n";
		echo "Your age value for your 'puppy_age' column (<b>" . $values_arr[$puppy_age_key] . "</b>) is not a valid positive integer. Confirm your values are surrounded by single quotes.<br />\n";
		echo "Confirm 'puppy_age' does not contain any non-numeric characters.<br />\n";
		echo "Confirm your 'columns' and 'values' parameters are listed in the same comma-delimited order as each other in your URL.<br />\n";
		$validparams = FALSE;
	}
}

if ($validparams == TRUE){
// Add columns and values to SQL statement
	$sql = 'INSERT INTO ' . $table . ' (';
	for($j = 0; $j < count($columns_arr); $j++){
		$sql .= $columns_arr[$j];
		if ($j !== (count($columns_arr)-1)){
			$sql .= ', ';
		}
	}

	if (isset($_GET['values'])){
		$clean_values = filter_var($_GET['values'], FILTER_SANITIZE_STRING);
		$revised_values = str_replace("%20"," ",$clean_values);
		$revised_values = preg_replace("!&#39;%?[a-zA-Z0-9]+%?&#39;!","?",$revised_values);
		$values_array = preg_match_all("!&#39;(%?[a-zA-Z0-9]+%?)&#39;!", $clean_values, $value_matches, PREG_PATTERN_ORDER);

		$sql .= ') VALUES (' . $revised_values . ')';
	}

	/*
	$sql .= ') VALUES (';

	for($k = 0; $k < count($values_arr); $k++){
		$sql .= "'" . $values_arr[$k] . "'";
		if ($k !== (count($values_arr)-1)){
			$sql .= ', ';
		}
	}

	$sql .= ")";
	*/

	$rows_affected = 0;
	// Perform an SQL query
	print $sql;
	if ($stmt = $mysqli->prepare($sql)){
		if ($clean_values != False) {
			$types = "";
			foreach ($value_matches[1] as $c) {
				if (preg_match("![0-9\.]+!",$c)) {
					$types .= "d";
				} else {
					$types .= "s";
				}
			}
			$stmt->bind_param($types, ...$value_matches[1]);
		}
		$stmt->execute();
		$rows_affected = $stmt->affected_rows;
	}
	else{
   		// Oh no! The query failed. 
		echo "Oops! Execution Error:<br />\n";
		echo "The <b>INSERT</b> did not execute successfully. Please check your syntax.<br />\n";
		echo "<i>( Example: <b>http://192.168.50.92/it350site/insert.php?user=my_user&secretkey=my_secretkey&table=customer&columns=customer_name,customer_email&values='Bob','bob@gmail.com'</b> )</i>";
		$validparams = FALSE;
		exit;
	}

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