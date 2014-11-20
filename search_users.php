<!-- Adrian Leach - Web Programming Assignment 1 - November 2014
		This script is used to find registered students.  A user can enter enter their search criteria
		which is pulled through to this PHP script and then used in the MySQL query. 
		The output is encoded in JSON format. -->
<html>
	<body>
		<?php 
			//include file containing log-in credentials
			require_once 'login.php';
			//store the connection in a variable.  connection requires 4 arguments - server(host), user, password and database name
			$db = mysqli_connect($server, $user, $pass, $database)
			//if not successful, display the actual error, and then terminate the program so as not to reveal further (irrelevant) error messages 
				or trigger_error("The connection was not successful. The returned error is: <BR>". mysqli_connect_error(), E_USER_ERROR);

			//temporary storage - user can enter any search criteria via URL for now, will use $_POST for app	
			$searchTerm = $_GET['search'];

			$query = "SELECT DISTINCT CONCAT( firstName, ' ', lastName ) AS Name, Faculty
						FROM Student
						LEFT JOIN Register ON Student.studentId = Register.studentId
						LEFT JOIN Course ON Course.courseId = Register.courseId
						WHERE firstName LIKE '%$searchTerm%'
						OR lastName LIKE '%$searchTerm%'
						ORDER BY Name ASC
						LIMIT 0 , 50";

			//convert any HTML characters (e.g. tags) to text, rendering any malicious code unable to run
			$safeQuery = htmlentities(trim($query));			
			//save the results of the query OR return a comprehensive error message including SQL used if the query is unsuccessful.
			$result = mysqli_query($db, $safeQuery) 
				or trigger_error("The query failed! The SQL was:<BR/> $query.<BR/><BR/>It returned the error: "
				. mysqli_error($db) , E_USER_ERROR);  

			//cretae an array, fetch each record in turn and append it to the array
			$rows = array();
			while($row = mysqli_fetch_assoc($result)) 
			{ 
				 $rows['Search Results'][] = $row; 
			}
			
			//format mysql result set as javascript online notation 
			print json_encode($rows);
	
		?>
	</body>
</html>
