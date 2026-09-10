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

        $ShippingLineID = 0;
    if(isset($_GET['ShippingLineID']))
    {
      $ShippingLineID = $_GET['ShippingLineID'];

    }
      $sql = "SELECT 
            sp.ShippingLineID
            ,sp.ShippingLine
            ,sp.ContactPerson
            ,sp.ContactNumber
            ,sp.UserID
            ,ua.Name
            FROM ShippingLine sp

            LEFT JOIN UserAccount ua ON ua.UserID = sp.UserID
         WHERE sp.isDel = 'False' AND sp.ShippingLineID = ?";
        $params = array($ShippingLineID);
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

