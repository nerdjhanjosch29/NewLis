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
        $TruckID = 0;
        $TruckingID = 0;
        $PlateNo = "";
        $Description = "";
        $Status = 0;
        if($data->TruckID)
        {
        $TruckID = $data->TruckID;
        }
        if($data->TruckingID)
        {
        $TruckingID = $data->TruckingID;
        }
        if($data->Description)
        {
        $Description = $data->Description;
        }
        if($data->PlateNo)
        {
        $PlateNo = $data->PlateNo;
        }
            $sql = "EXEC [dbo].[Trucks]
                    @TruckID = ?,
                    @TruckingID = ?,
                    @PlateNo = ?,
                    @Description = ?,
                    @Status = ?,
                    @UserID = ?";
            $params = array($TruckingID,$PlateNo,$Description,$PlateNo,$Status,$UserID);
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
                $TableName = "Truck";
                $Activity = "Insert Truck : $PlateNo";
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