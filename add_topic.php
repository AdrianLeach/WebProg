<!-- 	Adrian Leach - Web Programming Assignment 1 - November 2014
		This script lets a user upload new content to their webfolio.
		As a user is free to enter their own data, the database is vulnerable to SQL injection, as well
		as malicious code that attempts to run on other users' machines.  Therefore considerable effort was
		made to ensure the script was secure from attack.  Output is a message advising the user their entry
		was successful - or an error at the stage of the process where the problem occurred.

-->
<html>
	<body>
		<?php //insert query to add a new content topic to a user's webfolio using a prepared statement
			
			//include file containing log-in credentials
			require_once 'login.php';
			//store the connection in a variable.  connection requires 4 arguments - server(host), user, password and database name
			$db = mysqli_connect($server, $user, $pass, $database)
			//if not successful, display the actual error, and then terminate the program so as not to reveal further (irrelevent) error messages 
				or trigger_error("The connection was not successful. The returned error is: <BR>". mysqli_connect_error(), E_USER_ERROR);

			//the actual application would use $_SESSION to retain the logged in user's ID	
			//$owner = $_SESSION['studentId'];
			$owner = 1;
			$title = $_GET['title']; 
			$content = $_GET['content']; 
			$image = "images/linux_mint.png";
			
			//sanitise input by converting HTML characters to text and removing spaces before and after input
			//without spaces an SQL comment cannot be made, which protects against malicious code that is in
			//comments from avoiding detection			
			$safeTitle = htmlentities(trim($title));
			$safeContent = htmlentities(trim($content));

			//define the query to be used.  dateAdded is given a value, all other fields are given placeholders
			//date needs to be set here - now() will be mistaken by PHP as a function if assigned to a variable
			$query = "INSERT INTO Content (owner, title, content, image, dateAdded)
						VALUES (?, ?, ?, ?, NOW())";	
						
			//create a variable to hold the statement
			$stmt = mysqli_stmt_init($db);
			//prepare the statement, return error if this is not successful
			if(!mysqli_stmt_prepare($stmt, $query))
			{
				echo "It was not possible to prepare the statement.<BR/>";
				exit("The program will now exit.");
			}
			
			/*bind parameters required by the query to the statement variable
			requires 3 parameters - name of statement variable, datatype of each value (in order), values themselves
			datatype of variable - string = s, integer = i, blob = b, double = d
			ref - http://markonphp.com/simple-insert-mysqli/ */
			$bind = mysqli_stmt_bind_param($stmt, "isss", $owner, $safeTitle, $safeContent, $image);
			//error check to ensure bind was successful
			if($bind === false)
			{
				trigger_error("Error encountered during binding of parameters.", E_USER_ERROR);
			}
			//execute statement and check for error
			$execute = mysqli_stmt_execute($stmt);
			if($execute === false)
			{
				trigger_error("Error during execution of statement." . mysqli_stmt_error($stmt), E_USER_ERROR);
			}
			echo "Thank you.  Your topic has been created, and you will now be returned to your webfolio.";
			//close the statement
			mysqli_stmt_close($stmt); 
		?>
	</body>
</html>