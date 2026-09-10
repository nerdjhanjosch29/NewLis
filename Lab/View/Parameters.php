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
   
    $RawMaterialID = "";
    // if(isset($_GET['SID']))
    // {
    //     $StandardID = $_GET['SID'];
    // }
    if(isset($_GET['RawMatsID']))
    {
        $CategoryID = $_GET['RawMatsID'];
    }
        $sql = "SELECT
                rms.RawMaterialStandardDetailID
              ,rms.RawMaterialStandardID
              ,rrs.CategoryID
              ,rms.ParameterID
                ,rmp.Parameter
                ,rmp.boolean
                ,rmc.CategoryName
                ,rms.Value
                FROM RawMaterialStandardDetail rms
				  LEFT JOIN RawMaterialStandard rrs ON rrs.RawMaterialStandardID = rms.RawMaterialStandardID
                LEFT JOIN RawMatsParameters rmp ON rmp.ParameterID = rms.ParameterID
                LEFT JOIN RawMaterialCategory rmc ON rmc.CategoryID = rrs.CategoryID
                WHERE rrs.CategoryID = ? AND rms.Value <> 0";
         $params = [ $CategoryID
               ];
        $stmt1 = sqlsrv_query($conn,$sql,$params); 
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