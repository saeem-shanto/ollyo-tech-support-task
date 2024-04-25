<?php

//user_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		if (!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL)) {
			// invalid email check
			$out["email_warning"]="Enter valid Email";
			echo json_encode($out);
			return;
		}
		$email_checking_query = "Select user_email from user_details WHERE user_email= :user_email";
		$email_checking_statement = $connect-> prepare($email_checking_query);
		$email_checking_statement->execute(
			array(
				':user_email'		=>	$_POST["user_email"],
				)
			);
		$result = $email_checking_statement->fetchAll();
		if(count($result)>0 ){
			$out["email_warning"]="Email already exists";
			echo json_encode($out);
			return;
		}
		
		$query = "
		INSERT INTO user_details (user_email, user_password, user_name, user_type, user_status) 
		VALUES (:user_email, :user_password, :user_name, :user_type, :user_status)
		";	
		$statement = $connect->prepare($query);
		$statement->execute(
				array(
						':user_email'		=>	$_POST["user_email"],
						':user_password'	=>	password_hash($_POST["user_password"], PASSWORD_DEFAULT),
						':user_name'		=>	$_POST["user_name"],
						':user_type'		=>	'user',
						':user_status'		=>	'active'
						)
					);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			if($_POST["user_name"]){
				$out["show_message"] = "New User(".$_POST["user_name"].") Added";
			}else{
				$out["show_message"] = "New User Added";
			}
			echo json_encode($out);
			return;
		}
	}
					
					
					if($_POST['btn_action'] == 'fetch_single')
					{
						$query = "
						SELECT * FROM user_details WHERE user_id = :user_id
						";
						$statement = $connect->prepare($query);
						$statement->execute(
							array(
								':user_id'	=>	$_POST["user_id"]
								)
							);
							$result = $statement->fetchAll();
							foreach($result as $row)
							{
								$output['user_email'] = $row['user_email'];
								$output['user_name'] = $row['user_name'];
							}
							echo json_encode($output);
						}
						if($_POST['btn_action'] == 'Edit')
						{
							if($_POST['user_password'] != '')
							{
								$query = "
								UPDATE user_details SET 
								user_name = '".$_POST["user_name"]."', 
								user_email = '".$_POST["user_email"]."',
								user_password = '".password_hash($_POST["user_password"], PASSWORD_DEFAULT)."' 
								WHERE user_id = '".$_POST["user_id"]."'
								";
							}
							else
							{
								$query = "
								UPDATE user_details SET 
								user_name = '".$_POST["user_name"]."', 
								user_email = '".$_POST["user_email"]."'
								WHERE user_id = '".$_POST["user_id"]."'
								";
							}
							$statement = $connect->prepare($query);
							$statement->execute();
							$result = $statement->fetchAll();
							if(isset($result))
							{
								echo 'User Details Edited';
							}
						}
						if($_POST['btn_action'] == 'delete')
						{
							$status = 'Active';
							if($_POST['status'] == 'Active')
							{
								$status = 'Inactive';
							}
							$query = "
							UPDATE user_details 
							SET user_status = :user_status 
							WHERE user_id = :user_id
							";
							$statement = $connect->prepare($query);
							$statement->execute(
								array(
									':user_status'	=>	$status,
									':user_id'		=>	$_POST["user_id"]
									)
								);	
								$result = $statement->fetchAll();	
								if(isset($result))
								{
									echo 'User Status change to ' . $status;
								}
							}
						}
						
						?>