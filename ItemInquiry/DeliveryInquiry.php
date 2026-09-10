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
                ut.UnloadingTransactionID,
                ut.DrNumber,
                ut.DateUnload,
                ut.WarehouseID,
                rm.RawMaterial,
                ISNULL(SUM(wps.RawMatsWeight), 0) AS RawMaterialWeight,
                ut.WarehouseLocationID,
                ut.WarehousePartitionID
            FROM UnloadingTransaction ut
            LEFT JOIN WarehousePartitionStock wps
                ON wps.UnloadingTransactionID = ut.UnloadingTransactionID
                AND wps.RawMaterialID = ut.RawMaterialID
                AND wps.RawMatsWeight > 0
            LEFT JOIN RawMaterial rm
                ON rm.RawMaterialID = ut.RawMaterialID
            WHERE ut.isDel = 'False'
            GROUP BY
                ut.UnloadingTransactionID, 
                ut.DrNumber,
                ut.DateUnload,
                ut.WarehouseID,
                rm.RawMaterial,
                ut.WarehouseLocationID,
                ut.WarehousePartitionID
            ORDER BY ut.DateUnload ASC;
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

