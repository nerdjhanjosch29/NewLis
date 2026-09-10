<?php 
    require_once '../database/connection.php';
    if (sqlsrv_begin_transaction($conn) === false) {
      die( print_r( sqlsrv_errors(), true ));
    }

    $UserID = "";
    $CompanyID = "";
    $validateToken = include('../validate_token.php');
    $UserID = $decode->UserID; 
    $CompanyID = $decode->CompanyID;
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
      $sql = "SELECT 
          pd.PortOfDischargeID
          ,pd.PortOfDischarge
          ,pd.CreatedAt
          ,pd.UserID
          ,ua.Name
          FROM PortOfDischarge pd
          LEFT JOIN UserAccount ua ON ua.UserID = pd.UserID
           WHERE pd.isDel = 'False' AND pd.PortOfDischargeID = ?";
              $params = array($PortOfDischargeID);
        $stmt1 = sqlsrv_query($conn,$sql,$params); 
        if($stmt1)
        {
          $json = array();
          do {
            while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
            $json[] = $row;     	
            }
          } while (sqlsrv_next_result($stmt1));
        
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode($json);
        }
        else
        {
        sqlsrv_rollback($conn);
        echo "Rollback";
        }

