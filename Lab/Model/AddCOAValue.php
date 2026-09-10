<?php 
require '../connection.php';
$data = json_decode(file_get_contents('php://input'));
if ( sqlsrv_begin_transaction( $conn ) === false ) {
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
if(isset($data))

{        
       $ShippingTransactionID = 0;
       $COAStandardID = 0;
   
       if($data->COAStandardID)
            {
                $COAStandardID = $data->COAStandardID;
            }
    
    if($data->ShippingTransactionID)
            {
                $ShippingTransactionID = $data->ShippingTransactionID;
            }
    if($COAStandardID == 0)
    {

        $sql = "INSERT INTO RawMaterialCOAStandard (ShippingTransactionID) VALUES (?) SELECT SCOPE_IDENTITY()";
        $params = array($ShippingTransactionID);
        $stmt = sqlsrv_query($conn,$sql,$params);
        sqlsrv_next_result($stmt); 
        sqlsrv_fetch($stmt); 
        $COAStandardID = sqlsrv_get_field($stmt, 0);   
        echo 1;
    }
    else
    {
        $sql = "UPDATE RawMaterialCOAStandard SET COAStandardID = ? ";
        $params = array($COAStandardID);
        $stmt = sqlsrv_query($conn,$sql,$params);
        echo 2;

        $sql = "UPDATE RawMaterialCOAStandardDetail SET deleted = 1 WHERE COAStandardID = ? ";
        $params = array($COAStandardID);
        $stmt13 = sqlsrv_query($conn,$sql,$params);
        sqlsrv_commit($conn);

        $sql = "UPDATE ShippingTransaction SET COAStatus = 1 WHERE ShippingTransactionID = ?";
        $params = array($ShippingTransactionID);
        $stmt13 = sqlsrv_query($conn,$sql,$params);
        sqlsrv_commit($conn); 
    }
            $array = $data->COADetail;
            $length = count($array);
            for($i = 0; $i < $length; $i++)
            {
                    $COAStandardDetailID = 0; 
                    $ParameterID = 0;
                    $Value = 0;
                    $deleted = 0;

                    if($array[$i]->COAStandardDetailID)
                    {
                        $COAStandardDetailID = $array[$i]->COAStandardDetailID;
                    }
                    if($array[$i]->ParameterID)
                    {
                        $ParameterID = $array[$i]->ParameterID;
                    }
                    if($array[$i]->Value)
                    {
                        $Value = $array[$i]->Value;
                    }
                    if($COAStandardDetailID == 0)
                    { 
                        $sql = "INSERT INTO RawMaterialCOAStandardDetail(COAStandardID
                        ,ParameterID,Value,
                        deleted)VALUES(?,?,?,?)";
                        $params = array($COAStandardID
                        ,$ParameterID,$Value,
                        $deleted);
                        $stmt1 = sqlsrv_query($conn,$sql,$params); 
                        sqlsrv_commit($conn);
                    }
                    else
                    {
                        $sql = "UPDATE RawMaterialCOAStandardDetail SET COAStandardID = ?
                        ,ParameterID = ?, Value = ?, deleted = ? WHERE COAStandardDetailID = ?";
                        $params = array($COAStandardID
                        ,$ParameterID,$Value,
                        $deleted, $COAStandardDetailID);
                        $stmt12 = sqlsrv_query($conn,$sql,$params); 
                        sqlsrv_commit($conn);
                    }
            }     
            // CHECK IF THERE ARE STILL ACTIVE COA DETAILS 
                $sql = "SELECT COUNT(*) 
                        FROM RawMaterialCOAStandardDetail 
                        WHERE COAStandardID = ? AND deleted = 0";

                $params = array($COAStandardID);
                $stmtCheck = sqlsrv_query($conn, $sql, $params);

                sqlsrv_fetch($stmtCheck);
                $DetailCount = sqlsrv_get_field($stmtCheck, 0);

                //UPDATE SHIPPING TRANSACTION COA STATUS 
                if($DetailCount > 0)
                {
                    $sql = "UPDATE ShippingTransaction 
                            SET COAStatus = 1 
                            WHERE ShippingTransactionID = ?";
                }
                else
                {
                    $sql = "UPDATE ShippingTransaction 
                            SET COAStatus = 0 
                            WHERE ShippingTransactionID = ?";
                }

                $params = array($ShippingTransactionID);
                $stmtStatus = sqlsrv_query($conn, $sql, $params);
                sqlsrv_commit($conn);       
                
                            //------------SystemLogss-------------------------
                                        if($COAStandardID == 0)
                                    {
                                        $SystemLogID = 0;
                                        $FunctionID = 1;
                                        $TableName = "Add COA Standard";
                                        $sql = "EXEC [dbo].[SystemLogInsert]
                                                @SystemLogID = ?,
                                                @UserID = ?,
                                                @FunctionID = ?,
                                                @TableName = ?";
                                        $params = array($SystemLogID,$UserID,$FunctionID,$TableName);
                                        $stmt = sqlsrv_query($conn, $sql, $params);
                                    }
                                    else
                                    {
                                        $SystemLogID = 0;
                                        $FunctionID = 2;
                                        $TableName = "Add COA Standard";
                                        $sql = "EXEC [dbo].[SystemLogEdit]
                                                @SystemLogID = ?,
                                                @UserID = ?,
                                                @FunctionID = ?,
                                                @TableName = ?";
                                    $params = array($SystemLogID,$UserID, $FunctionID,$TableName);
                                    $stmt = sqlsrv_query($conn, $sql, $params);
                                    }
                    }
?>
