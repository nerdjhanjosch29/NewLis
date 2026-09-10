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
				cs.ScheduleRotationID
				,cs.TypeID
				,cs.PlantID
				,p.PlantID
				,cs.DateRotation
				,cs.WarehouseLocationID
				,wl.WarehouseLocation
				,cs.UserID
				,ua.Name
        ,cs.Status

        FROM CheckerSchedule cs
			  LEFT JOIN UserAccount ua ON ua.UserID = cs.UserID
			  LEFT JOIN Plant p ON p.PlantID = cs.PlantID
			  LEFT JOIN WarehouseLocation wl ON wl.WarehouseLocationID = cs.WarehouseLocationID
              WHERE cs.isDel = 'False' AND cs.Status = 1";
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

