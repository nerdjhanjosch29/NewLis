<?php 
    require_once '../connection.php';
    if (sqlsrv_begin_transaction($conn) === false) {
      die( print_r( sqlsrv_errors(), true ));
    }
    // $validateToken = include('validate_token.php');
    // if(!$validateToken)
    // {
    //   http_response_code(404);
    //   die();
    // }
        $sql = "SELECT 
             rmr.InspectionReportID
            ,rmr.ContainerNumber
            ,rmr.DRNumber
            ,rmr.PO
			,po.PONo
            ,rmr.DateTimeReleased
            ,rmr.DeliveryTypeID
            ,rmr.EffectiveDate
            ,rmr.InspectionDate
            ,rmr.InspectionReportID
            ,rmr.PlateNo
            ,rmr.CategoryID
            ,rmc.CategoryName
            ,rmr.Remarks
            ,rmr.SampleCode
            ,rmr.Status
            ,rmr.SupplierID
            ,s.Supplier 
            ,rmr.TimeOfSampling
            ,rmr.VersionNo
            FROM RawMatsInspectionReport rmr
            LEFT JOIN Supplier s ON s.SupplierID = rmr.SupplierID
			LEFT JOIN RawMaterialCategory rmc ON rmc.CategoryID = rmr.CategoryID
            LEFT JOIN PurchaseOrder po ON po.PurchaseOrderID = rmr.PO ORDER BY  rmr.DateTimeReleased DESC";     
        $stmt1 = sqlsrv_query($conn,$sql);
        $json = array();
        $CategoryID = "";
        if ($stmt1) {
            while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) 
            {
                $Result = $row;
                $InspectionReportID = $row['InspectionReportID'];
                 $CategoryID = $row['CategoryID'];
                // Fetch Binloading details for the current Binload
                $sql = "SELECT 
                        rpr.ParameterResultID
                        ,rpr.ParameterID
                        ,rmp.Parameter
                        ,rpr.InspectionID
						
                        ,CASE
                                            WHEN rmp.boolean = 1 AND rpr.Value = 1 THEN 1
                                            WHEN rmp.boolean = 1 AND rpr.Value = 0 THEN 0
                                            ELSE CAST(CONVERT(decimal(10,2), rpr.Value) AS int)
                                        END AS Value
                        ,CASE
                                            WHEN rmp.boolean = 1 AND rpr.Result = 1 THEN 1
                                            WHEN rmp.boolean = 1 AND rpr.Result = 0 THEN 0
                                            ELSE CAST(CONVERT(decimal(10,2), rpr.Result) AS int)
                                        END AS Result
                        ,CASE
                                            WHEN rmp.boolean = 1 AND rpr.StandardValue = 1 THEN 1
                                            WHEN rmp.boolean = 1 AND rpr.StandardValue = 0 THEN 0
                                            ELSE CAST(CONVERT(decimal(10,2), rpr.StandardValue) AS int)
                                        END AS StandardValue
						
                        ,rpr.Permission
                        ,rmp.OperatorID, 
                        op.OperatorName,
                        op.Operator,
                        rmp.boolean
                        FROM RawMatsParameterResult  rpr
                        LEFT  JOIN dbo.RawMatsParameters AS rmp ON rmp.ParameterID = rpr.ParameterID
                        LEFT  JOIN dbo.Operator AS op ON op.OperatorID = rmp.OperatorID

                        WHERE rpr.InspectionID = ? AND deleted = 0";        
                        $params = array($InspectionReportID);
                        $stmt = sqlsrv_query($conn, $sql, $params); 
               
                $ResultArrayRow = array();
                if ($stmt) {
                    while ($ResultRow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $ResultArrayRow[] = $ResultRow;
                    } 
                }
                // Add Binloading to the Binload object
                $Result['Result'] = $ResultArrayRow;
                // Add the combined Binload object to the json array
                $json[] = $Result;
            }
        }
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($json);
?>