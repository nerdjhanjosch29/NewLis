<?php 
require '../Database/connection.php';
$data = json_decode(file_get_contents('php://input'));
if ( sqlsrv_begin_transaction( $conn ) === false ) {
  die( print_r( sqlsrv_errors(), true ));
} 
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
        if($data->ShippingTransactionID)
        {
        $ShippingTransactionID = $data->ShippingTransactionID;
        }
            $Status = 1;
            // $ATA = "";
            $MBL = "";
            $sql = "SELECT MBL FROM ShippingTransaction WHERE ShippingTransactionID = ?";
            $params = array($ShippingTransactionID);
            $stmt1 = sqlsrv_query($conn, $sql, $params);
            while($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC))
            {
                $MBL = $row['MBL'];
            }
        $sql = "UPDATE ShippingTransaction SET Status = ?, ATA = NULL WHERE ShippingTransactionID = ?";
        $params = array($Status, $ShippingTransactionID);
        $stmt = sqlsrv_query($conn, $sql, $params);
            //   var_dump($stmt);
            if ($stmt) {
            $SystemLogID = 0;
            $FunctionID = 2; 
            $TableName = "ShippingTransaction";
            $Activity = 'Revert ShippingTransaction Status into Sailing : '. $MBL;
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