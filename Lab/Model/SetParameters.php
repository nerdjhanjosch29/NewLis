<?php 
require '../connection.php';
$data = json_decode(file_get_contents('php://input'));
if ( sqlsrv_begin_transaction( $conn ) === false ) {
  die( print_r( sqlsrv_errors(), true ));
} 
   //    $UserID = "";
   //  $validateToken = include('../validate_token.php');
   //      $UserID = $decode->UserID;
   //  if(!$validateToken)
   //  {
   //    http_response_code(404);
   //    die();
   //  }
if(isset($data))
{

$CategoryID = 0;
$RawMaterialStandardID = 0;
   $UserID = "";

     if($data->RawMaterialStandardID )
     {
        $RawMaterialStandardID = $data->RawMaterialStandardID;
     }
     
     if($data->CategoryID )
     {
        $CategoryID = $data->CategoryID;
     }
     if($RawMaterialStandardID == 0)
     {
      $CountCategoryID = 0;

      $sql = "SELECT COUNT(rms.CategoryID) AS CategoryID
       FROM RawMaterialStandard rms WHERE rms.CategoryID = ? ";
      $params = array($CategoryID);
   
      $stmt1 = sqlsrv_query($conn,$sql,$params);
      while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
        
         $CountCategoryID = $row['CategoryID'];
          }
      // var_dump($RawMaterialCount);
      if($CountCategoryID > 0 )
      {
         echo 0;
      }
      else
      {
         $RawMaterialStandardIDs = "";
        $sql = "INSERT INTO RawMaterialStandard(CategoryID)
                                    VALUES(?) SELECT SCOPE_IDENTITY()";
                            $params = array($CategoryID);
                            $stmt1 = sqlsrv_query($conn,$sql,$params); 
                            sqlsrv_next_result($stmt1); 
                            sqlsrv_fetch($stmt1); 
                            $RawMaterialStandardIDs = sqlsrv_get_field($stmt1, 0);    

                            sqlsrv_commit($conn); 
                           echo 1;
          $sql = "UPDATE RawMaterialStandardDetail SET isDel = 'True' WHERE RawMaterialStandardID = ?";
          $params = array($RawMaterialStandardIDs);
          $stmt = sqlsrv_query($conn,$sql,$params);
          sqlsrv_commit($conn); 
                                 $arrays = $data->RawMaterialStandardDetails;
                                 $length = count($arrays);
                                 
                                 for($i=0; $i<=$length-1; $i++)
                                    {
                                       
                                       $ParameterID = 0;
                                       $Value = 0;
                                       $StandardValue = 0;
                                       $isDel = 0;
                                       $RawMaterialStandardDetailID = 0;
                                             if($arrays[$i]->RawMaterialStandardDetailID)
                                             {
                                                $RawMaterialStandardDetailID = $arrays[$i]->RawMaterialStandardDetailID;
                                             }
                                             // if($arrays[$i]->RawMaterialStandardID)
                                             // {
                                             //   $RawMaterialStandardID = $arrays[$i]->RawMaterialStandardID;
                                             // }
                                             if($arrays[$i]->ParameterID)
                                             {
                                             $ParameterID = $arrays[$i]->ParameterID;
                                             }
                                             if($arrays[$i]->Value)
                                             {
                                             $Value= $arrays[$i]->Value;
                                             }
                                            if($arrays[$i]->StandardValue)
                                             {
                                              $StandardValue= $arrays[$i]->StandardValue;
                                             }
                                          if($RawMaterialStandardDetailID == 0)
                                             {     
                                                $sql = "INSERT INTO RawMaterialStandardDetail(RawMaterialStandardID,ParameterID,Value,StandardValue,UserID,isDel)
                                                         VALUES(?,?,?,?,?,?)";
                                                $params = array($RawMaterialStandardIDs,$ParameterID,$Value,$StandardValue,$UserID,$isDel);
                                                $stmt1 = sqlsrv_query($conn,$sql,$params); 
                                          sqlsrv_commit($conn); 
                                       

                                             }
                                          else
                                             {
                                       
                                             $sql = "UPDATE RawMaterialStandardDetail SET RawMaterialStandardID = ?, ParameterID = ?,Value = ?, StandardValue = ?, 
                                             UserID = ?,isDel = ?
                                             WHERE RawMaterialStandardDetailID = ?";
                                             $params = array($RawMaterialStandardIDs,$ParameterID,$Value,$StandardValue,$UserID,$isDel,$RawMaterialStandardDetailID);
                                             $stmt2 = sqlsrv_query($conn,$sql,$params); 
                                             }
                                          sqlsrv_commit($conn);  
                                    }
          }



          }
          else
          {

                    $sql = "UPDATE RawMaterialStandardDetail SET isDel = 'True' WHERE RawMaterialStandardID = ?";
                     $params = array($RawMaterialStandardID);
                     $stmt = sqlsrv_query($conn,$sql,$params);
                     sqlsrv_commit($conn); 

                     $sql = "UPDATE RawMaterialStandard SET CategoryID = ? WHERE RawMaterialStandardID = ?";
                     $params = array($CategoryID,$RawMaterialStandardID);
                     $stmt = sqlsrv_query($conn,$sql,$params);
                     echo 2;

                   $arrays = $data->RawMaterialStandardDetails;
                                 $length = count($arrays);
                                 
                                 for($i=0; $i<=$length-1; $i++)
                                    {
                                       
                                       $ParameterID = 0;
                                       $Value = 0;
                                       // $UserID = "";
                                       $StandardValue = 0;
                                       $isDel = 0;
                                       $RawMaterialStandardDetailID = 0;
                                             if($arrays[$i]->RawMaterialStandardDetailID)
                                             {
                                                $RawMaterialStandardDetailID = $arrays[$i]->RawMaterialStandardDetailID;
                                             }
                                             // if($arrays[$i]->RawMaterialStandardID)
                                             // {
                                             //   $RawMaterialStandardID = $arrays[$i]->RawMaterialStandardID;
                                             // }
                                             if($arrays[$i]->ParameterID)
                                             {
                                             $ParameterID = $arrays[$i]->ParameterID;
                                             }

                                             if($arrays[$i]->Value)
                                             {
                                             $Value= $arrays[$i]->Value;
                                             }
                                            if($arrays[$i]->StandardValue)
                                             {
                                              $StandardValue= $arrays[$i]->StandardValue;
                                             }
                                          if($RawMaterialStandardDetailID == 0)
                                             {     
                                                $sql = "INSERT INTO RawMaterialStandardDetail(RawMaterialStandardID,ParameterID,Value,StandardValue,UserID,isDel)
                                                         VALUES(?,?,?,?,?,?)";
                                                $params = array($RawMaterialStandardID,$ParameterID,$Value,$StandardValue,$UserID,$isDel);
                                                $stmt1 = sqlsrv_query($conn,$sql,$params); 
                                          sqlsrv_commit($conn); 
                                       

                                             }
                                          else
                                             {

                                             $sql = "UPDATE RawMaterialStandardDetail SET RawMaterialStandardID = ?, ParameterID = ?,Value = ?,
                                             StandardValue = ?,
                                             UserID = ?,isDel = ?
                                             WHERE RawMaterialStandardDetailID = ?";
                                             $params = array($RawMaterialStandardID,$ParameterID,$Value,$StandardValue,$UserID,$isDel,$RawMaterialStandardDetailID);
                                             $stmt2 = sqlsrv_query($conn,$sql,$params); 
                                             }
                                          sqlsrv_commit($conn);  
                                    }
          }
     }
    

         
 
                


            
                // //------------SystemLogss-------------------------
                //             if($COAResultID == 0)
                //         {
                //             $SystemLogID = 0;
                //             $FunctionID = 1;
                //             $TableName = "COAResult";
                //             $sql = "EXEC [dbo].[SystemLogInsert]
                //                     @SystemLogID = ?,
                //                     @UserID = ?,
                //                     @FunctionID = ?,
                //                     @TableName = ?";
                //             $params = array($SystemLogID,$UserID,$FunctionID,$TableName);
                //             $stmt = sqlsrv_query($conn, $sql, $params);
                //         }
                //         else
                //         {
                //             $SystemLogID = 0;
                //             $FunctionID = 2;
                //             $TableName = "COAResult";
                //             $sql = "EXEC [dbo].[SystemLogEdit]
                //                     @SystemLogID = ?,
                //                     @UserID = ?,
                //                     @FunctionID = ?,
                //                     @TableName = ?";
                //         $params = array($SystemLogID,$UserID, $FunctionID,$TableName);
                //         $stmt = sqlsrv_query($conn, $sql, $params);
                //         }
        
?>
