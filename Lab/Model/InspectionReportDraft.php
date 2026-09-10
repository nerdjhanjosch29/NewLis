<?php 
require '../connection.php';
$data = json_decode(file_get_contents('php://input'));
if ( sqlsrv_begin_transaction( $conn ) === false ) {
  die( print_r( sqlsrv_errors(), true ));
} 

if(isset($data))
{
        $InspectionReportID = 0;
        $PO = 0;
        $BL = 0;
        $DeliveryTypeID = 0;
        $EffectiveDate = "";
        $VersionNo = 0;
        $InspectionDate = "";
        $SampleCode = 0;
        $CategoryID = 0;
        $SupplierID = 0;
        $DRNumber = "";
        $PlateNo = "";
        $ContainerNumber = "";
        $TimeOfSampling = "";
        $FinalResultID = 0;
        $DateTimeReleased = "";
        $Remarks = "";
        $Status = 0; //Save As Draft
        $UserID = "";
        $year = date('Y');
            if($data->InspectionReportID)
            {
                $InspectionReportID = $data->InspectionReportID;
            }
            if($data->PO)
            {
                $PO = $data->PO;
            }
            //  if($data->BL)
            // {
            //     $BL = $data->BL;
            // }
            // if($data->DeliveryTypeID)
            // {
            //     $DeliveryTypeID = $data->DeliveryTypeID;
            // }
            // if($data->EffectiveDate)
            // {
            //     $EffectiveDate = $data->EffectiveDate;
            // }
            if($data->Status)
            {
                $Status = $data->Status;
            }
            if($data->InspectionDate)
            {
                $InspectionDate = $data->InspectionDate;
                $InspectionDate = new DateTime($InspectionDate);
            }
                $sql = "SELECT TOP 1 ISNULL(SampleCode, 0) AS SampleCode FROM RawMatsInspectionReport WHERE SampleCode LIKE ? ORDER BY SampleCode DESC";
                $params = array($year. "%");
                $stmt = sqlsrv_query($conn,$sql,$params);
                while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
                {
                $SampleCode = $row['SampleCode'];
                }      
                if($SampleCode == 0)
                {
                $SampleCode = $year."0001"; 
                }
                else
                {
                $SampleCode = $SampleCode + 1;    
                }
            if($data->CategoryID)
            {
                $CategoryID = $data->CategoryID;
            }
            if($data->SupplierID)
            {
                $SupplierID = $data->SupplierID;
            }
            if($data->DRNumber)
            {
                $DRNumber = $data->DRNumber;
            }
            if($data->PlateNo)
            {
                $PlateNo = $data->PlateNo;
            }
            if($data->ContainerNumber)
            {
                $ContainerNumber = $data->ContainerNumber;
            }
            if($data->TimeOfSampling)
            {
                $TimeOfSampling = $data->TimeOfSampling;
                $TimeOfSampling = new DateTime($TimeOfSampling);
            }
            if($data->FinalResultID)
            {
                $FinalResultID = $data->FinalResultID;
            }
            if($data->DateTimeReleased)
            {
                $DateTimeReleased = $data->DateTimeReleased;
                 $DateTimeReleased = new DateTime($DateTimeReleased);
            }
            if($data->Remarks)
            {
                $Remarks = $data->Remarks;
            }
            if($data->Status)
            {
                $Status = $data->Status;
            }
            if($data->UserID)
            {
                $UserID = $data->UserID;
            }       
            if($InspectionReportID == 0)
            {
                $InspectionReportID = "";
                $sql = "INSERT INTO RawMatsInspectionReport(PO,BL,DeliveryTypeID,EffectiveDate,VersionNo,InspectionDate,
                         SampleCode,CategoryID,SupplierID,DRNumber,
                         PlateNo,ContainerNumber,TimeOfSampling,DateTimeReleased,Remarks,Status,UserID)
                         VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
                         SELECT SCOPE_IDENTITY()";
                $params = array($PO,$BL,$DeliveryTypeID,$EffectiveDate,$VersionNo,$InspectionDate,$SampleCode,$CategoryID,$SupplierID,$DRNumber
                                ,$PlateNo,$ContainerNumber,$TimeOfSampling,$DateTimeReleased,$Remarks,$Status,$UserID);
                $stmt = sqlsrv_query($conn,$sql,$params); 
                sqlsrv_next_result($stmt); 
                sqlsrv_fetch($stmt); 
                $InspectionReportID = sqlsrv_get_field($stmt, 0);   
                if($stmt)
                {
                    sqlsrv_commit($conn);
                    echo 1;                  
                }           
            }
            else
            {
                if($data->PO)
                {
                    $PO = $data->PO;
           
                }
                // if($data->BL)
                // {
                //     $BL = $data->BL;
               
                // }
                if($data->DeliveryTypeID)
                {
                    $DeliveryTypeID = $data->DeliveryTypeID;
                }
                if($data->EffectiveDate)
                {
                    $EffectiveDate = $data->EffectiveDate;
                     $EffectiveDate = new DateTime($EffectiveDate);
                }
                if($data->VersionNo)
                {
                    $VersionNo = $data->VersionNo;
                }
                if($data->InspectionDate)
                {
                    $InspectionDate = $data->InspectionDate;
                     $InspectionDate = new DateTime($InspectionDate);
                    
                }
                if($data->CategoryID)
                {
                    $CategoryID = $data->CategoryID;
                }
                if($data->SupplierID)
                {
                    $SupplierID = $data->SupplierID;
                }
                if($data->DRNumber)
                {
                    $DRNumber = $data->DRNumber;
                }
                if($data->PlateNo)
                {
                    $PlateNo = $data->PlateNo;
                }
                if($data->ContainerNumber)
                {
                    $ContainerNumber = $data->ContainerNumber;
                }
                if($data->TimeOfSampling)
                {
                    $TimeOfSampling = $data->TimeOfSampling;
                    $TimeOfSampling = new DateTime($TimeOfSampling);
              
                }
                if($data->FinalResultID)
                {
                    $FinalResultID = $data->FinalResultID;
                }
                if($data->DateTimeReleased)
                {
                    
                    $DateTimeReleased = $data->DateTimeReleased;
                     $DateTimeReleased = new DateTime($DateTimeReleased);
                }
                if($data->Remarks)
                {
                    $Remarks = $data->Remarks;
                }
                if($data->Status)
                {
                    $Status = $data->Status;
                }
                if($data->UserID)
                {
                    $UserID = $data->UserID;
                }
            $sql = "UPDATE RawMatsInspectionReport SET PO= ?, BL = ?, DeliveryTypeID = ?,EffectiveDate = ?,VersionNo = ?,InspectionDate = ?,
                        CategoryID = ?,SupplierID = ?,DRNumber = ?,
                        PlateNo = ?,ContainerNumber = ?,TimeOfSampling = ?,DateTimeReleased = ?,Remarks = ?, 
                        Status = ?,UserID = ?
                        WHERE InspectionReportID = ?";
            $params = array($PO, $BL,$DeliveryTypeID,$EffectiveDate,$VersionNo,$InspectionDate,$CategoryID,$SupplierID,$DRNumber
                             ,$PlateNo,$ContainerNumber,$TimeOfSampling,$DateTimeReleased,$Remarks,$Status,$UserID,$InspectionReportID);
            $stmt = sqlsrv_query($conn,$sql,$params);
        
            if($stmt)
            {
                sqlsrv_commit($conn);
                echo 2;
            }
                //Set deleted into 1
                $sql1 = "UPDATE RawMatsParameterResult SET deleted = 1 WHERE InspectionID = ?";
                $params1 =  array($InspectionReportID);
                $stmt1 = sqlsrv_query($conn,$sql1,$params1);
               
            }
            $arrays = $data->Parameters;
            $length = count($arrays);
            for($i=0; $i<=$length-1; $i++)
                {
                  $ParameterResultID = 0;
                  $ParameterID = 0;
                  $Parameter = 0;
                  $Result = 0;
                  $StandardValue = 0;
                  $Permission = 0;
                  $deleted = 0;
                  $Value = 0;
                     if($arrays[$i]->ParameterResultID)
                        {
                        $ParameterResultID = $arrays[$i]->ParameterResultID;
                        }

                     if($arrays[$i]->Result)
                        {
                         $Result= $arrays[$i]->Result;
                        }
                     if($arrays[$i]->StandardValue)
                        {
                         $StandardValue= $arrays[$i]->StandardValue;
                        }
                    if($arrays[$i]->Value)
                        {
                        $Value = $arrays[$i]->Value;
                        }
                    if($arrays[$i]->Permission)
                        {
                         $Permission= $arrays[$i]->Permission;
                        }
    
                     if($arrays[$i]->ParameterID)
                        {
                         $ParameterID = $arrays[$i]->ParameterID;   
                        }
                     if($ParameterResultID == 0)
                        {     
                            $sql = "INSERT INTO RawMatsParameterResult(ParameterID,InspectionID,Value,StandardValue,Result,Permission,deleted)
                                    VALUES(?,?,?,?,?,?,?)";
                            $params = array($ParameterID,$InspectionReportID,$Value,$StandardValue,$Result,$Permission,$deleted);
                            $stmt1 = sqlsrv_query($conn,$sql,$params); 


                            //  $sql = "SELECT COUNT(ParameterID) AS CountParameters  FROM RawMaterialStandardDetail rmsd
                            //         LEFT JOIN RawMaterialStandard rms ON rms.RawMaterialStandardID =rmsd.RawMaterialStandardID
                            //         WHERE rms.CategoryID = ?  ";
                            // $params = array($CategoryID);
                            // $stmt1 = sqlsrv_query($conn,$sql,$params); 
                            // $CountParameters = "";
                            // while($StandardRows = sqlsrv_fetch_array($stmt1,SQLSRV_FETCH_ASSOC))
                            // {
                            //     $CountParameters = $StandardRows['CountParameters'];
                            // }
                            // if($CountParameters != 1)
                            // {

                            //     if($data->Value)
                            //     {
                            //         $Value = $data->Value;
                            //     }
                            //     if($data->RawMaterialStandardID)
                            //     {
                            //         $RawMaterialStandardID = $data->RawMaterialStandardID;
                            //     }
                            // $sql = "INSERT INTO RawMaterialStandardDetail(RawMaterialStandardID,ParameterID,Value,UserID)
                            //         VALUES(?,?,?,?)";
                            // $params = array($RawMaterialStandardID,$ParameterID,$Value,$UserID);
                            // $stmt1 = sqlsrv_query($conn,$sql,$params); 
                            // }


                        }
                     else
                        {
                            $sql = "UPDATE RawMatsParameterResult SET ParameterID = ?,InspectionID = ?, Value = ?,StandardValue = ?, Result = ?,
                            Permission = ?, deleted = ? WHERE ParameterResultID = ?";
                            $params = array($ParameterID,$InspectionReportID,$Value,$StandardValue, $Result,$Permission,
                            $deleted,$ParameterResultID);
                            $stmt1 = sqlsrv_query($conn,$sql,$params); 
                        }
                     sqlsrv_commit($conn);  
                } 
                //------------SystemLogss-------------------------
                            if($InspectionReportID == 0)
                        {
                            $SystemLogID = 0;
                            $FunctionID = 1;
                            $TableName = "InspectionReport (Draft)";
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
                            $TableName = "InspectionReport (Draft)";
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
