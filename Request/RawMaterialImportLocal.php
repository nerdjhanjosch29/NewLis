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

    
    $RawMaterialImportLocalID = 0;
    if(isset($_GET['RawMaterialImportLocalID']))
    {
      $RawMaterialImportLocalID = $_GET['RawMaterialImportLocalID'];

    }
      $sql = "SELECT 
               ril.RawMaterialImportLocalID
              ,ril.RawMaterial
              ,ril.CategoryID
              ,rc.CategoryName
              ,ril.Import
              ,ril.Local
              ,ril.CreatedAt
              ,ril.UserID
              ,ua.Name
              FROM RawMaterialImportLocal ril
              LEFT JOIN RawMaterialCategory rc ON rc.CategoryID = ril.CategoryID
              LEFT JOIN UserAccount ua ON ua.UserID = ril.UserID
                      WHERE ril.isDel = 'False' AND ril.RawMaterialImportLocalID = ?";
        $params = array($RawMaterialImportLocalID);
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

