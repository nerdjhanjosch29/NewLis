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
   
        $WarehouseLocationID = 0;
        $WarehouseLocation = "";
        if($data->WarehouseLocationID)
        {
        $WarehouseLocationID = $data->WarehouseLocationID;
        }
        if($data->WarehouseLocation)
        {
        $WarehouseLocation = $data->WarehouseLocation;
        }

            $sql = "EXEC [dbo].[WarehouseLocations]
                    @WarehouseLocationID = ?,
                    @WarehouseLocation = ?,
                    @UserID = ?";
            $params = array($WarehouseLocationID,$WarehouseLocation,$UserID);
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

                                            wl.WarehouseLocationID,
                                            wl.WarehouseLocation,
                                            wl.Status,
                                            wl.UserID
                                            ,ua.Name
                                            FROM WarehouseLocation wl
                                            LEFT JOIN UserAccount ua ON ua.UserID = wl.UserID
                                                WHERE wl.isDel = 'False' AND wl.WarehouseLocationID = ?";
                                $params = array($WarehouseLocationID);
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
                                            wl.WarehouseLocationID,
                                            wl.WarehouseLocation,
                                            wl.Status,
                                            wl.UserID
                                            ,ua.Name
                                            FROM WarehouseLocation wl
                                            LEFT JOIN UserAccount ua ON ua.UserID = wl.UserID
                                                WHERE wl.isDel = 'False'
                                          ORDER BY wl.WarehouseLocationID DESC
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
                $TableName = "WarehouseLocation";
                $Activity = "Insert WarehouseLocation : $WarehouseLocation";
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