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
                rcr.COAResultID
				,rcr.RawMaterialImportLocalID
				,rmp.RawMaterial
				,rcr.ParameterID
				,rp.Parameter
				,rcr.OperatorID
				,op.Operator
				,op.OperatorName
				,rcr.ShippingTransactionID
				,st.BL
				,st.MBL
				,rcr.Value
				,rcr.Permission
				,rcr.Result
				,rcr.created_at
				,rcr.UserID
             
                FROM RawMaterialCOAResult rcr
                LEFT JOIN RawMatsParameters rp ON rp.ParameterID = rcr.ParameterID
				LEFT JOIN Operator op ON op.OperatorID = rcr.OperatorID
				LEFT JOIN ShippingTransaction st ON st.ShippingTransactionID = rcr.ShippingTransactionID
				LEFT JOIN RawMaterialListImportLocal rmp ON rmp.RawMaterialImportLocalID = rcr.RawMaterialImportLocalID
                WHERE rcr.isDel = 'False' ";
  
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