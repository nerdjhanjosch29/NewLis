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
        $WarehouseID = 0;
        $Warehouse_Name = "";
        $WarehouseLocationID = 0;
        $MaximumCapacity = 0;
        $MinimumCapacity = 0;
        $TotalQuantity = 0;
        $TotalWeight = 0;
        $Remarks = "";
        if($data->WarehouseID)
        {
        $WarehouseID = $data->WarehouseID;
        }
        if($data->Warehouse_Name)
        {
        $Warehouse_Name = $data->Warehouse_Name;
        }
        if($data->WarehouseLocationID)
        {
        $WarehouseLocationID = $data->WarehouseLocationID;
        }
        if($data->MaximumCapacity)
        {
        $MaximumCapacity = $data->MaximumCapacity;
        }
        if($data->MinimumCapacity)
        {
        $MinimumCapacity = $data->MinimumCapacity;
        }
        if($data->TotalQuantity)
        {
        $TotalQuantity = $data->TotalQuantity;
        }
        if($data->TotalWeight)
        {
        $TotalWeight = $data->TotalWeight;
        }
        if($data->Remarks)
        {
        $Remarks = $data->Remarks;
        }
            $sql = "EXEC [dbo].[Warehouses]
                    @WarehouseID = ?,
                    @WarehouseLocationID = ?,
                    @Warehouse_Name = ?,
                    @MaximumCapacity = ?,
                    @MinimumCapacity = ?,
                    @TotalQuantity = ?,
                    @TotalWeight = ?,                                       
                    @Remarks = ?,
                    @UserID = ?";
            $params = array($WarehouseID,$WarehouseLocationID,$Warehouse_Name,$MaximumCapacity,$MinimumCapacity,$TotalQuantity,$TotalWeight,$Remarks,$UserID);
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
                                          w.WarehouseID,
                                          w.WarehouseLocationID,
                                          wl.WarehouseLocation,
                                          w.Warehouse_Name,
                                          w.MaximumCapacity,
                                          w.MinimumCapacity,
                                          w.TotalQuantity,
                                          w.TotalWeight,
                                          w.Remarks,
                                          w.CreatedAt,
                                          ua.Name,
                                          w.UserID
                                          FROM Warehouse w
                                          LEFT JOIN WarehouseLocation wl ON wl.WarehouseLocationID = w.WarehouseLocationID
                                          LEFT JOIN UserAccount ua ON ua.UserID = w.UserID
                                                WHERE w.isDel = 'False' AND w.WarehouseID = ?";
                                $params = array($WarehouseID);
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
                                          w.WarehouseID,
                                          w.WarehouseLocationID,
                                          wl.WarehouseLocation,
                                          w.Warehouse_Name,
                                          w.MaximumCapacity,
                                          w.MinimumCapacity,
                                          w.TotalQuantity,
                                          w.TotalWeight,
                                          w.Remarks,
                                          w.CreatedAt,
                                          ua.Name,
                                          w.UserID
                                          FROM Warehouse w
                                          LEFT JOIN WarehouseLocation wl ON wl.WarehouseLocationID = w.WarehouseLocationID
                                          LEFT JOIN UserAccount ua ON ua.UserID = w.UserID
                                                WHERE w.isDel = 'False' 
                                          ORDER BY w.WarehouseID DESC";
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
                $TableName = "Warehouse";
                $Activity = "Insert Warehouse : $Warehouse_Name";
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