<?php 
    require_once '../Database/connection.php';
    
$data = json_decode(file_get_contents('php://input'));
    if (sqlsrv_begin_transaction($conn) === false) {
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

    if(isset($data))
    {
        $ShippingTransactionID = 0;
       $Status = 0;
      if($data->ShippingTransactionID)
      {
        $ShippingTransactionID = $data->ShippingTransactionID;
      }
      if($data->Status)
      {
        $Status = $data->Status;
      }
      $sql = "UPDATE ShippingTransaction SET Status = ? WHERE ShippingTransactionID = ?";
        $params = array($Status,$ShippingTransactionID);     
        $stmt1 = sqlsrv_query($conn,$sql,$params); 
        sqlsrv_commit($conn);
        if($stmt1)
        {
          echo 2;
        }
        else
        {
        sqlsrv_rollback($conn);
        echo "Rollback";
        }

    }
       
