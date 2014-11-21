<?php session_start();
//session started to enable studentId to be captured for use in other scripts.
?>
<!-- 	Adrian Leach - Web Programming Assignment 1 - November 2014
		This script inserts a new user into the Student table.
		This script contains a very robust defence against SQL injection and malicious code input.
		Output is provided to the user to advise their data was stored.
		Error checking has been incorporated into the script to provide feedback in the event of a problem.

-->
<html>
	<body>
		<?php 
			//include files containing log-in credentials and constants
			require_once 'login.php';
			//require_once 'constants.php'
			//store the connection in a variable.  connection requires 4 arguments - server(host), user, password and database name
			$db = mysqli_connect($server, $user, $pass, $database)
			//if not successful, display the actual error, and then terminate the program so as not to reveal further (irrelevent) error messages 
				or trigger_error("The connection was not successful. The returned error is: <BR>". mysqli_connect_error(), E_USER_ERROR);

			//store raw user input in each field	
			$firstName_get = $_GET['firstName']; 
			$lastName_get = $_GET['lastName']; 
			$email_get = $_GET['email'];
			$dob_get = $_GET['dob']; 
			$username_get = $_GET['username'];
			$pass_get = $_GET['pass']; 
			//pull image data from $_FILES superglobal
			//$image = $_FILES['userImage']['name'];
			//set the path to the permanent storage location using the constant defined in constants.php
			//$path = UPLOADPATH.$userImage;
			//use temp name of image to identify and locate it, then move it to the images folder on the server
			//$_FILES['userImage']['tmp_name'],$path;
			$userImage = $_GET['userImage'];

			//begin comprehensive sanitation.  First, remove all dangerous characters from string, plus spaces before and after input
			$first = mysqli_real_escape_string($db, trim($firstName_get)); 
			$last = mysqli_real_escape_string($db, trim($lastName_get)); 
			$email = mysqli_real_escape_string($db, trim($email_get));
			$dob = mysqli_real_escape_string($db, trim($dob_get)); 
			$username = mysqli_real_escape_string($db, trim($username_get));
			$pass = mysqli_real_escape_string($db, trim($pass_get)); 

			//Next, run current input (after being escaped and trimmed) through a regular expression
			//create an array to test similar strings
			$test_array_names = array($first,$last);
			//define regex for each test, and error messages to display if a match is found
			//any alphabetical character, space and apostrophe
			$regex = "/[^a-zA-Z '-]+/";
			//any digit and a hyphen
			$regex_dob = "/[^\d-]+/";
			//any alphabetical character, any number, underscore...also allows #, WHY?
			$regex_user = "/[^a-zA-Z0-9_]+/";
			$errorMsg = "Your name input was not valid.  Please do not use symbols or numbers.<BR/>";
			$errorEmail = "Your email address was not accepted.  Please try again.<BR/>";
			$errorDob = "Your date of birth was not accepted.  The format required is YYYY-MM-DD.<BR/>";
			$errorUser = "Your username or password was not accepted.  Please use only letters, numbers and underscores.<BR/>";

			//begin testing - first, user input for first name and last name.
			foreach ($test_array_names as $check)
			{
				if(preg_match($regex, $check))
				{
					echo $errorMsg;
					exit("You will now be returned to the previous page.");
				}
			}
			//filter_var function used in place of regex due to the complication of checking for a valid email address
			//this function actually uses a regex defined specfically for eliminating false email addresses whilst reducing
			//false-positives (addresses that are genuine but flagged as invalid) as possible - https://fightingforalostcause.net/content/misc/2006/compare-email-regex.php
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
			{
				echo $errorEmail;
				exit("You will now be returned to the previous page.");
			}

			//test dob (need to find out how to test for format - currently DD-MM-YYYY will work)
			if(preg_match($regex_dob, $dob))
			{
				echo $errorDob;
				exit("You will now be returned to the previous page.");
			}

			//test username and password
			$test_array_user = array($username,$pass);
			
			foreach ($test_array_user as $check_user)
			{
				if(preg_match($regex_user, $check_user))
				{
					echo $errorUser;
					exit("You will now be returned to the previous page.");
				}
			}	

			//check to see if an image was uploaded, if so, check that there is actually a file present and that the size is larger than 0
			if(isset($userImage))
			{
				if(!is_file($userImage) || filesize($userImage) <= 0)
				{
					echo "Please use a valid image for the file upload.";
					exit("You will now be returned to the previous page.");
				}
			}

			//only when all the above is verified will the query be concatenated
			$query = "INSERT INTO Student (firstName, lastName, email, dob, username, pass, userImage)
						VALUES (?, ?, ?, ?, ?, ?, ?)";

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
			$bind = mysqli_stmt_bind_param($stmt, "sssssss", $first, $last, $email, $dob, $username, $pass, $userImage);
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
			echo "Thank you.  Your registration is now complete.<BR/> You will now be taken to your new webfolio.";
			//close the statement
			mysqli_stmt_close($stmt); 			

			//the following section is required to provide the $_SESSION array with the new user's ID
			$get_studentId = "SELECT studentId FROM Student WHERE username='$username' && pass='$pass'";
			$sid_result = mysqli_query($db, $get_studentId)
			or trigger_error("It was not possible to retrieve the unique ID number. <BR/> 
				For debugging purposes, the SQL used in this query was: 
				<BR/> '$get_studentId' <BR/>" . mysqli_error($db) , E_USER_ERROR);
			
			while($row = mysqli_fetch_array($sid_result))
			{
				$studentId = $row['studentId'];
			}
			//$_SESSION['studentId'] = $studentId;
			//below used for testing purposes to ensure correct value is passed into $studentId
			echo "<br/>Your new user ID is: " . $studentId . " .";
		?>
	</body>
</html>


