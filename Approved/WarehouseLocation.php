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
              wl.WarehouseLocationID,
              wl.WarehouseLocation,
              ua.Name,
              wl.Status,
              wl.UserID
              FROM WarehouseLocation wl
              LEFT JOIN Warehouse w ON w.WarehouseLocationID = wl.WarehouseLocationID
              LEFT JOIN UserAccount ua ON ua.UserID = wl.UserID
              WHERE wl.isDel = 'False' AND wl.Status = 1";
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

