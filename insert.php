<?php
error_reporting(1);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);
ini_set('display_errors', true);
// Let's pass in a $_GET variable to our example, in this case
// it's aid for actor_id in our Sakila database. Let's make it
// default to 1, and cast it to an integer as to avoid SQL injection
// and/or related security problems. Handling all of this goes beyond
// the scope of this simple example. Example:
//   http://example.org/script.php?aid=42
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
	echo "Oops! Database connection error:<br />\n";

    // Something you should not do on a public site, but this example will show you
    // anyways, is print out MySQL error related information -- you might log this
	echo "Errno: " . $mysqli->connect_errno . "<br />\n";
	echo "Error: " . $mysqli->connect_error . "<br />\n";
	if ($mysqli->connect_errno == 1045){
		echo "Incorrect credentials. Double-check your credentials and make sure you are authorized to access the Puppies Unlimited&trade; database.";
	}
	else if ($mysqli->connect_errno == 1049){
		echo "Unknown database. Make sure the database you're trying to connect to exists.";
	} 
	else if ($mysqli->connect_errno == 2002){
		echo "Connection refused. Make sure you're on the correct network to access the Puppies Unlimited&trade; database and that it's live.";
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
		echo "The number of values you specified (<b>" . count($values_arr) . ") does not match the number of columns you specified (" . count($columns_arr) .").";
		$validparams = FALSE;
	}
}

// Check if minimum requirements for 'columns' is met for 'puppy' table
if (($table == 'puppy') && ((!in_array("puppy_name", $columns_arr)) && (!in_array("puppy_photo", $columns_arr))) && $validparams == TRUE) {
	echo "Oops! Parameter error:<br />\n";
	echo "Your <b>INSERT</b> 'columns' parameter for table <b>" . $table . "</b> needs to at least include either:<br />\n";
	echo "(1) puppy_location,puppy_photo<br />\n";
	echo "(2) puppy_name,puppy_location<br />\n";
	echo "<i>( Example: <b>http://192.168.50.92/it350site/insert.php?user=my_user&secretkey=my_secretkey&table=puppy&columns=puppy_name,puppy_location&values=Alfred,Mesa</b> )</i>";
	$validparams = FALSE;
}

// Check if minimum requirements for 'columns' is met for 'customer' table
if (($table == 'customer') && ((!in_array("customer_phone", $columns_arr)) && (!in_array("customer_email", $columns_arr)) && (!in_array("customer_address", $columns_arr))) && $validparams == TRUE) {
	echo "Oops! Parameter error:<br />\n";
	echo "Your <b>INSERT</b> 'columns' parameter for table <b>" . $table . "</b> needs to at least include either:<br />\n";
	echo "(1) customer_name,customer_address<br />\n";
	echo "(2) customer_name,customer_phone<br />\n";
	echo "(3) customer_name,customer_email<br />\n";
	echo "<i>( Example: <b>http://192.168.50.92/it350site/insert.php?user=my_user&secretkey=my_secretkey&table=customer&columns=customer_name,customer_email&values=Bob,bob@gmail.com</b> )</i>";
	$validparams = FALSE;
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

	$sql .= ') VALUES (';

	for($k = 0; $k < count($values_arr); $k++){
		$sql .= "'" . $values_arr[$k] . "'";
		if ($k !== (count($values_arr)-1)){
			$sql .= ', ';
		}
	}

	$sql .= ")";

	echo $sql;

	// Perform an SQL query
	if(($validparams == TRUE) && ($result = $mysqli->query($sql))){
		echo "<b>INSERT</b> executed successfully!";
	} else {
    // Oh no! The query failed. 
		echo "Sorry, the website is experiencing problems.";

    // Again, do not do this on a public site, but we'll show you how
    // to get the error information
		echo "Error: Our query failed to execute and here is why: \n";
		echo "Query: " . $sql . "\n";
		echo "Errno: " . $mysqli->errno . "\n";
		echo "Error: " . $mysqli->error . "\n";
		exit;
	}
}

// The script will automatically free the result and close the MySQL
// connection when it exits, but let's just do it anyways
$mysqli->close();
?>