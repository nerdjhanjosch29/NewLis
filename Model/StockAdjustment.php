<?php
require_once '../Database/connection.php';
$data = json_decode(file_get_contents('php://input'));

if (sqlsrv_begin_transaction($conn) === false) {
    die(print_r(sqlsrv_errors(), true));
}

  $UserID = "1asfafas";
//   $validateToken = include('../validate_token.php');
//   $UserID = $decode->UserID;
//   if(!$validateToken)
//   {
//     http_response_code(404);
//     die();
//   }


try
{
    if(isset($data))
    {

        $AdjustmentNatureID = 0;


        $Remarks = "";
        // echo $CurrentStockWeight;
        if($data->AdjustmentNatureID)
        {
            $AdjustmentNatureID = $data->AdjustmentNatureID; //What kind of Adjustment: Condemed ,Variance,Correction
        }
        if($data->AdjustmentTypeID)
        {
            //Check Nature Adjustment if NatureID = 1 Auto Decrease
            if($AdjustmentNatureID == 1)
            {
                $AdjustmentTypeID = 2; //Increase 1 or Decrease 2
            }
            else 
            {
               $AdjustmentTypeID = 0;
               if($data->AdjustmentTypeID)
               {
                 $AdjustmentTypeID = $data->AdjustmentTypeID; //Increase 1 or Decrease 2
               }
            }
        }


        if($data->Remarks)
        {
            $Remarks = $data->Remarks;
        }

        $array = $data->AdjustmentDetails;
        $length = count($array);
        for($i = 0; $i < $length; $i++)
        {     
    
                    $WarehousePartitionStockID = 0;
                    $Weight = 0;
                    if($array[$i]->WarehousePartitionStockID)
                    {
                        $WarehousePartitionStockID = $array[$i]->WarehousePartitionStockID;
                        $CurrentStockWeight = 0;
                        $UnloadingTransactionID = 0;
                        $UnloadingDetailID = 0;
                        $WarehouseLocationID = 0;
                        $WarehouseID = 0;
                        $WarehousePartitionID = 0;

                        $sq1 = "SELECT EndingWeight,
                                        UnloadingTransactionID,
                                        UnloadingDetailID,
                                        WarehouseLocationID,
                                        WarehouseID,
                                        WarehousePartitionID
                                FROM WarehousePartitionStock WHERE WarehousePartitionStockID = ?";
                        $params = array($WarehousePartitionStockID);
                        $stmt3 = sqlsrv_query($conn,$sq1,$params);
                             
                        if($stmt3 === false)
                            throw new Exception('Query 1: Select on WarehousePartitionStock');
                        while($row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC))
                        {
                            $CurrentStockWeight = $row['EndingWeight'];
                            $UnloadingTransactionID = $row['UnloadingTransactionID'];
                            $UnloadingDetailID = $row['UnloadingDetailID'];
                            $WarehouseLocationID = $row['WarehouseLocationID'];
                            $WarehouseID = $row['WarehouseID'];
                            $WarehousePartitionID = $row['WarehousePartitionID'];
                        }
                  
                        $CurrentOutstandingWeight = 0;
                        $sq1 = "SELECT EndingWeight
                                FROM WarehouseInventory WHERE WarehouseLocationID = ? AND WarehouseID = ? AND WarehousePartitionID = ?";
                        $params = array($WarehouseLocationID,$WarehouseID,$WarehousePartitionID);
                        $stmt4 = sqlsrv_query($conn,$sq1,$params);
                           
                        if($stmt4 === false)
                            throw new Exception('Query 2: Select on WarehouseInventory');
                        while($row = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC))
                        {
                            $CurrentOutstandingWeight = $row['EndingWeight'];
                        }
                    }
                    if($array[$i]->Weight)
                    {
                        $Weight = $array[$i]->Weight; //New Stock
                    }
                    // Current stock from WarehousePartitionStock
                    // $CurrentStockWeight = floatval($CurrentStockWeight);
               
                    $AdjustmentWeight = $Weight;
                    $NewEndingWeight = 0;
                    $MovementWeight = 0;  
                    $WeightDifference = 0;
                    // Validate adjustment type
                    if($AdjustmentTypeID == 1)
                    {
                        // ADD STOCK
                        $MovementWeight = $AdjustmentWeight;
                        $NewEndingWeight = $CurrentStockWeight + $AdjustmentWeight;
                        $NewOutstandingWeight = $CurrentOutstandingWeight + $AdjustmentWeight;

                        $WeightDifference = $NewEndingWeight - $CurrentStockWeight;
                    }
                    else if($AdjustmentTypeID == 2)
                    {
                        // MINUS STOCK
                        if($AdjustmentWeight > $CurrentStockWeight)
                        {
                            throw new Exception("Adjustment weight is greater than current stock weight.");
                        }
                        $MovementWeight = -$AdjustmentWeight;
                        $NewEndingWeight = $CurrentStockWeight - $AdjustmentWeight;
                        $NewOutstandingWeight = $CurrentOutstandingWeight - $AdjustmentWeight;
                        $WeightDifference = $NewEndingWeight - $CurrentStockWeight;
                    }
                    else
                    {
                        throw new Exception("Invalid AdjustmentTypeID.");
                    }
                    $sq11 = "UPDATE WarehousePartitionStock SET EndingWeight = ? WHERE WarehousePartitionStockID = ? ";
                    $params = array($NewEndingWeight,$WarehousePartitionStockID);
                    $stmt1 = sqlsrv_query($conn,$sq11,$params);
                    
                    if($stmt1 === false)
                       throw new Exception('Query 3: UPDATE on WarehousePartitionStock');
                    $sq12 = "UPDATE WarehouseInventory SET EndingWeight = ? WHERE WarehouseLocationID = ? AND WarehouseID = ? AND WarehousePartitionID = ?";

                    $params = array($NewOutstandingWeight,$WarehouseLocationID,$WarehouseID,$WarehousePartitionID);
                    $stmt2 = sqlsrv_query($conn,$sq12,$params);
                    
                    if($stmt2 === false)
                       throw new Exception('Query 4: UPDATE on WarehouseInventory');

                        $LastStockAdjustmentLogID = 0;
                             $Status = 1;
                    $sql = "INSERT INTO StockAdjustmentLog(
                        WarehousePartitionStockID,
                        UnloadingTransactionID,
                        UnloadingDetailID,
                        AdjustmentNatureID,
                        AdjustmentTypeID,
                        OldWeight,
                        NewWeight,
                        EndingWeight,
                        WeightDifference,
                        Remarks,
                        Status,
                        UserID
                        )
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?) SELECT SCOPE_IDENTITY()";
                    $params = array(
                        $WarehousePartitionStockID,
                        $UnloadingTransactionID,
                        $UnloadingDetailID,
                        $AdjustmentNatureID,
                        $AdjustmentTypeID,
                        $CurrentStockWeight,
                        $AdjustmentWeight,
                        $NewEndingWeight,
                        $WeightDifference,
                        $Remarks, 
                        $Status,
                        $UserID
                    );
                $stmt10 = sqlsrv_query($conn,$sql,$params);

                 if($stmt10 === false)  throw new Exception('Query 5: Insert on StockAdjustmentLog');
                sqlsrv_next_result($stmt10); 
                sqlsrv_fetch($stmt10); 
                $LastStockAdjustmentLogID = sqlsrv_get_field($stmt10, 0);

                        $Quantity = 0;
                       // Transaction DIDs
                        $TransactionDidID = 0;
                        $TransactionTypeID = 3;
                        $sql = "EXEC [dbo].[TransactionDids]
                        @TransactionDidID = ?,
                        @TransactionTypeID = ?,
                        @MainTransactionID = ?,
                        @TransactionID = ?,
                        @WarehousePartitionStockID = ?,
                        @Quantity = ?,
                        @Weight = ?,
                        @UserID = ?";
                        $params = array(
                            $TransactionDidID,
                            $TransactionTypeID,
                            $LastStockAdjustmentLogID,
                            $LastStockAdjustmentLogID,
                            $WarehousePartitionStockID,
                            $Quantity,
                            $WeightDifference,
                            $UserID
                        );
                        $stmt6 = sqlsrv_query($conn,$sql,$params);
                        if($stmt6 === false) throw new Exception('Query 6: Insert on TransactionDid');
                        $RejectWeight = 0;
                        $TransferedWeight = 0;
                        $BinloadingWeight = 0;
                        $AdjustmentWeight = 0;
                        $sql = "EXEC [dbo].[SP_UpdateUnloadingWeights]
                                @UnloadingTransactionID = ?,
                                @RejectWeight = ?,
                                @TransferedWeight = ?,
                                @BinloadingWeight = ?,
                                @AdjustmentWeight = ? ";    
                        $params = array(
                        $UnloadingTransactionID,
                        $RejectWeight,
                        $TransferedWeight,
                        $BinloadingWeight,
                        $WeightDifference
                        );
                        $stmt7 = sqlsrv_query($conn,$sql,$params);
                        // var_dump($stmt7);
                        if($stmt7 === false) throw new Exception('Query 7: UPDATE on SP_UpdateUnloadingWeights');

                }
                
  }
                         /*
                        |--------------------------------------------------------------------------
                        | COMMIT
                        |--------------------------------------------------------------------------
                        */
                          if(sqlsrv_commit($conn) === false)
                        {
                            throw new Exception(print_r(sqlsrv_errors(),true));
                        }
    echo 1;
}
catch(Exception $e){
    sqlsrv_rollback($conn);
  header('Content-Type: application/json');

    echo json_encode([

        "error"   => $e->getMessage()
    ]);
}
?>