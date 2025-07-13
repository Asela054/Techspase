<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'coverup_details';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`ua`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id' ),
	array( 'db' => '`ua`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial' ),
	array( 'db' => '`uc`.`name`', 'dt' => 'name', 'field' => 'name' ),
	array( 'db' => '`u`.`coverdate`', 'dt' => 'coverdate', 'field' => 'coverdate' ),
	array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
	array( 'db' => '`u`.`start_time`', 'dt' => 'start_time', 'field' => 'start_time' ),
	array( 'db' => '`u`.`end_time`', 'dt' => 'end_time', 'field' => 'end_time' ),
	array( 'db' => '`u`.`covering_hours`', 'dt' => 'covering_hours', 'field' => 'covering_hours' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

// require( 'ssp.class.php' );
require('ssp.customized.class.php' );


$joinQuery = "FROM `coverup_details` AS `u` LEFT JOIN `employees` AS `ua` ON (`ua`.`id` = `u`.`id`) LEFT JOIN `branches` AS `ub` ON (`ub`.`id` = `ua`.`emp_location`) LEFT JOIN `departments` AS `uc` ON (`uc`.`id` = `ua`.`emp_department`)";

$extraWhere = "";
$extraWhere .= "ua.deleted = 0 AND ua.status = 1 AND ua.is_resigned = 0";

if(!empty($_POST['department'])){
    $department = $_POST['department'];
    $extraWhere.="uc.id='$department'";
}
if(!empty($_POST['employee'])){
    $employee = $_POST['employee'];
    $extraWhere.="ua.emp_id='$employee'";
}
if(!empty($_POST['location'])){
    $location = $_POST['location'];
    $extraWhere.="ua.emp_location='$location'";
}


echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
