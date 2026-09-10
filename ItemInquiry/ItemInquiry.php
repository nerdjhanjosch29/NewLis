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
                rl.RawMaterialImportLocalID
                ,rm.RawMaterialID
                ,rl.CategoryID
                ,rl.RawMaterial AS GeneralName
                ,rm.RawMaterial AS InventoryName
                ,rc.CategoryName AS QA_CategoryName

                ,rl.Import
                ,rl.Local
                ,ISNULL((SELECT SUM(wps.RawMatsWeight)FROM WarehousePartitionStock wps WHERE rm.RawMaterialID = wps.RawMaterialID 
                AND wps.RawMatsWeight > 0),0)AS RawMatsWeight
                FROM RawMaterialList rl 
                LEFT JOIN RawMaterial rm ON rl.RawMaterialimportLocalID = rm.RawMaterialImportLocalID
                LEFT JOIN RawMaterialCategory rc ON rc.CategoryID = rl.CategoryID

                GROUP BY
                rl.RawMaterialImportLocalID
                ,rm.RawMaterialID
                ,rl.CategoryID
                ,rl.RawMaterial
                ,rm.RawMaterial 
                ,rc.CategoryName 

                ,rl.Import
                ,rl.Local
                ;
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

