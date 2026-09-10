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
   
        $RawMaterialImportLocalID = 0;
        $RawMaterial = "";
        $Local = 0;
        $Import = 0;
        if($data->RawMaterialImportLocalID)
        {
        $RawMaterialImportLocalID = $data->RawMaterialImportLocalID;
        }
        if($data->RawMaterial)
        {
        $RawMaterial = $data->RawMaterial;
        }
        if($data->Local)
        {
        $Local = $data->Local;
        }
        if($data->Import)
        {
        $Import = $data->Import;
        }
            $sql = "EXEC [dbo].[RawMaterialLists]
                    @RawMaterialImportLocalID = ?,
                    @RawMaterial = ?,
                    @Local = ?,
                    @Import = ?,
                    @UserID = ?";
            $params = array($RawMaterialImportLocalID,$RawMaterial,$Local,$Import,$UserID);
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
                $TableName = "RawMaterialImportLocal";
                $Activity = "Insert RawMaterialImportLocal : $RawMaterial";
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