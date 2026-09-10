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
       $RawMaterialImportLocalID = 0;
    
    if($data->RawMaterialImportLocalID)
            {
                $RawMaterialImportLocalID = $data->RawMaterialImportLocalID;
            }

    $array = $data->COADetail;
    $length = count($array);
    for($i = 0; $i < $length; $i++)
    {
    $COAResultID = 0; 
    $BL = 0;
    $ParameterID = 0;
    $Value = 0;
    $Result = 0;
    $Permission = 0;
    $deleted = 0;

    
            if($array[$i]->COAResultID)
            {
                $COAResultID = $array[$i]->COAResultID;
            }
            if($array[$i]->BL)
            {
                $BL = $array[$i]->BL;
            }

            if($array[$i]->ParameterID)
            {
                $ParameterID = $array[$i]->ParameterID;
            }
            if($array[$i]->Value)
            {
                $Value = $array[$i]->Value;
            }
            if($array[$i]->Result)
            {
                $Result = $array[$i]->Result;
            }
            if($array[$i]->Permission)
            {
                $Permission = $array[$i]->Permission;
            }
        
            if($COAResultID == 0)
            { 
                $sql = "INSERT INTO RawMaterialCOAResult(BL,RawMaterialImportLocalID
                ,ParameterID,Value,Result,Permission,
                deleted,UserID)VALUES(?,?,?,?,?,?,?,?)";
                $params = array($BL,$RawMaterialImportLocalID
                ,$ParameterID,$Value,$$Result,$Permission,
                $deleted,$UserID);
                $stmt = sqlsrv_query($conn,$sql,$params); 
                var_dump($stmt);
           if($stmt)
           {
                 echo 1;
           }
                  sqlsrv_commit($conn);
            }
            else
            {
                $sql = "UPDATE RawMaterialCOAResult SET BL = ?,RawMaterialImportLocalID = ?
                ,ParameterID =?,Value =?,Result = ?,Permission = ?,
                deleted = ?,UserID = ? WHERE COAResultID = ?";
                $params = array($BL,$RawMaterialImportLocalID
                ,$ParameterID,$Value,$$Result,$Permission,
                $deleted,$UserID, $COAResultID);
                $stmt = sqlsrv_query($conn,$sql,$params); 
                echo 2;
                  sqlsrv_commit($conn);
            }
    }



   
                //------------SystemLogss-------------------------
                            if($COAResultID == 0)
                        {
                            $SystemLogID = 0;
                            $FunctionID = 1;
                            $TableName = "COAResult";
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
                            $TableName = "COAResult";
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
