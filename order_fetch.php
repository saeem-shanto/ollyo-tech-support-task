<?php

//order_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
	SELECT * FROM inventory_order WHERE 
";

if($_SESSION['type'] == 'user')
{
	$query .= 'user_id = "'.$_SESSION["user_id"].'" AND ';
}

if(isset($_POST["search"]["value"]))
{
	$query .= '(inventory_order_id LIKE "'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_name LIKE "'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_name LIKE "% '.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_total LIKE "'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_status LIKE "'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_date LIKE "'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
{
	$order  = ["inventory_order_id","inventory_order_name","inventory_order_total","payment_status","inventory_order_status","inventory_order_date"];
	$query .= 'ORDER BY '.$order[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY inventory_order_id DESC ';
}

if($_POST["length"] != -1)
{
	$start = intval($_POST['start'] ??	"0") ;
    $length = intval($_POST['length'] ?? "0" );
    // Ensure start and length are non-negative
	if($length<=0){

	}else{
		$query .= 'LIMIT ' . $start . ', ' . $length;
	}
}

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
	$payment_status = '';

	if($row['payment_status'] == 'cash')
	{
		$payment_status = '<span class="label label-primary">Cash</span>';
	}
	else
	{
		$payment_status = '<span class="label label-warning">Credit</span>';
	}

	$status = '';
	if($row['inventory_order_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['inventory_order_id'];
	$sub_array[] = $row['inventory_order_name'];
	$sub_array[] = $row['inventory_order_total'];
	$sub_array[] = $payment_status;
	$sub_array[] = $status;
	$sub_array[] =  date_format(date_create($row['inventory_order_date'])," j F Y");
	if($_SESSION['type'] == 'master')
	{
		$sub_array[] = get_user_name($connect, $row['user_id']);
	}
	$sub_array[] = '<a href="view_order.php?pdf=1&order_id='.$row["inventory_order_id"].'" class="btn btn-info btn-xs">View PDF</a>';
	$sub_array[] = '<button type="button" name="update" id="'.$row["inventory_order_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	$sub_array[] = '<button type="button" name="delete" id="'.$row["inventory_order_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["inventory_order_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM inventory_order");
	$statement->execute();
	return $statement->rowCount();
}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"    			=> 	$data
);	

echo json_encode($output);

?>