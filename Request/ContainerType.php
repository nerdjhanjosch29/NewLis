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

      $ContainerTypeID = 0;
      if(isset($_GET['ContainerTypeID']))
      {
        $ContainerTypeID = $_GET['ContainerTypeID'];
      }
      $sql = "SELECT 
				ct.Container
				,ct.ContainerTypeID
				,ct.CreatedAt
				,ct.UserID
				,ua.Name
        FROM ContainerType ct
			  LEFT JOIN UserAccount ua ON ua.UserID = ct.UserID
        WHERE ct.isDel = 'False' AND ct.ContainerTypeID = ?";
        $params = array($ContainerTypeID);
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

