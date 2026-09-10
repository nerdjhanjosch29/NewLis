<?php 
    require_once '../database/connectionLIS.php';
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
                    w.WarehouseID
                    ,w.Warehouse_Name
                    ,w.WarehouseLocationID
                    ,wl.WarehouseLocation
                    ,ISNULL((SELECT SUM(wps.RawMatsWeight) FROM WarehousePartitionStock wps
                    WHERE wps.WarehouseID = w.WarehouseID AND wps.RawMatsWeight > 0),0

                    )AS WarehouseStock
                    FROM Warehouse w
                    LEFT JOIN WarehouseLocation wl ON wl.WarehouseLocationID = w.WarehouseLocationID

                    GROUP BY wl.WarehouseLocationID,
                    w.WarehouseID
                    ,w.Warehouse_Name
                    ,w.WarehouseLocationID
                    ,wl.WarehouseLocation
                    ORDER BY WarehouseStock DESC";

                    
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

