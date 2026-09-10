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
        $ContainerTypeID = 0;
        $Container = "";
        if($data->ContainerTypeID)
        {
        $ContainerTypeID = $data->ContainerTypeID;
        }
        if($data->Container)
        {
        $Container = $data->Container;
        }
            $sql = "EXEC [dbo].[ContainerTypes]
                    @ContainerTypeID = ?,
                    @Container = ?,
                    @UserID = ?";
            $params = array($ContainerTypeID,$Container,$UserID);
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
                                ct.ContainerTypeID
                                ,ct.Container
                                ,ct.Status
                                ,ct.UserID
                                ,ct.CreatedAt
                                ,ua.Name


                                FROM ContainerType ct 
                                LEFT JOIN UserAccount ua ON ua.UserID = ct.UserID
                                          WHERE ct.isDel = 'False' AND ct.ContainerTypeID = ?";
                                $params = array($ContainerTypeID);
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
                        $sql = "SELECT TOP(1)
                                          ct.ContainerTypeID
                                          ,ct.Container
                                          ,ct.Status
                                          ,ct.UserID
                                          ,ct.CreatedAt
                                          ,ua.Name
                                          FROM ContainerType ct 
									                        LEFT JOIN UserAccount ua ON ua.UserID = ct.UserID
                                            WHERE ct.isDel = 'False'

                                        ORDER BY ct.ContainerTypeID DESC
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
                $TableName = "Container Type";
                $Activity = "Insert Container Type : $Container";
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