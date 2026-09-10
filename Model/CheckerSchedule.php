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

try
{
if(isset($data))
{
        $ScheduleRotationID = 0;


        $ScheduleRotationID = 0;
        $CheckerUserID = "";
        $WarehouseLocationID = 0;
        $TypeID = 0;
        $PlantID = 0;
        $DateRotation = "";
        $AdminUserID = "";
        if($data->ScheduleRotationID)
        {
        $ScheduleRotationID = $data->ScheduleRotationID;
        }
        if($data->CheckerUserID)
        {
        $CheckerUserID = $data->CheckerUserID;
        }
        if($data->WarehouseLocationID)
        {
        $WarehouseLocationID = $data->WarehouseLocationID;
        }
        if($data->TypeID) 
        {
        $TypeID = $data->TypeID;
        }
        if($data->PlantID)
        {
        $PlantID = $data->PlantID;
        }
        if($data->DateRotation)
        {
        $DateRotation = $data->DateRotation;
        }

            $sql = "EXEC [dbo].[CheckerSchedules]
                    @ScheduleRotationID = ?,
                    @UserID = ?,
                    @WarehouseLocationID = ?,
                    @TypeID = ?,
                    @PlantID = ?,
                    @DateRotation = ?,
                    @AdminUserID = ?
                    ";
            $params = array($ScheduleRotationID,$CheckerUserID,$WarehouseLocationID,$TypeID,$PlantID,$DateRotation,$UserID);
            $stmt = sqlsrv_query($conn, $sql, $params);
            $result = 0;
            while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
            {
               
                                $result = $row['result'];
         

                     if($result == 0)
                        {
                        $json = array(
                            "result" => $result,
                                "message" => "Already exist"
                            );
                                header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }
                        else if($result == 2)
                        {
                                $sql = " SELECT 
                                          cs.ScheduleRotationID
                                          ,cs.TypeID
                                          ,cs.PlantID
                                          ,p.PlantID
                                          ,cs.DateRotation
                                          ,cs.WarehouseLocationID
                                          ,wl.WarehouseLocation
                                          ,cs.UserID
                                          ,ua.Name

                                          FROM CheckerSchedule cs
                                          LEFT JOIN UserAccount ua ON ua.UserID = cs.UserID
                                          LEFT JOIN Plant p ON p.PlantID = cs.PlantID
                                          LEFT JOIN WarehouseLocation wl ON wl.WarehouseLocationID = cs.WarehouseLocationID
                                                WHERE cs.isDel = 'False' AND cs.ScheduleRotationID = ?";
                                $params = array($ScheduleRotationID);
                                $stmt1 = sqlsrv_query($conn,$sql,$params); 
                                if($stmt1)
                                {
                                do {
                                    while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                                 
                                    $json = array(
                                        "message" => "Successfully updated.",
                                        "result" =>$result,
                                            "data" => $row
                                        );
                                    }
                                } while (sqlsrv_next_result($stmt1));
                                header('Content-Type: application/json; charset=utf-8');
                                    echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                }
                                else
                                {
                                sqlsrv_rollback($conn);
                                echo "Rollback";
                                }
                        }
                        else
                        {
                        $sql = " SELECT TOP(1)
                                     
                                          cs.ScheduleRotationID
                                          ,cs.TypeID
                                          ,cs.PlantID
                                          ,p.PlantID
                                          ,cs.DateRotation
                                          ,cs.WarehouseLocationID
                                          ,wl.WarehouseLocation
                                          ,cs.UserID
                                          ,ua.Name

                                          FROM CheckerSchedule cs
                                          LEFT JOIN UserAccount ua ON ua.UserID = cs.UserID
                                          LEFT JOIN Plant p ON p.PlantID = cs.PlantID
                                          LEFT JOIN WarehouseLocation wl ON wl.WarehouseLocationID = cs.WarehouseLocationID
                                          WHERE cs.isDel = 'False' 
                                          ORDER BY cs.ScheduleRotationID DESC
                      ";
                    $stmt1 = sqlsrv_query($conn,$sql); 
           
                    if($stmt1)
                    {
                    do {
                        while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
 
                        $json = array(
                            "message" => "Successfully added.",
                            "result" =>$result,
                                "data" => $row
                             
                            );
                        }
                    } while (sqlsrv_next_result($stmt1));

                    header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    }
                    else
                    {
                    sqlsrv_rollback($conn);
                    echo "Rollback";
                    }

                        }




              
            }
            
            if($result != 0 )
            {       
                $SystemLogID = 0;
                $FunctionID = 1; 
                $TableName = "Checker Schedule";
                $Activity = "Insert Checker Schedule : $CheckerUserID";
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
}
catch(Exception $e)
{
    echo 0;
}
?>