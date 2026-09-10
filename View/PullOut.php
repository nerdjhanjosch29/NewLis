  <?php 
    require_once '../database/connection.php';
    if (sqlsrv_begin_transaction($conn) === false) {
      die( print_r( sqlsrv_errors(), true ));
    }

    $UserID = "";
    // $CompanyID = "";
    $validateToken = include('../validate_token.php');
    $UserID = $decode->UserID; 
    // $CompanyID = $decode->CompanyID;
    if(!$validateToken)
    {
      http_response_code(404);
      die();
    }

            $sql = "  SELECT 
                        po.PullOutID
                        ,po.ContainerNumber
                        ,st.MBL
                        ,st.HBL
                        ,po.DateOfDischarge
                        ,po.Storage
                        ,po.Demurrage
                        ,po.Detention
                        ,po.PullOutDate
                        ,po.DateIn
                        ,po.DateOut
                        ,po.ReturnDate
                        ,po.TruckingID
                        ,po.Remarks
                        FROM PullOut po 
                        LEFT JOIN ShippingTransaction st ON st.ShippingTransactionID = po.ShippingTransactionID
                        LEFT JOIN Trucking tk ON tk.TruckingID = po.TruckingID";
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


  
