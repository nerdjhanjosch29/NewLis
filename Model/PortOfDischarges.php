<?php
require_once '../Database/connection.php';
$data = json_decode(file_get_contents('php://input'));
  $UserID = "";
  $validateToken = include('../validate_token.php');
  $UserID = $decode->UserID;
  if(!$validateToken)
  {
    http_response_code(404);
    die();
  }
if(isset($data))
{
        $PortOfDischargeID = 0;
        $PortOfDischarge = "";
        if($data->PortOfDischargeID)
        {
        $PortOfDischargeID = $data->PortOfDischargeID;
        }
        if($data->PortOfDischarge)
        {
        $PortOfDischarge = $data->PortOfDischarge;
        }
            $sql = "EXEC [dbo].[PortOfDischarges]
                    @PortOfDischargeID = ?,
                    @PortOfDischarge = ?,
                    @UserID = ?";
            $params = array($PortOfDischargeID,$PortOfDischarge,$UserID);
            $stmt = sqlsrv_query($conn, $sql, $params);
            $result = 0;
            while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
            {
                echo $result = $row['result'];
            }

          
            if($result != 0 )
            {       
                $SystemLogID = 0;
                $FunctionID = 1; 
                $TableName = "PortOfDischarge";
                $Activity = "Insert PortOfDischarge : $PortOfDischarge";
                $UpdatedData = "0";
                $sql1 = "EXEC	[dbo].[SystemLogs]
                          @SystemLogID = ?,
                          @UserID = ?,
                          @FunctionID = ?,
                          @TableName = ?,
                          @Activity = ?,
                          @UpdatedData = ?";
                  $paramss = array($SystemLogID,$UserID,$FunctionID,$TableName,$Activity,$UpdatedData);       
                  $stmt = sqlsrv_query($conn, $sql1, $paramss);
            }
          
              sqlsrv_commit($conn);  
}
?>