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

    $WarehousePartitionID = 0;
    if(isset($_GET['WarehousePartitionID']))
    {
      $WarehousePartitionID = $_GET['WarehousePartitionID'];

    }
      $sql = "SELECT 
                wp.WarehousePartitionID,
                wp.WarehouseID,
                wp.WarehousePartitionName,
                wp.MaximumCapacity,
                wp.TotalWeight,
                wp.TotalQuantity,
                ua.Name,
                wp.UserID
                FROM WarehousePartition wp
                LEFT JOIN Warehouse w ON w.WarehouseID = wp.WarehouseID 
                LEFT JOIN UserAccount ua ON ua.UserID = wp.UserID
                WHERE wp.isDel = 'False' AND wp.WarehousePartitionID = ?";
        $params = array($WarehousePartitionID); 
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

