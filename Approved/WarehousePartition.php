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
                wp.WarehousePartitionID,
                wp.WarehouseID,
                wp.WarehousePartitionName,
                wp.MaximumCapacity,
                wp.TotalWeight,
                wp.TotalQuantity,
                ua.Name,
                wp.Status,
                wp.UserID
                FROM WarehousePartition wp
                LEFT JOIN Warehouse w ON w.WarehouseID = wp.WarehouseID 
                LEFT JOIN UserAccount ua ON ua.UserID = wp.UserID
                WHERE wp.isDel = 'False' AND wp.Status = 1";
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

