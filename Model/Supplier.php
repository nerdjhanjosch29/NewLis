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
        $SupplierID = 0;
        $Source = "";
        $Supplier = "";
        $Address = "";
        $Currency = "";
        $Origin = "";
        $Terms = "";
        $IndentorID = 0;
        $ContactPerson = "";
        $ContactNumber = "";
        
        if($data->SupplierID)
        {
        $SupplierID = $data->SupplierID;
        }
        if($data->Source)
        {
        $Source = $data->Source;
        }
        if($data->Supplier)
        {
        $Supplier = $data->Supplier;
        }
        if($data->Address)
        {
        $Address = $data->Address;
        }
        if($data->Currency)
        {
        $Currency = $data->Currency;
        }
        if($data->Origin)
        {
        $Origin = $data->Origin;
        }

        if($data->Terms)
        {
        $Terms = $data->Terms;
        }
        if($data->ContactPerson)
        {
        $ContactPerson = $data->ContactPerson;
        }
        if($data->ContactNumber)
        {
        $ContactNumber = $data->ContactNumber;
        }
            $sql = "EXEC [dbo].[Suppliers]
                    @SupplierID = ?,
                    @Source = ?,
                    @Supplier = ?,
                    @Address =?,
                    @Origin =?,
                    @Currency = ?,
                    @IndentorID = ?,
                    @Terms = ?,
                    @ContactPerson= ?,
                    @ContactNumber = ?,
                    @UserID = ?";
            $params = array($SupplierID,$Source,$Supplier,$Address,$Origin,$Currency,$IndentorID,
            $Terms,$ContactPerson,$ContactNumber,$UserID);
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
                                          s.SupplierID
                                          ,s.Supplier
                                          ,s.Address
                                          ,s.ContactPerson
                                          ,s.ContactNumber
                                          ,s.Currency
                                          ,s.IndentorID
                                          ,i.Indentor
                                          ,s.Product
                                          ,s.Origin
                                          ,s.Source
                                          ,s.Terms
                                          ,s.UserID
                                          ,ua.Name
                                          FROM Supplier s
                                          LEFT JOIN UserAccount ua ON ua.UserID = s.UserID
                                          LEFT JOIN Indentor i ON i.IndentorID = s.IndentorID
                                          WHERE s.SupplierID = ?";
                                $params = array($SupplierID);
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
                                           s.SupplierID
                                          ,s.Supplier
                                          ,s.Address
                                          ,s.ContactPerson
                                          ,s.ContactNumber
                                          ,s.Currency
                                          ,s.IndentorID
                                          ,i.Indentor
                                          ,s.Product
                                          ,s.Origin
                                          ,s.Source
                                          ,s.Terms
                                          ,s.UserID
                                          ,ua.Name
                                          FROM Supplier s
                                          LEFT JOIN UserAccount ua ON ua.UserID = s.UserID
                                          LEFT JOIN Indentor i ON i.IndentorID = s.IndentorID
                                          WHERE s.isDel = 'False' 
                                          ORDER BY s.SupplierID DESC
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
                $TableName = "Supplier";
                $Activity = "Insert Supplier : $Supplier";
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