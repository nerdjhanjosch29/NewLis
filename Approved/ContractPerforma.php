<?php 

    require_once 'connection.php';
    if (sqlsrv_begin_transaction($conn) === false) {
      die( print_r( sqlsrv_errors(), true ));
    }
    $UserID = "";
    // $CompanyID = "";
    $validateToken = include('../validate_token.php');
    $UserID = $decode->UserID; 
    // $CompanyID = $decode->CompanyID;
    if(!$validateToken)
    {
      http_response_code(404);
      die();
    }

    //Status = 1 is For Active
         $sql = "SELECT 
                 cp.ContractPerformaID
                ,cp.ContractNo
                ,cp.Quantity
                ,cp.EstimatedContainer
                ,cp.Packaging
                ,cp.PackedInID
                ,pki.PackedIn
                ,cp.RawMaterialImportLocalID
                ,rl.RawMaterial
                ,cp.SupplierID
                ,s.Supplier
                ,cp.SupplierAddress
                ,cp.PortOfDischargeID
                ,pod.PortOfDischarge
                ,cp.FromShipmentPeriod
                ,cp.ToShipmentPeriod
                ,cp.CountryOfOrigin
                ,cp.UnitPrice
                ,cp.created_at
                ,cp.Status
                FROM ContractPerforma cp 
                LEFT JOIN PackedIn pki ON cp.PackedInID = pki.PackedInID
                LEFT JOIN RawMaterialList rl ON cp.RawMaterialImportLocalID = rl.RawMaterialImportLocalID
                LEFT JOIN Supplier s ON cp.SupplierID = s.SupplierID
                LEFT JOIN PortOfDischarge pod ON cp.PortOfDischargeID = pod.PortOfDischargeID
                WHERE cp.isDel = 'False' AND cp.Status = 1";
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