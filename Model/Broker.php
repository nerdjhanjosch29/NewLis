<?php
require_once '../Database/connection.php';
$data = json_decode(file_get_contents('php://input'));
    if (sqlsrv_begin_transaction($conn) === false) {
      die( print_r( sqlsrv_errors(), true ));
    }
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
        $BrokerID = 0;
        $Broker = "";
        $ContactPerson = "";
        $ContactNumber = "";
        if($data->BrokerID)
        {
        $BrokerID = $data->BrokerID;
        }
        if($data->Broker)
        {
        $Broker = $data->Broker;
        }
        if($data->ContactPerson)
        {
        $ContactPerson = $data->ContactPerson;
        }
        if($data->ContactNumber)
        {
        $ContactNumber = $data->ContactNumber;
        }
            $sql = "EXEC [dbo].[Brokers]
                    @BrokerID = ?,
                    @Broker = ?,
                    @ContactPerson = ?,
                    @ContactNumber = ?,
                    @UserID = ?";
            $params = array($BrokerID,$Broker,$ContactPerson,$ContactNumber,$UserID);
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
                                          b.BrokerID
                                          ,b.Broker
                                          ,b.ContactNumber
                                          ,b.ContactPerson
                                          ,b.UserID
                                          ,ua.Name
                                          FROM Broker b 
                                  LEFT JOIN UserAccount ua ON ua.UserID = b.UserID
                                          WHERE b.isDel = 'False' AND b.BrokerID = ?";
                                $params = array($BrokerID);
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
                                           b.BrokerID
                                          ,b.Broker
                                          ,b.ContactNumber
                                          ,b.ContactPerson
                                          ,b.UserID
                                          ,ua.Name
                                          FROM Broker b 
                                  LEFT JOIN UserAccount ua ON ua.UserID = b.UserID
                                  WHERE b.isDel = 'False'
                              ORDER BY b.BrokerID DESC
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
                $TableName = "Broker";
                $Activity = "Insert Broker : $Broker";
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
            else
            {

        sqlsrv_rollback($conn);
         echo "Rollback";
            }
          

              sqlsrv_commit($conn);   


            
     }
}
catch(Exception $e)
{
  sqlsrv_rollback($conn);

  header('Content-Type: application/json; charset=utf-8');
echo 0;
}


?>