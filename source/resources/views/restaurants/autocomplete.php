<?php
/** DB connection config */
define('DB_SERVER', 'localhost');
define('DB_USER', 'username');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'database_name');
define('DB_PORT', '3306');

if (isset($_GET['term']))
{
	$return_arr = array();

	try
	{
		$conn = new PDO('mysql:host='.DB_SERVER.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare('SELECT country FROM countries WHERE country LIKE :term');
		$stmt->execute(array('term' => '%'.$_GET['term'].'%'));

		while ($row = $stmt->fetch())
		{
			$return_arr[] =  $row['country'];
		}

	}
	catch (PDOException $e)
	{
		echo 'ERROR: ' . $e->getMessage();
	}

    /** Toss back results as JSON encoded array. */
    echo json_encode($return_arr);
}

?>
