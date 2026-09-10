<?php 

    require_once '../Database/connection.php';

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

    //Status = 0 is for Pending
    //Status = 1 is for Active
    //Status = 2 is for Completed

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
            ,cp.PortOfDischargeID
            ,pod.PortOfDischarge
            ,cp.FromShipmentPeriod
            ,cp.ToShipmentPeriod
            ,cp.CountryOfOrigin
            ,cp.CreatedAt
            ,cp.Status
            FROM ContractPerforma cp 
            LEFT JOIN PackedIn pki ON cp.PackedInID = pki.PackedInID
            LEFT JOIN RawMaterialImportLocal rl ON cp.RawMaterialImportLocalID = rl.RawMaterialImportLocalID
            LEFT JOIN Supplier s ON cp.SupplierID = s.SupplierID
            LEFT JOIN PortOfDischarge pod ON cp.PortOfDischargeID = pod.PortOfDischargeID
            WHERE cp.isDel = 'False' 
            AND cp.Status = 0";

    $stmt1 = sqlsrv_query($conn,$sql); 

    if($stmt1 === false)
    {
        sqlsrv_rollback($conn);
        die(print_r(sqlsrv_errors(), true));
    }

    $json = array();

    while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {

        $sqlItem = "SELECT
                        ContractInventoryItemID,
                        ContractPerformaID,
                        RawMaterialID
                    FROM ContractInventoryItem
                    WHERE ContractPerformaID = ?
                    AND isDel = 0";

        $paramsItem = array($row['ContractPerformaID']);

        $stmtItem = sqlsrv_query($conn, $sqlItem, $paramsItem);

        if($stmtItem === false)
        {
            sqlsrv_rollback($conn);
            die(print_r(sqlsrv_errors(), true));
        }

        $ContractInventoryItem = array();

        while($item = sqlsrv_fetch_array($stmtItem, SQLSRV_FETCH_ASSOC))
        {
            $ContractInventoryItem[] = $item;
        }

        $row['ContractInventoryItem'] = $ContractInventoryItem;

        $json[] = $row;
    }


    sqlsrv_commit($conn);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($json);

?>