v<?php 
    require_once '../database/connection.php';
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
       $WarehouseLocationID = 0;
      if(isset($_GET['WarehouseLocationID']))
      {
        $WarehouseLocationID = $_GET['WarehouseLocationID'];
      }
      $sql = "UPDATE WarehouseLocation SET Status = 1 WHERE WarehouseLocationID = ?";
        $params = array($WarehouseLocationID);     
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

