<?php 

    require_once '../connection.php';
    if (sqlsrv_begin_transaction($conn) === false) {
      die( print_r( sqlsrv_errors(), true ));
    }
    $StandardID = "";
    $RawMaterialID = "";

        $sql = "SELECT 
             rmr.InspectionReportID
            ,rmr.DRNumber
            ,rmr.PO
            ,po.PONo

            ,rmr.PlateNo
            ,rmr.CategoryID
            ,rmc.CategoryName
            ,rmr.Status
            ,rmr.SupplierID
            ,s.Supplier
            FROM RawMatsInspectionReport rmr
            LEFT JOIN Supplier s ON s.SupplierID = rmr.SupplierID
			LEFT JOIN RawMaterialCategory rmc ON rmc.CategoryID = rmr.CategoryID
            LEFT JOIN PurchaseOrder po ON po.PurchaseOrderID = rmr.PO
			WHERE rmr.Status = 0 OR rmr.Status = 2";
         
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