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

        $WarehousePartitionID = 0;
        $WarehousePartitionName = "";
        $WarehouseID = 0;
        $MaximumCapacity = 0;
        $TotalQuantity = 0;
        $TotalWeight = 0;
        if($data->WarehousePartitionID)
        {
        $WarehousePartitionID = $data->WarehousePartitionID;
        }
        if($data->WarehousePartitionName)
        {
        $WarehousePartitionName = $data->WarehousePartitionName;
        }
        if($data->WarehouseID)
        {
        $WarehouseID = $data->WarehouseID;
        }
        if($data->MaximumCapacity)
        {
        $MaximumCapacity = $data->MaximumCapacity;
        }
        if($data->TotalQuantity)
        {
        $TotalQuantity = $data->TotalQuantity;
        }
        if($data->TotalWeight)
        {
        $TotalWeight = $data->TotalWeight;
        }
            $sql = "EXEC [dbo].[WarehousePartitions]
                    @WarehousePartitionID = ?,
                    @WarehouseID = ?,
                    @WarehousePartitionName = ?,
                    @MaximumCapacity = ?,
                    @TotalQuantity = ?,
                    @TotalWeight = ?,                                       
                    @UserID = ?";
            $params = array($WarehousePartitionID,$WarehouseID,$WarehousePartitionName,$MaximumCapacity,$TotalQuantity,$TotalWeight,$UserID);
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
                                $sql = "  SELECT
                                            wp.WarehousePartitionID,
                                            wp.WarehousePartitionName,
                                            w.Warehouse_Name,
											wl.WarehouseLocation,
											wp.WarehouseID,

                                            wp.Status,
                                            wp.UserID,
                                            ua.Name
                                            FROM WarehousePartition wp
                                            LEFT JOIN UserAccount ua ON CAST(ua.UserID AS nvarchar(36)) = wp.UserID
											LEFT JOIN Warehouse w ON w.WarehouseID = wp.WarehouseID
											LEFT JOIN WarehouseLocation wl ON w.WarehouseLocationID = wl.WarehouseLocationID
                                                WHERE wp.isDel = 'False' AND wp.WarehousePartitionID = ?";
                                $params = array($WarehousePartitionID);
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
                                            wp.WarehousePartitionID,
                                            wp.WarehousePartitionName,
                                            w.Warehouse_Name,
                                            wl.WarehouseLocation,
                                            wp.WarehouseID,
                                            wp.Status,
                                            wp.UserID,
                                            ua.Name
                                            FROM WarehousePartition wp
                                            LEFT JOIN UserAccount ua ON CAST(ua.UserID AS nvarchar(36)) = wp.UserID
											LEFT JOIN Warehouse w ON w.WarehouseID = wp.WarehouseID
											LEFT JOIN WarehouseLocation wl ON w.WarehouseLocationID = wl.WarehouseLocationID
                                                WHERE wp.isDel = 'False'
                                          ORDER BY wp.WarehousePartitionID DESC";
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
                $TableName = "WarehousePartition";
                $Activity = "Insert WarehousePartition : $WarehousePartitionName";
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