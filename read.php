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

	// Print MySQL Errors
	echo "Errno: " . $mysqli->connect_errno . "<br />\n";
	echo "Error: " . $mysqli->connect_error . "<br />\n";
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

// Check if 'order' Column exists in Table
if (isset($_GET['order']) && $validparams == TRUE){
	$order =  filter_var($_GET['order'], FILTER_SANITIZE_STRING);
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
	$limit = filter_var($_GET['limit'], FILTER_SANITIZE_STRING);
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
	// Check if values go out of range
	if ($validparams == TRUE){
		$limit_arr = explode(",",$limit);
		$sql = 'SELECT * FROM ' . $table;
		if(intval($limit_arr[0])+intval($limit_arr[1]) > mysqli_num_rows($mysqli->query($sql))){
			echo "Oops! Parameter error:<br />\n";
			echo 'The value you specified for  your \'limit\' parameter is out of range for the table. Adjust your \'limit\' value to fit within the <b>' . mysqli_num_rows($mysqli->query($sql)) . '</b> rows that are in table \'' . $table . '\'.';
			$validparams = FALSE;
		}
	}
	// Check for leading 0's
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
}

// Perform an SQL query
if ($validparams == TRUE){
	// Append `SELECT * FROM`
	$sql = 'SELECT * FROM ' . $table;
	// Append `LIMIT [limit]`
	if (isset($_GET['limit'])){
		$sql .= ' LIMIT ' . $limit;
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

	// Append `ORDER BY [order]`
	if (isset($_GET['order'])){
		$sql .= ' ORDER BY ' . $order;
	}

	// HAS CONDITIONS SQL Query:
	if (isset($_GET['conditions'])){
		if ($stmt = $mysqli->prepare($sql)){
			if ($clean_conditions != False) {
				$types = "";
				foreach ($condition_matches[1] as $c) {
					if (preg_match("![0-9\.]+!",$c)) {
						$types .= "d";
					} else {
						$types .= "s";
					}
				}
				$stmt->bind_param($types, ...$condition_matches[1]);
			}
			$stmt->execute();
		}
		else{
   			// Oh no! The query failed. 
			echo "Sorry, the website is experiencing problems.";

    		// Again, do not do this on a public site, but we'll show you how
    		// to get the error information
			echo "Error: Our query failed to execute and here is why: \n";
			echo "Query: " . $sql . "\n";
			echo "Errno: " . $mysqli->errno . "\n";
			echo "Error: " . $mysqli->error . "\n";
			$validparams = FALSE;
			exit;
		}

		if ($validparams == TRUE){
			// Print result of SQL query as JSON
			$result = $stmt->get_result();
			$result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
			echo json_encode($result_array);
		}
	}

	// WITHOUT CONDITIONS SQL query:
	else if ($validparams == TRUE){
		$sql = 'SELECT * FROM ' . $table;
		if (isset($_GET['limit'])){
			$sql .= ' LIMIT ' . $limit;
		}
		if (isset($_GET['conditions'])){
			$sql .= ' WHERE ' . filter_var($_GET['conditions'], FILTER_SANITIZE_STRING);
		}
		if (isset($_GET['order'])){
			$sql .= ' ORDER BY ' . $order;
		}
		// Print result of SQL query as JSON
		if($result = $mysqli->query($sql)){
			$result_array = $result->fetch_all(MYSQLI_ASSOC);
			echo json_encode($result_array);
		}
		if (!$result = $mysqli->query($sql)) {
   			// Oh no! The query failed. 
			echo "Oops! Execution Error:<br />\n";
			echo "The <b>READ</b> did not execute successfully. Please check your syntax.<br />\n";
			echo "<i>( Example: <b>http://192.168.50.92/it350site/read.php?user=my_user&secretkey=my_secretkey&table=puppy</b> )</i>";
			exit;
		}
	}
}

// The script will automatically free the result and close the MySQL
// connection when it exits, but let's just do it anyways
$result->free();
$mysqli->close();
?>