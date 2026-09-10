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
        $RawMaterialID = $_GET['RawMatsID'];
    }
        $sql = "SELECT        dbo.RawMatsStandard.StandardID, dbo.RawMatsStandard.ParameterID, dbo.RawMatsStandard.RawMaterialID, dbo.RawMatsStandard.Value,
         dbo.RawMaterial.RawMaterial, 
                         dbo.RawMatsParameters.Parameter, dbo.RawMatsParameters.boolean
FROM            dbo.RawMatsStandard LEFT OUTER JOIN
                         dbo.RawMatsParameters ON dbo.RawMatsStandard.ParameterID = dbo.RawMatsParameters.ParameterID LEFT OUTER JOIN
                         dbo.RawMaterial ON dbo.RawMatsStandard.RawMaterialID = dbo.RawMaterial.RawMaterialID";
                        // --   LEFT OUTER JOIN
                        // --  dbo.Operator AS op ON dbo.RawMatsStandard.OperatorID = op.OperatorID";
         $params = [
                    $RawMaterialID
      
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