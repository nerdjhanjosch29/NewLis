

<?php 

$serverName = "(local)";

$connectionInfo = array("Database"=>"LIS_db", "UID"=>"sa", "PWD"=>"celsun");
$conn = sqlsrv_connect($serverName, $connectionInfo);

// if(!$conn)
// {
//  echo "Not";
// } 

// else
// {
// echo "Successful";

// }

?>