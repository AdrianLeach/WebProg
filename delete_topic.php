<?php session_start();
?>
<!-- 	Adrian Leach - Web Programming Assignment 1 - November 2014
		This script deletes a topic from the Content table.
		A prepared statement is used to protect against SQL injection.
		Output is provided to the user to advise the content was removed.
		Error checking in the script is limited to the connection and the prepared statement as there is not
		anything else that should produce an issue.
-->
<html>
	<body>
		<?php //join query on database with error checking, output results in JSON format
			//include file containing log-in credentials
			require_once 'login.php';
			//store the connection in a variable.  connection requires 4 arguments - server(host), user, password and database name
			$db = mysqli_connect($server, $user, $pass, $database)
			//if not successful, display the actual error, and then terminate the program so as not to reveal further (irrelevant) error messages 
				or trigger_error("The connection was not successful. The returned error is: <BR>". mysqli_connect_error(), E_USER_ERROR);

			//the final application will use $_SESSION to retrieve the stored contentId.
			//a radio button will be included on the page to enable users to select which topic they want to remove
			//for now, a $_GET request is used to mimic this functionality	
			//$contentId = $_SESSION['contentId'];
			$contentId = $_GET['content'];	

			$query = "DELETE FROM Content WHERE contentId = ?";

			$stmt = mysqli_stmt_init($db);
			//error check to test preparation was successful
			if(!mysqli_stmt_prepare($stmt, $query))
			{
				echo "It was not possible to prepare the statement.<BR/>";
				exit("The program will now exit.");
			}
			//i used to signify contentId is a numeric value
			$bind = mysqli_stmt_bind_param($stmt, "i", $contentId);
			//error check to ensure bind was successful
			if($bind === false)
			{
				trigger_error("Error encountered during binding of parameters.", E_USER_ERROR);
			}
			//execute and check for error
			$execute = mysqli_stmt_execute($stmt);
			if($execute === false)
			{
				trigger_error("Error during execution of statement." . mysqli_stmt_error($stmt), E_USER_ERROR);
			}
			echo "The topic and its contents have been deleted. <BR/> 
			You will now be returned to your webfolio.";
			//close the statement
			mysqli_stmt_close($stmt); 		
		?>
	</body>
</html>


