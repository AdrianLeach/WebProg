<?php session_start();
//session started to enable studentId to be captured for use in other scripts.
?>
<!-- 	Adrian Leach - Web Programming Assignment 1 - November 2014
		This script allows a user to edit the content of a topic within the Content table.  This script is a basic
		version of the finalised one, as eventually it will post data to the user, allowing them to see their current 
		topic information. They will then be able to edit and save the record.  Currently, this script simply overwrites
		the stored information as nothing has been presented to the user.
		As the script contains user input robust defence against SQL injection and malicious code input has been included.
		Output is provided to the user to advise their updated data was stored.
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
			//application will use a radio button interface, with the titles of user's topics displayed
			//each choice will be named according to the contentId value stored in the database.
			$contentId = $_GET['contentId'];	
			$title = $_GET['title']; 
			$content_get = $_GET['content']; 
			/*pull image data from $_FILES superglobal
			//$image = $_FILES['userImage']['name'];
			//set the path to the permanent storage location using the constant defined in constants.php
			//$path = UPLOADPATH.$userImage;
			//use temp name of image to identify and locate it, then move it to the images folder on the server
			//$_FILES['userImage']['tmp_name'],$path;  */
			$image_get = "images/linux_mint.png";

			//begin sanitation.  First, remove all dangerous characters from string, plus spaces before and after input
			$title = mysqli_real_escape_string($db, trim($title));
			//mysqli_real_escape_string removed the data from the content field 
			$content = htmlentities(trim($content_get));
			$image = mysqli_real_escape_string($db, trim($image_get));
			
			/*check to see if an image was uploaded, if so, check that there is actually a file present and that the size is larger than 0
			if(isset($userImage))
			{
				if(!is_file($userImage) || filesize($userImage) <= 0)
				{
					echo "Please use a valid image for the file upload.";
					exit("You will now be returned to the previous page.");
				}
			} */

			//query to be used within prepared statement
			$query = "UPDATE Content SET title=?, content=?, image=?
						WHERE contentId= ?";

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
			$bind = mysqli_stmt_bind_param($stmt, "sssi", $title, $content, $image, $contentId);
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
			echo "Thank you for updating this section of your webfolio.  Your changes have been successfully saved.<BR/>
			We will now return you to your main page";
			//close the statement
			mysqli_stmt_close($stmt); 			
		?>
	</body>
</html>


