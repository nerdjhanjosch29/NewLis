<?php

require_once '../Database/connection.php';
$data = json_decode(file_get_contents('php://input'));

if(isset($data))
{
    
    if($data->TransferTypeID)
    {
        $TransferTypeID = $data->$TransferTypeID;
    }

 if($TransferTypeID == 1)
  {
            $TransferCode = 0;
            $RawMaterialTransferID = 0;
            $DateTransfer = "";
            // $DateTimeTransfer = "";
            $BL = 0;
            $PO = 0;
            $DispatcherID = 0;
            $DispatcherRequestID = 0;
            $WarehousePartitionStockID = 0;
            $FromWarehouseLocationID = 0;
            $FromWarehouseID = 0;
            $FromWarehousePartitionID = 0;
            $ToWarehouseLocationID = 0;
            $ToWarehouseID = 0;
            $ToWarehousePartitionID = 0;
            $FeedmixDeparture = "";  
            $SourceArrival = "";
            $SourceDeparture = "";
            $FeedmixArrival = "";
            $GuardID = 0;
            $RawMaterialID = 0;
            $Status = 0;
            $ArrivalWeight = 0;
            $WeigherOut = 0;
            $WeigherIn = 0;
            $DriverID = 0;
            $TruckID = 0;
            $Quantity = 0;
            $Weight = 0;
            $LossQuantity = 0;
            $OverQuantity = 0;
            $LossWeight = 0;
            $OverWeight = 0;
            $CheckerID = 0;
            $UserID = 0;

            $FeedmixDeparture = $data->FeedmixDeparture;
            if ($FeedmixDeparture === null) {
            $FeedmixDeparture = null;
            } 
            else {
            $FeedmixDeparture=date_create($data->FeedmixDeparture);
            $FeedmixDeparture= date_format($FeedmixDeparture,"Y-m-d H:i");
            }
            $SourceArrival = $data->SourceArrival;
            if ($SourceArrival === null) {
            $SourceArrival = null;
            } 
            else {
            $SourceArrival=date_create($data->SourceArrival);
            $SourceArrival= date_format($SourceArrival,"Y-m-d H:i");
            }
            $SourceDeparture = $data->SourceDeparture;
            if ($SourceDeparture === null) {
            $SourceDeparture = null;
            } 
            else {
            $SourceDeparture=date_create($data->SourceDeparture);
            $SourceDeparture= date_format($SourceDeparture,"Y-m-d H:i");
            }
            $FeedmixArrival = $data->FeedmixArrival; // Assuming this is a DateTime object or null
            if ($FeedmixArrival === null) {
                $FeedmixArrival = null;
            } else {
            $FeedmixArrival=date_create($data->FeedmixArrival);
            $FeedmixArrival= date_format($FeedmixArrival,"Y-m-d H:i");
            }
            //Save PO or BL for Traceability
            if($data->PO)
            {
            $PO=$data->PO;
            }
            if($data->BL)
            {
            $BL=$data->BL;
            }
            if($data->RawMaterialTransferID)
            {
            $RawMaterialTransferID =$data->RawMaterialTransferID;
            }

                // $sql = "SELECT TransferCode FROM RawMaterialTransfer WHERE RawMaterialTransferID = ?";
                // $params = array($RawMaterialTransferID);
                // $stmt = sqlsrv_query($conn, $sql, $params);
                // $OldData = "";
                // while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
                // {
                // sqlsrv_commit($conn);
                //   $OldData = $row['TransferCode'];
                // }
            if($data->DateTransfer)
            {
            $DateTransfer =$data->DateTransfer;
            $DateTransfer = date_create($DateTransfer);
            $DateTransfer = date_format($DateTransfer,"Y/m/d");
            }
            if($data->DispatcherID)
            {
            $DispatcherID = $data->DispatcherID;
            }
            if($data->DispatcherRequestID)
            {
            $DispatcherRequestID = $data->DispatcherRequestID;
            }
            if($data->FromWarehouseLocationID)
            {
            $FromWarehouseLocationID = $data->FromWarehouseLocationID;
            }
            if($data->FromWarehouseID)
            {
            $FromWarehouseID=$data->FromWarehouseID;
            }
            if($data->FromWarehousePartitionID)
            {
            $FromWarehousePartitionID =$data->FromWarehousePartitionID;
            }
            if($data->ToWarehouseLocationID)
            {
            $ToWarehouseLocationID =$data->ToWarehouseLocationID;
            }
            if($data->ToWarehouseID)
            {
            $ToWarehouseID =$data->ToWarehouseID;
            }
            if($data->ToWarehousePartitionID)
            {
            $ToWarehousePartitionID =$data->ToWarehousePartitionID;
            }
            if($data->WarehousePartitionStockID)
            {
            $WarehousePartitionStockID = $data->WarehousePartitionStockID;
            }
            if($data->GuardID)
            {
            $GuardID =$data->GuardID;
            }
            if($data->RawMaterialID)
            {
            $RawMaterialID =$data->RawMaterialID;
            }
            if($data->Status)
            {
            $Status=$data->Status;
            }
            if($data->ArrivalWeight)
            {
            $ArrivalWeight=$data->ArrivalWeight;
            }
            if($data->WeigherOut)
            {
            $WeigherOut=$data->WeigherOut;
            }
            if($data->WeigherIn)
            {
            $WeigherIn=$data->WeigherIn;
            }
            if($data->DriverID)
            {
            $DriverID=$data->DriverID;
            }
            if($data->TruckID)
            {
            $TruckID =$data->TruckID;
            }
            if($data->Quantity)
            {
            $Quantity=$data->Quantity;
            }
            if($data->Weight)
            {
            $Weight=$data->Weight;
            }
            if($data->LossQuantity)
            {
            $LossQuantity=$data->LossQuantity;
            }
            if($data->OverQuantity)
            {
            $OverQuantity=$data->OverQuantity;
            }
            if($data->LossWeight)
            {
            $LossWeight =$data->LossWeight;
            }
            if($data->OverWeight)
            {
            $OverWeight=$data->OverWeight;
            }
            if($data->CheckerID)
            {
            $CheckerID=$data->CheckerID;
            }
            if($data->UserID)
            {
            $UserID=$data->UserID;
            }
            $sql = "SELECT TOP 1 ISNULL(TransferCode, 0) AS TransferCode FROM RawMaterialTransfer WHERE TransferCode LIKE ? ORDER BY TransferCode DESC";
            $params = array($year. "%");
            $stmt1 = sqlsrv_query($conn,$sql,$params);

            while($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC))
            {
            $TransferCode = $row['TransferCode'];
            }      
            if($TransferCode == 0)
            {
            $TransferCode = $year."0001"; 
            }
            else
            {
            $TransferCode = $TransferCode + 1;    
            }
                $sql="EXEC [dbo].[RawMaterialTransfers]
                    @RawMaterialTransferID = ?,
                    @TransferCode = ?,
                    @TransferTypeID = ?,
                    @PO = ?,
                    @BL = ?,
                    @DateTransfer = ?,
                    @DispatcherID = ?,
                    @DispatcherRequestID = ?,
                    @WarehousePartitionStockID = ?,
                    @FromWarehouseLocationID = ?,
                    @FromWarehouseID = ?,
                    @FromWarehousePartitionID = ?,
                    @ToWarehouseLocationID = ?,
                    @ToWarehouseID = ?,
                    @ToWarehousePartitionID = ?,
                    @FeedmixDeparture = ?,
                    @SourceArrival = ?,
                    @SourceDeparture = ?,
                    @FeedmixArrival = ?,
                    @GuardID = ?,
                    @RawMaterialID = ?,
                    @Status = ?,
                    @ArrivalWeight = ?,
                    @WeigherOut = ?,
                    @WeigherIn = ?,
                    @DriverID = ?,
                    @TruckID = ?,
                    @Quantity = ?,
                    @Weight = ?,
                    @LossQuantity = ?,
                    @OverQuantity = ?,
                    @LossWeight = ?,
                    @OverWeight = ?,
                    @CheckerID = ?,
                    @UserID = ?";
      $params = array($RawMaterialTransferID,$TransferCode,$TransferTypeID,$PO,$BL,$DateTransfer,$DispatcherID,$DispatcherRequestID,$WarehousePartitionStockID,
      $FromWarehouseLocationID,$FromWarehouseID,$FromWarehousePartitionID,
      $ToWarehouseLocationID,$ToWarehouseID,$ToWarehousePartitionID,$FeedmixDeparture,$SourceArrival,$SourceDeparture,
      $FeedmixArrival,$GuardID,$RawMaterialID,$Status,$ArrivalWeight,$WeigherOut,$WeigherIn,$DriverID,$TruckID,$Quantity,$Weight,
      $LossQuantity, $OverQuantity,$LossWeight,$OverWeight,
      $CheckerID,$UserID);
      $stmt = sqlsrv_query($conn,$sql,$params);
      // var_dump($Status);
      //  var_dump($stmt);
      $result = "";
      while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
      {
      sqlsrv_commit($conn);
      echo $result = $row['result'];
      }  

                    if($Status == 3)
                    {
                        // OUT - Source Warehouse // 0 for OUT
                        $sqlInv = "EXEC WarehouseInventoriesTransfer
                                    @WarehouseID = ?,
                                    @RawMaterialID = ?,
                                    @Quantity = ?,
                                    @Weight = ?,
                                    @TransactionType = ?";

                        $paramsInv = array(
                            $FromWarehouseID,
                            $RawMaterialID,
                            $Quantity,
                            $Weight,
                            'O'
                        );
                        sqlsrv_query($conn, $sqlInv, $paramsInv);
                        // IN - Destination Warehouse //  I For IN
                            $paramsInv = array(
                            $ToWarehouseID,
                            $RawMaterialID,
                            $Quantity,
                            $Weight,
                            'I'
                        );
                        sqlsrv_query($conn, $sqlInv, $paramsInv);
                    }

    }
                    if($RawMaterialTransferID == 0)
                    {
                        $SystemLogID = 0;
                        $FunctionID = 1;
                        $TableName = "RawMaterialTransfer";  
                        $Activity = "Adding RawMaterial Transfer : $TransferCode";
                        $UpdatedData = "0";
                                                $sql1 = "EXEC [dbo].[SystemLogInsert]
                                                                        @SystemLogID = ?,
                                                                        @UserID = ?,
                                                                        @FunctionID = ?,
                                                                        @TableName = ?,
                                                                        @Activity = ?,  
                                                                        @UpdatedData = ?";
                                                $paramss = array($SystemLogID,$UserID,$FunctionID,$TableName,$Activity,$UpdatedData);     
                                                $stmt = sqlsrv_query($conn, $sql, $paramss);

                    }
                    else
                    {
                        $SystemLogID = 0;
                        $FunctionID = 2;
                        $TableName = "RawMaterialTransfer";
                        $Activity = "Updating in RawMaterial Transfer: $TransferCode";
                        $UpdatedData = "0";
                                                $sql1 = "EXEC [dbo].[SystemLogEdit]
                                                                        @SystemLogID = ?,
                                                                        @UserID = ?,
                                                                        @FunctionID = ?,
                                                                        @TableName = ?,
                                                                        @Activity = ?,  
                                                                        @UpdatedData = ?";
                                                $paramss = array($SystemLogID,$UserID,$FunctionID,$TableName,$Activity,$UpdatedData);    
                                                $stmt = sqlsrv_query($conn, $sql, $paramss);
                    }
                    sqlsrv_commit($conn);
}





 ?>