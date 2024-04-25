<?php

//edit_profile.php

include('database_connection.php');

if(isset($_POST['user_name']))
{
	
	if($_POST["user_old_password"] !=''){
		$query = "SELECT *
		FROM user_details 
		WHERE user_id = '".$_SESSION["user_id"]."'";
		$check_old_password_statement = $connect->prepare($query);
		$check_old_password_statement->execute();
		$count = $check_old_password_statement->rowCount();
		if($count > 0)
		{
			$result = $check_old_password_statement->fetchAll();
			foreach($result as $row)
			{
					if(password_verify($_POST["user_old_password"], $row["user_password"]))
					{ 

					}
					else
					{
						echo '<div class="alert alert-danger">Old Password did not match. Enter it currently.</div>';
						return;
					}
			}
		}
		else
		{
			echo '<div class="alert alert-danger">Wrong query(Custom)!</div>';
			return;
		}
	}
	if($_POST["user_new_password"] != '')
	{
		$query = "
		UPDATE user_details SET 
		user_name = '".$_POST["user_name"]."', 
		user_email = '".$_POST["user_email"]."', 
		user_password = '".password_hash($_POST["user_new_password"], PASSWORD_DEFAULT)."' 
		WHERE user_id = '".$_SESSION["user_id"]."'
		";
	}
	else
	{
		$query = "
		UPDATE user_details SET 
		user_name = '".$_POST["user_name"]."', 
		user_email = '".$_POST["user_email"]."'
		WHERE user_id = '".$_SESSION["user_id"]."'
		";
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	if(isset($result))
	{
		echo '<div class="alert alert-success">Profile Edited</div>';
	}
}

?>