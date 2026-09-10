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
       $PortOfDischargeID = 0;
      if(isset($_GET['PortOfDischargeID']))
      {
        $PortOfDischargeID = $_GET['PortOfDischargeID'];
      }
      $sql = "UPDATE PortOfDischarge SET Status = 1 WHERE PortOfDischargeID = ?";
        $params = array($PortOfDischargeID);     
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

