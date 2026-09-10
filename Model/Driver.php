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
   
        $DriverID = 0;
        $DriverName = "";
        if($data->DriverID)
        {
        $DriverID = $data->DriverID;
        }
        if($data->DriverName)
        {
        $DriverName = $data->DriverName;
        }
        if($data->ContactNumber)
        {
        $ContactNumber = $data->ContactNumber;
        }
            $sql = "EXEC [dbo].[Drivers]
                    @DriverID = ?,
                    @DriverName = ?,
                    @ContactNumber = ?,
                    @UserID = ?";
            $params = array($DriverID,$DriverName,$ContactNumber,$UserID);
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
                $TableName = "Driver";
                $Activity = "Insert Driver : $DriverName";
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