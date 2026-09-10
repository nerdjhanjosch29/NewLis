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

$WarehouseID = 0;
    if(isset($_GET['WarehouseID']))
    {
      $WarehouseID = $_GET['WarehouseID'];
    }
      $sql = "SELECT 
              w.WarehouseID,
              w.WarehouseLocationID,
              w.Warehouse_Name,
              w.MaximumCapacity,
              w.MinimumCapacity,
              w.TotalQuantity,
              w.TotalWeight,
              w.Remarks,
              w.CreatedAt,
              ua.Name,
              w.UserID
              FROM Warehouse w
              LEFT JOIN WarehouseLocation wl ON wl.WarehouseLocationID = w.WarehouseLocationID
              LEFT JOIN UserAccount ua ON ua.UserID = wl.UserID
              WHERE w.isDel = 'False' AND w.WarehouseID = ?";
        $params = array($WarehouseID);
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

