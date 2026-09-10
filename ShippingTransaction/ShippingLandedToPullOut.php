<?php

require '../Database/connection.php';
$data = json_decode(file_get_contents('php://input'));
if ( sqlsrv_begin_transaction( $conn ) === false ) {
  die( print_r( sqlsrv_errors(), true ));
} 
$UserID = ""; 
$validateToken = include('validate_token.php');
$UserID = $decode->UserID;
if(!$validateToken)
{
  http_response_code(404);
  die();
}
      if(isset($data))
      {
              $ShippingTransactionID = 0;
              $Status = 3;
              $MBL = "";
              $UserID = "";
          if($data->ShippingTransactionID)
          {
          $ShippingTransactionID = $data->ShippingTransactionID;
          }
          $sql = "SELECT MBL FROM ShippingTransaction WHERE ShippingTransactionID = ?";
          $params = array($ShippingTransactionID);
          $stmt1 = sqlsrv_query($conn, $sql, $params);
          while($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC))
          {
              $MBL = $row['MBL'];
          }
          $sql = "UPDATE ShippingTransaction SET Status = ? WHERE ShippingTransactionID = ?";
          $params = array($Status, $ShippingTransactionID);
          $stmt = sqlsrv_query($conn, $sql, $params);
              //   var_dump($stmt);
      if ($stmt) {
            $SystemLogID = 0;
          $FunctionID = 2; 
          $TableName = "ShippingTransaction";
          $Activity = "Update ShippingTransaction Status into PullOut : ".$MBL;
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

?>