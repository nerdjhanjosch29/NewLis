<?php

require '../connection.php';
$data = json_decode(file_get_contents('php://input'));
if ( sqlsrv_begin_transaction( $conn ) === false ) {
  die( print_r( sqlsrv_errors(), true ));
} 

   $UserID = "";
    // $UserLevel = "";
 
    $validateToken = include('../validate_token.php');
          // $UserLevel = $decode->usl; 
         $UserID = $decode->UserID;

    if(!$validateToken)
    {
      http_response_code(404);
      die();
    }

    if(isset($data))
    {
            $InspectionReportID = 0;
            $Status = 2;
            $Dr = "";

        if($data->InspectionReportID)
        {
        $InspectionReportID = $data->InspectionReportID;
        }
        $sql1 = "SELECT DRNumber FROM RawmatsInspectionReport WHERE InspectionReportID = ?";
        $params1 = array($InspectionReportID);
        $stmt1 = sqlsrv_query($conn, $sql1, $params1);
        while($row = sqlsrv_fetch_array($stmt1,SQLSRV_FETCH_ASSOC))
        {
            $Dr = $row['DRNumber'];
        }

        $sql = "UPDATE RawmatsInspectionReport SET Status = ?, WaiverUserID = ? WHERE InspectionReportID = ?";
        $params = array($Status,$UserID, $InspectionReportID);
        $stmt = sqlsrv_query($conn, $sql, $params);
            //   var_dump($stmt);
            if($stmt)
            {
            echo 2;
            sqlsrv_commit($conn); 
            $SystemLogID = 0;
            $FunctionID = 2;
            $TableName = "RawmatsInspectionReport";
            $Activity = "Updating Status of Inspection Report into Waived";
            $UpdatedData = $Dr;
                                $sql1 = "EXEC [dbo].[SystemLogEdit]
                                                        @SystemLogID = ?,
                                                        @UserID = ?,
                                                        @FunctionID = ?,
                                                        @TableName = ?,
                                                        @Activity = ?,  
                                                        @UpdatedData = ?";
                                $paramss = array($SystemLogID,$UserID,$FunctionID,$TableName,$Activity,$UpdatedData);    
                                    $stmt = sqlsrv_query($conn, $sql1,$paramss);
    }
            
    }

?>