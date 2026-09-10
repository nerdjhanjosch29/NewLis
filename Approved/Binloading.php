<?php 
    require_once '../database/connection.php';
    if (sqlsrv_begin_transaction($conn) === false) {
      die( print_r( sqlsrv_errors(), true ));
    }
    $data = json_decode(file_get_contents('php://input'));


    $UserID = "";

    $validateToken = include('../validate_token.php');
    $UserID = $decode->UserID; 

    if(!$validateToken)
    {
      http_response_code(404);
      die();
    }

      $sql = "SELECT 
           bl.BinloadingID
          ,bl.PlantID
          ,p.PlantName
		  ,ua.Name
          ,bl.IntakeID
          ,bl.Status
          ,bl.BinloadingDate
          ,bl.BinloadingDateTime
          ,rm.RawMaterialID
          ,rm.RawMaterial
          ,bl.Quantity
          ,bl.Weight
          ,bl.UserID
      FROM Binloading bl 
          LEFT JOIN Plant p ON bl.PlantID = p.PlantID
          LEFT JOIN RawMaterial rm ON bl.RawMaterialID = rm.RawMaterialID 
		  LEFT JOIN UserAccount ua ON ua.UserID = bl.UserID
		  
          WHERE bl.isDel = 'False' AND bl.Status = 1";
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
?>