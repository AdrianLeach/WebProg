<?php session_start();

?>
<!-- 	Adrian Leach - Web Programming Assignment 1 - November 2014
		This script is designed to allow a user to view all of the content contained within their own
		webfolio.  To achieve this the script will eventually use $_SESSION to pull the user's stored
		student ID, for now though it is being tested with hard-coded values.

-->
<html>
	<body>
		<?php 
			//include file containing log-in credentials
			require_once 'login.php';
			//store the connection in a variable.  connection requires 4 arguments - server(host), user, password and database name
			$db = mysqli_connect($server, $user, $pass, $database)
			//if not successful, display the actual error, and then terminate the program so as not to reveal further (irrelevant) error messages 
				or trigger_error("The connection was not successful. The returned error is: <BR>". mysqli_connect_error(), E_USER_ERROR);

			//the final application will use $_SESSION to retrieve the stored studentId
			//$owner = $_SESSION['studentId'];
			$owner = 1;	
			$query = "SELECT title AS Title, content AS Content, image AS Image, dateAdded AS Date_Added
						FROM Content
						WHERE owner = $owner
						ORDER BY dateAdded ASC";

			
			$safeQuery = strip_tags($query);
			//save the results of the query OR return a comprehensive error message including SQL used if the query is unsuccessful.
			$result = mysqli_query($db, $safeQuery) 
				or trigger_error("The query failed! The SQL was:<BR/> $query.<BR/><BR/>It returned the error: "
				. mysqli_error($db) , E_USER_ERROR);  

			$rows = array();

			while($row = mysqli_fetch_assoc($result)) 
			{ 
				 $rows['Webfolio Content'][] = $row; 
			}
			
			//format mysql result set as javascript online notation 
			print json_encode($rows);
	
		?>
	</body>
</html>


