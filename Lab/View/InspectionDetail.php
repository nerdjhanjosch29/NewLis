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
                rpr.ParameterResultID
                ,rpr.ParameterID
				,rmp.Parameter
                ,rpr.InspectionID
                ,rpr.Value,
                rpr.Result,
                rpr.Permission
                FROM RawMatsParameterResult  rpr
                LEFT  JOIN dbo.RawMatsParameters AS rmp ON rmp.ParameterID = rpr.ParameterID
                LEFT  JOIN dbo.Operator AS op ON op.OperatorID = rmp.OperatorID";
				// -- WHERE rp.InspectionType = 1
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