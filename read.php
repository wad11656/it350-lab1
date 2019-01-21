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
	$user = $_GET['user'];
} else {
	$user = 'N/A';
}

if (isset($_GET['secretkey'])){
	$secretkey = $_GET['secretkey'];
} else {
	$secretkey = 'N/A';
}

if (isset($_GET['table'])){
	$table = $_GET['table'];
} else {
	$table = 'N/A';
}

if ((!isset($_GET['user'])) || (!isset($_GET['secretkey'])) || (!isset($_GET['table']))){
	echo "Oops! Parameter error:<br />\n";
	echo "All Puppies Unlimited&trade; URL queries require a 'user', 'secretkey' and 'table' parameter. Check you have at least these three in your URL.<br />\n";
	echo "( Example: http://192.168.50.92/it350site/read.php?user=my_user&secretkey=my_secretkey&table=my_table )";
	exit;
}

// Connecting to and selecting a MySQL database named sakila
// Hostname: 127.0.0.1, username: your_user, password: your_pass, db: sakila
$mysqli = new mysqli('127.0.0.1', $user, $secretkey, 'puppies_unlimited');
$validparams = TRUE;

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
if (!$mysqli->query($checktable)){
	echo "Oops! Parameter error:<br />\n";
	echo "The table you specified for your 'table' parameter is not in the Puppies Unlimited&trade; database. Check your spelling and try again.";
	$validparams = FALSE;
	exit;
}

// Check if 'order' Column exists in Table
if (isset($_GET['order']) && $validparams == TRUE){
	$order = $_GET['order'];
	if ($mysqli->query("SELECT $order FROM $table")){
		// Valid 'order' column
	} else{
		echo "Oops! Parameter error:<br />\n";
		echo "The column you specified for your 'order' parameter is not in the $table table. Check your spelling and try again.";
		$validparams = FALSE;
	}
}

// Check if 'limit' parameters are valid
if (isset($_GET['limit']) && $validparams == TRUE){
	$limit = $_GET['limit'];
	// Check for invalid characters (only numerical and ',' allowed)
	if (preg_match("/[^0-9\,]/", $limit) && $validparams == TRUE){
    echo "Oops! Parameter error:<br />\n";
	echo "The value you specified for  your 'limit' parameter contains invalid characters. The 'limit' parameter either uses 1 numeric value or 2 numeric values separated by a comma.";
	$validparams = FALSE;
	}
	// Check for more than 1 comma
	if ((substr_count($limit, ",") > 1) && $validparams == TRUE){
		echo "Oops! Parameter error:<br />\n";
		echo "The value you specified for  your 'limit' parameter contains too many commas. The 'limit' parameter either uses 1 numeric value or 2 numeric values separated by a comma.";
	    $validparams = FALSE;
	}
	// Check for commas at beginning or end
	if (((strpos($limit, ",") === 0) || (substr($limit, -1) == ",")) && $validparams == TRUE){
		echo "Oops! Parameter error:<br />\n";
		echo "The value you specified for  your 'limit' parameter has a comma in the wrong place. The 'limit' parameter either uses 1 numeric value or 2 numeric values separated by a comma.";
		$validparams = FALSE;
	}
	// Check for leading 0's
	/*
	if ($validparams == TRUE){
	$limit_arr = explode(",",$limit);
		foreach($limit_arr as $item) {
			if((string)(intval($item)) !== $item){
			$validparams = FALSE;
			}
		}
		if ($validparams == FALSE){
			echo "Oops! Parameter error:<br />\n";
			echo "The value you specified for  your 'limit' parameter has at least 1 integer with leading 0's. Please remove any leading 0's in your 'limit' parameter.";
		}
	}
	*/
}

// Perform an SQL query
$sql = "SELECT actor_id, first_name, last_name FROM actor WHERE actor_id = $aid";
if (!$result = $mysqli->query($sql)) {
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

// Phew, we made it. We know our MySQL connection and query 
// succeeded, but do we have a result?
if ($result->num_rows === 0) {
    // Oh, no rows! Sometimes that's expected and okay, sometimes
    // it is not. You decide. In this case, maybe actor_id was too
    // large? 
    echo "We could not find a match for ID $aid, sorry about that. Please try again.";
    exit;
}

// Now, we know only one result will exist in this example so let's 
// fetch it into an associated array where the array's keys are the 
// table's column names
$actor = $result->fetch_assoc();
echo "Sometimes I see " . $actor['first_name'] . " " . $actor['last_name'] . " on TV.";

// Now, let's fetch five random actors and output their names to a list.
// We'll add less error handling here as you can do that on your own now
$sql = "SELECT actor_id, first_name, last_name FROM actor ORDER BY rand() LIMIT 5";
if (!$result = $mysqli->query($sql)) {
    echo "Sorry, the website is experiencing problems.";
    exit;
}

// Print our 5 random actors in a list, and link to each actor
echo "<ul>\n";
while ($actor = $result->fetch_assoc()) {
    echo "<li><a href='" . $_SERVER['SCRIPT_FILENAME'] . "?aid=" . $actor['actor_id'] . "'>\n";
    echo $actor['first_name'] . ' ' . $actor['last_name'];
    echo "</a></li>\n";
}
echo "</ul>\n";

// The script will automatically free the result and close the MySQL
// connection when it exits, but let's just do it anyways
$result->free();
$mysqli->close();
?>