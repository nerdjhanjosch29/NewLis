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
        $sql = " SELECT 

			rms.RawMaterialStandardID
            ,rms.CategoryID
			,rmc.CategoryName
				
            FROM RawMaterialStandard rms
			LEFT JOIN RawMaterialCategory rmc ON rms.CategoryID = rmc.CategoryID";
        $stmt1 = sqlsrv_query($conn,$sql); 
        $json = array();
     
     if ($stmt1) {
              while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                  $CategoryID = $row['CategoryID'];
                  // Fetch PullOut details for the current ShippingTransaction
                  $sql = "SELECT 
				rms.RawMaterialStandardDetailID
                ,rms.ParameterID
                ,rp.Parameter
                ,rp.boolean
                ,rms.CategoryID
                ,rms.Value
                
                ,rms.StandardValue
				,rms.isDel
				,rms.Operator
				,rms.OperatorID
				,rms.OperatorName
                FROM View_RawMatsStandardReference rms
                LEFT JOIN RawMatsParameters rp ON rp.ParameterID = rms.ParameterID
				LEFT JOIN RawMaterialStandard rs ON rs.RawMaterialStandardID = rms.RawMaterialStandardID
                WHERE rms.CategoryID = ?  AND rms.isDel = 0";
                  $params= array($CategoryID);
                  $stmt2 = sqlsrv_query($conn, $sql, $params);
                  $RawMaterialDetail = array();
                  if ($stmt2) {
                      while ($StandardRow = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
                          $RawMaterialDetail[] = $StandardRow;
                      }
                  }
                  $row['StandardDetail'] = $RawMaterialDetail;
                  $json[] = $row;
              }
          }
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode($json);

?>