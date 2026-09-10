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
    $ContractPerformaID = 0;
if(isset($_GET['ContractPerformaID']))
{
$ContractPerformaID = $_GET['ContractPerformaID'];
}
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
            ,cp.CreatedAt
            ,cp.Status
            FROM ContractPerforma cp 
            LEFT JOIN PackedIn pki ON cp.PackedInID = pki.PackedInID
            LEFT JOIN RawMaterialImportLocal rl ON cp.RawMaterialImportLocalID = rl.RawMaterialImportLocalID
            LEFT JOIN Supplier s ON cp.SupplierID = s.SupplierID
            LEFT JOIN PortOfDischarge pod ON cp.PortOfDischargeID = pod.PortOfDischargeID
            WHERE cp.isDel = 'False' AND cp.ContractPerformaID = ?
            -- AND cp.Status = 0
            ";
            $params = array($ContractPerformaID);

    $stmt1 = sqlsrv_query($conn,$sql,$params); 

    if($stmt1 === false)
    {
        sqlsrv_rollback($conn);
        die(print_r(sqlsrv_errors(), true));
    }

    $json = array();

    while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {

        $sqlItem = "SELECT
                        cii.ContractInventoryItemID,
                        cii.ContractPerformaID,
						cp.ContractNo,
                        cii.RawMaterialID AS CategoryID,
						rm.RawMaterial AS CategoryName
                    FROM ContractInventoryItem cii
					LEFT JOIN ContractPerforma cp ON cp.ContractPerformaID = cii.ContractPerformaID
					LEFT JOIN RawMaterial rm ON cii.RawMaterialID = rm.RawMaterialID
					
                    WHERE cii.ContractPerformaID = ? AND cii.deleted = 0
                    AND cii.isDel = 0";

        $paramsItem = array($ContractPerformaID);

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

        $json = $row;
    }


    sqlsrv_commit($conn);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($json);

?>