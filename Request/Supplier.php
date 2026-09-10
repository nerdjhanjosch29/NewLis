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

      $SupplierID = 0;
    if(isset($_GET['SupplierID']))
    {
      $SupplierID = $_GET['SupplierID'];

    }
      $sql = "SELECT 
            s.SupplierID
            ,s.Supplier
            ,s.ContactPerson
            ,s.ContactNumber
            ,s.Currency
            ,s.Indentor
            ,s.IndentorAddress
            ,s.Product
            ,s.Origin
            ,s.Source
            ,s.Terms
            ,s.UserID
            ,ua.Name
            FROM Supplier s

            LEFT JOIN UserAccount ua ON ua.UserID = s.UserID
         WHERE s.isDel = 'False' AND s.SupplierID = ?";
        $params = array($SupplierID);
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

