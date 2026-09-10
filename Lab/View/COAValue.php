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
	rcs.COAStandardID
  ,st.ShippingTransactionID
  ,st.RawMaterialImportLocalID
  ,rml.RawMaterial
  ,st.SupplierID
  ,s.Supplier
  ,st.MBL
  ,st.BL
  FROM RawMaterialCOAStandard rcs
  LEFT JOIN ShippingTransaction st ON st.ShippingTransactionID = rcs.ShippingTransactionID
  LEFT JOIN RawMaterialList rml ON rml.RawMaterialImportLocalID = st.ShippingTransactionID
  LEFT JOIN Supplier s ON s.SupplierID = st.SupplierID
 ";     
        $stmt1 = sqlsrv_query($conn,$sql);
        $json = array();
        $COAStandardID = "";
        if ($stmt1) {
            while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) 
            {
                $COA = $row;
              
                 $COAStandardID = $row['COAStandardID'];
                // Fetch Binloading details for the current Binload
            $sql = "SELECT 
                rcp.COAStandardDetailID
				,rcp.COAStandardID
				,rcp.ParameterID
				,rp.Parameter
				,st.BL
				,st.MBL
				,rcp.Value
				,rcp.created_at          
                FROM RawMaterialCOAStandardDetail rcp
                LEFT JOIN RawMatsParameters rp ON rp.ParameterID = rcp.ParameterID
				LEFT JOIN RawMaterialCOAStandard rcs ON rcs.COAStandardID = rcp.COAStandardID
				LEFT JOIN ShippingTransaction st ON st.ShippingTransactionID = rcs.ShippingTransactionID
                WHERE rcp.isDel = 'False' AND rcp.COAStandardID = ? AND deleted = 0 ";        
                        $params = array($COAStandardID);
                        $stmt = sqlsrv_query($conn, $sql, $params); 
               
                $COAArrayRow = array();
                if ($stmt) {
                    while ($COARow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $COAArrayRow[] = $COARow;
                    } 
                }
                // Add Binloading to the Binload object
                $COA['COADetails'] = $COAArrayRow;
                // Add the combined Binload object to the json array
                $json[] = $COA;
            }
        }
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($json);
?>