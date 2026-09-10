<?php 
require '../Database/connection.php';
$data = json_decode(file_get_contents('php://input'));
if (sqlsrv_begin_transaction($conn) === false) {
  die(print_r(sqlsrv_errors(), true));
}

$data = json_decode(file_get_contents('php://input'));

$UserID = "";
$validateToken = include('../validate_token.php');
$UserID = $decode->UserID;

if(!$validateToken)
{
  http_response_code(404);
  die();
}




try
{
  if(isset($data))
{
    $ShippingTransactionID = 0;
    if($data->ShippingTransactionID)
    {
        $ShippingTransactionID = $data->ShippingTransactionID;
    }
    
    $sql = "UPDATE ShippingTransaction SET Status = 4 WHERE ShippingTransactionID = ? ";
    $params = array($ShippingTransactionID);
    $stmt = sqlsrv_query($conn,$sql,$params);
    if($stmt)
    {
        echo 2;

        $SystemLogID = 0;
        $FunctionID = 2; 
        $TableName = "ShippingTransaction";
        $Activity = "Update ShippingTransaction Status into Compeleted : $ShippingTransactionID";
        $UpdatedData = "0";

        $sql1 = "EXEC [dbo].[SystemLogs]
                @SystemLogID = ?,
                @UserID = ?,
                @FunctionID = ?,
                @TableName = ?,
                @Activity = ?,
                @UpdatedData = ?";

        $paramss = array($SystemLogID,$UserID,$FunctionID,$TableName,$Activity,$UpdatedData);
        $stmtLog = sqlsrv_query($conn, $sql1, $paramss);
    }
}
}
catch(Exception $e)
{
          sqlsrv_rollback($conn);
  header('Content-Type: application/json; charset=utf-8');
        echo 0;

}



?>