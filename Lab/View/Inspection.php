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
    $StandardID = "";
    $RawMaterialID = "";
    // if(isset($_GET['SID']))
    // {
    //     $StandardID = $_GET['SID'];
    // }
    if(isset($_GET['RawMatsID']))
    {
        $CategoryID = $_GET['RawMatsID'];
    }
    //This is the Detail for Adding Parameters Standards
                $sql = "SELECT 
                 rms.RawMaterialStandardDetailID
			    ,rms.RawMaterialStandardID
                ,rms.ParameterID
                ,rms.Operator
                ,rms.OperatorName
                ,rp.Parameter
                ,rp.boolean
                ,rms.CategoryName
				,rms.CategoryID
                ,rms.Value
				,rms.StandardValue
                FROM View_RawMatsStandardReference rms
                LEFT JOIN RawMatsParameters rp ON rp.ParameterID = rms.ParameterID
                WHERE rms.CategoryID = ? AND rms.isDel = 'False' ";
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