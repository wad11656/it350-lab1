<?php
<<<<<<< HEAD
=======
error_reporting(E_ALL);
ini_set('display_errors', '1');
>>>>>>> 1d35559ff6aeff9f2361c062466a801a38c3ccf2

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
if (isset($_GET['columns'])){
	$columns = filter_var($_GET['columns'], FILTER_SANITIZE_STRING);
} else {
	$columns = 'N/A';
}

if (isset($_GET['values'])){
	$values = filter_var($_GET['values'], FILTER_SANITIZE_STRING);
} else {
	$values = 'N/A';
}

if (isset($_GET['fktables'])){
	$fktables = filter_var($_GET['fktables'], FILTER_SANITIZE_STRING);
} else {
	$fktables = 'N/A';
}

if (isset($_GET['fkcolumns'])){
	$fkcolumns = filter_var($_GET['fkcolumns'], FILTER_SANITIZE_STRING);
} else {
	$fkcolumns = 'N/A';
}

$validparams = TRUE;

if ((!isset($_GET['user'])) || (!isset($_GET['secretkey'])) || (!isset($_GET['table'])) || (!isset($_GET['columns'])) || (!isset($_GET['values'])) || (!isset($_GET['fktables'])) || (!isset($_GET['fkcolumns']))){
	echo "Oops! Parameter error:<br />\n";
	echo "All Puppies Unlimited&trade; URL queries require a 'user', 'secretkey', 'table', 'columns', 'values', 'fktables', and 'fkcolumns' parameter. Check you have at least these three in your URL.<br />\n";
	echo "<i>( Example: <b>http://40.117.58.200/it350site/insert_join.php?user=my_user&secretkey=my_secretkey&table=adoption&columns=puppy_id,customer_id&values='Jill','John'&fktables=puppy,customer&fkcolumns=puppy_name,customer_name</b> )</i>";
	$validparams = FALSE;
	exit;
}

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

if ($validparams == TRUE){

	$revised_values = str_replace("%20"," ",$values);
	$revised_values = preg_replace("!&#39;%?[a-zA-Z0-9\-\@\. ]+%?&#39;!","*",$revised_values);
	$values_array = preg_match_all("!&#39;(%?[a-zA-Z0-9\-\@\. ]+%?)&#39;!",$values,$value_matches,PREG_PATTERN_ORDER);

	// The following assumes the fktable order is the same as the fkcolumns order
	$subqueries = array();
	$fktables_array = explode(",",$fktables);
	$fkcolumns_array = explode(",",$fkcolumns);
	for ($i = 0; $i < count($fktables_array); $i++){
		$fktable = $fktables_array[$i];
		$fkcolumn = $fkcolumns_array[$i];

		$subquery = "(SELECT id FROM $fktable WHERE $fkcolumn = ?)";
		$subqueries[] = $subquery;
	}

	for ($i = 0; $i < count($subqueries); $i++){
		$subquery = $subqueries[$i];
		$revised_values = preg_replace('#\*#', $subquery, $revised_values,1);
	}

	$revised_values = str_replace('*', '?', $revised_values);

	// Run the query
	$query_sql = "INSERT INTO $table ($columns) VALUES ($revised_values)";
<<<<<<< HEAD
	print $query_sql;
=======
>>>>>>> 1d35559ff6aeff9f2361c062466a801a38c3ccf2

	if ($stmt = $mysqli->prepare($query_sql)) {
		$types = "";
		foreach ($value_matches[1] as $v) {
			if (preg_match("!^[0-9\.]+$!",$v)) {
				$types .= "d";
			} else {
				$types .= "s";
			}
		}
		$stmt->bind_param($types, ...$value_matches[1]);
		$stmt->execute();
<<<<<<< HEAD
		print $stmt->error;
=======
>>>>>>> 1d35559ff6aeff9f2361c062466a801a38c3ccf2
		$rows_affected = $stmt->affected_rows;
		if ($rows_affected < 0){
			echo "Oops! Parameter Error:<br />\n";
			echo "The syntax of your <b>INSERT</b> query was correct, but did not execute successfully. Make sure the values in your <b>'values'</b> parameter actually exist in their respective tables.";
			$validparams = FALSE;
		}
	}
	else{
   		// Oh no! The query failed. 
		echo "Oops! Execution Error:<br />\n";
		echo "The <b>INSERT</b> did not execute successfully. Please check your syntax.<br />\n";
		echo "<i>( Example: <b>http://40.117.58.200/it350site/insert_join.php?user=my_user&secretkey=my_secretkey&table=adoption&columns=puppy_id,customer_id&values='Jill','John'&fktables=puppy,customer&fkcolumns=puppy_name,customer_name</b> )</i>";
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