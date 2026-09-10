<?php 

require_once '../Database/connection.php';
if (sqlsrv_begin_transaction($conn) === false) {
    die(print_r(sqlsrv_errors(), true));
}

$validateToken = include('../validate_token.php');
if(!$validateToken)
{
  http_response_code(404);
  die();
}

$mbl = "";
if (isset($_GET['mbl'])) {
    $mbl = $_GET['mbl'];
}

$sql = "SELECT
        po.PullOutID
       ,po.HBL
       ,po.MBL
       ,st.SupplierID
       ,s.Supplier
       ,st.BrokerID
       ,b.Broker
       ,po.ContainerNumber
       ,po.DateOfDischarge
       ,po.Storage
       ,po.Demurrage
       ,po.DateIn
       ,po.DateOut
       ,po.PullOutDate
       ,po.Detention
       ,po.ReturnDate
       ,po.TruckingID
       ,t.TruckingName
       ,po.Remarks
       ,po.deleted
        FROM PullOut po
        LEFT JOIN ShippingTransaction st ON po.MBL = st.MBL
        LEFT JOIN ContractPerforma cp ON cp.ContractPerformaID = st.ContractPerformaID
        LEFT JOIN Supplier s ON st.SupplierID = s.SupplierID
        LEFT JOIN Broker b ON st.BrokerID = b.BrokerID
        LEFT JOIN Trucking t ON po.TruckingID = t.TruckingID
        WHERE po.MBL = ?
          AND (st.Status = 3 OR st.Status = 4)
          AND po.deleted = 0
          AND st.Packaging = 1
          AND cp.isDel = 'False'
          AND st.isDel = 'False'";

$params = array($mbl);

$stmt1 = sqlsrv_query(
    $conn,
    $sql,
    $params
    
);

if ($stmt1) {
    $json = array();

    while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
        $json[] = $row;
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($json);
} else {
    sqlsrv_rollback($conn);
    echo "Rollback";
}

?>
