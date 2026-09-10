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

    $GuardID = 0;
    if(isset($_GET['GuardID']))
    {
      $GuardID = $_GET['GuardID'];
    }
      $sql = "SELECT 
            g.GuardID
            ,g.GuardName
            ,g.UserID
            ,g.CreatedAt
            ,ua.Name
            FROM Guard g
        LEFT JOIN UserAccount ua ON ua.UserID = g.UserID
         WHERE g.isDel = 'False' AND g.GuardID = ?";
        $params = array($GuardID);
        $stmt1 = sqlsrv_query($conn,$sql,$GuardID); 
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

