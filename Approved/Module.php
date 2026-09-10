<?php 
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
      $sql = "SELECT 
            m.ModuleID
            ,m.ModuleName
            ,m.UserID
            ,m.CreatedAt
            ,m.Status
            FROM Module m 
LEFT JOIN UserAccount ua ON ua.UserID = m.UserID
WHERE m.isDel = 'False' AND m.Status = 1

";
        $stmt1 = sqlsrv_query($conn,$sql); 
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

