<?php
	// This script is a query that INSERTs a record in the users table
	// Check that form has been submitted:
	$errors = array ();  // Initialize an error array.
	// Check for first name:
	$first_name = trim ( $_POST[ 'first_name' ] );
	if ( empty( $first_name ) ) {
		$errors = 'You forgot to enter your first name.';
	}
	
	// Check for last name:
	$last_name = trim ( $_POST[ 'last_name' ] );
	if ( empty( $last_name ) ) {
		$errors = 'You forgot to enter your last name.';
	}
	
	// Check for last name:
	$email = trim ( $_POST[ 'email' ] );
	if ( empty( $email ) ) {
		$errors = 'You forgot to enter your email address.';
	}
	
	// Check for a password and match against the confirmed password:
	$password1 = trim ( $_POST[ 'password' ] );
	$password2 = trim ( $_POST[ 'password2' ] );
	
	if ( !empty( $password1 ) ) {
		if ( $password1 !== $password2 ) {
			$errors[] = 'Your two passwords did not match.';
		}
	} else {
		$errors[] = 'You forgot to enter your password.';
	}
	if ( empty( $errors ) ) {
		try {
			// Register the user in the database...
			// Hash password current 60 characters but can icnrease
			$hashed_passcode = password_hash ( $password1, PASSWORD_DEFAULT );
			require ( 'mysqli_connect.php' );      // Connect to the db.
			
			// Make the query:
			$query = "INSERT INTO users(first_name, last_name, email, password) ";
			$query .= "VALUES (?, ?, ?, ?)";
			$q = mysqli_stmt_init ( $dbcon );
			mysqli_stmt_prepare ( $q, $query );
			// use prepared statement to ensure that only text is inserted
			// bind field to SQL statement.
			mysqli_stmt_bind_param ( $q, 'ssss', $first_name, $last_name, $email, $hashed_passcode );
			// execute query
			mysqli_stmt_execute ( $q );
			if ( mysqli_stmt_affected_rows ( $q ) == 1 ) {
				header ( "location: register-thanks.php" );
				exit();
			} else {
				// Public message:
				$errorstring = "<p class='text-center col-sm-8' style='color: red;'>";
				$errorstring .= "System Error<br />You cannot be registered due ";
				$errorstring .= "to a system error. We apologise for any inconvenience.</p>";
				echo "<p class='text-center col-sm-2' style='color:red'>$errorstring</p>";
				// Debugging message below do not use in production
				// echo '<p>' . mysqli_error($dbcon) . '<br><br>Query: ' . $query . '</p>';
				mysqli_close ( $dbcon );   // Close the database connection.
				// include footer then close program to stop execution.
				echo '<footer class="jumbotron text-center col-sm-12" style="padding-bottom: 1px; padding-top:8px;">include("footer.php")</footer>';
				exit();
			}
		} catch ( Exception $e ) {
			// print "An Exception occurred. Message: " . $e->getMessage();
			print "An Exception occurred. Message: " . $e->getMessage();
		} catch ( Error $e ) {
			print "The system is busy please try again later.";
		}
	} else {
		// Report the errors.
		$errorstring = "Error! The following error(s) occured:<br>";
		foreach ( $errors as $msg ) {
			$errorstring .= " - $msg<br>\n";
		}
		$errorstring .= "Please try again.<br>";
		echo "<p class='text-center col-sm-2' style='color:red'>$errorstring</p>";
	}