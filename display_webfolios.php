<!-- Adrian Leach - Web Programming Assignment 1 - November 2014
		This script connects to the database, then uses a query to return a result set consisting of all 
		student's names and their course area.  The students first and last names are concatenated and the 
		results are ordered by name.  The query will only include each student once, regardless of if they 
		are studying multiple modules.
		This file contains error checking for the connection and the query as commented below. 
		The result set from the query is output in JSON format -->
<html>
	<body>
		<?php 
			//include file containing log-in credentials
			require_once 'login.php';
			//function to sanitise output before it reaches the user
			function cleanOutput($data)
			{
				htmlentities(trim($data), ENT_QUOTES);
				return $data;
			}
			//store the connection in a variable.  connection requires 4 arguments - server(host), user, password and database name
			$db = mysqli_connect($server, $user, $pass, $database)
			//if not successful, display the actual error, and then terminate the program so as not to reveal further (irrelevant) error messages 
				or trigger_error("The connection was not successful. The returned error is: <BR>". mysqli_connect_error(), E_USER_ERROR);

			$query = "SELECT DISTINCT CONCAT(firstName, ' ', lastName) AS Name, Faculty 
						FROM Student
						LEFT JOIN Register
						ON Student.studentId = Register.studentId
						LEFT JOIN Course
						ON Course.courseId = Register.courseId
						ORDER BY Name ASC
						LIMIT 0, 50";

			//call function
			cleanOutput($query);			
			//save the results of the query OR return a comprehensive error message including SQL used if the query is unsuccessful.
			$result = mysqli_query($db, $query) 
				or trigger_error("The query failed! The SQL was:<BR/> $query.<BR/><BR/>It returned the error: "
				. mysqli_error($db) , E_USER_ERROR);  

			//create an array, fetch each record in turn and append it to the array
			$rows = array();
			while($row = mysqli_fetch_assoc($result)) 
			{ 
				 $rows['Webfolio List'][] = $row; 
			}
			
			//format mysql result set as javascript online notation 
			print json_encode($rows);
	
		?>
	</body>
</html>
