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
   
        $CheckerTypeID = 0;
        $CheckerType = "";
        if($data->CheckerTypeID)
        {
        $CheckerTypeID = $data->CheckerTypeID;
        }
        if($data->CheckerType)
        {
        $CheckerType = $data->CheckerType;
        }

            $sql = "EXEC [dbo].[CheckerTypes]
                    @CheckerTypeID = ?,
                    @CheckerType = ?,
                    @UserID = ?";

            $params = array($CheckerTypeID,$CheckerType,$UserID);
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
                                      ct.CheckerTypeID
                                      ,ct.CheckerType
                                      
                                      ,ct.CreatedAt
                                      ,ct.UserID
                                      ,ua.Name
                                      FROM CheckerType ct
                                      LEFT JOIN UserAccount ua ON ua.UserID = ct.UserID
                                      WHERE ct.isDel = 'False' AND ct.CheckerTypeID = ?";
                                $params = array($CheckerTypeID);
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
                                         ct.CheckerTypeID
                                      ,ct.CheckerType
                                      
                                      ,ct.CreatedAt
                                      ,ct.UserID
                                      ,ua.Name
                                      FROM CheckerType ct
                                      LEFT JOIN UserAccount ua ON ua.UserID = ct.UserID
                                      WHERE ct.isDel = 'False'
                              ORDER BY ct.CheckerTypeID DESC
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
                $TableName = "Checker Type";
                $Activity = "Insert Checker Type : $CheckerType";
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