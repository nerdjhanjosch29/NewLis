<?php 

require '../Database/connection.php';
$data = json_decode(file_get_contents('php://input'));
if (sqlsrv_begin_transaction($conn) === false) {
  die(print_r(sqlsrv_errors(), true));
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
    $isTransactionID = 0;
    $UnloadingTransactionID = 0;
    $PurchaseOrderID = 0;
    $ShippingTransactionID = 0;
    $BeforeImage = "";
    $DateUnload = "";
    $DrNumber = "";
     $LIMSReferenceCode = "";
    $DateTimeUnload = "";
    $TruckID = 0;
    $SupplierID = 0;
    $Status = 0;
    $TableName = "Unloading";
    $deleted = 0;
    $ImageID = 0;

  if($data->isTransactionID)
  {
      $isTransactionID = $data->isTransactionID;
  }
  if($data->DateUnload)
  {
    $DateUnload =$data->DateUnload;
  }
    if($data->DateTimeUnload)
  {
    $DateTimeUnload =$data->DateTimeUnload;
  }
  if($data->DrNumber)
  {
    $DrNumber = $data->DrNumber;
  }
    if($data->LIMSReferenceCode)
  {
    $LIMSReferenceCode = $data->LIMSReferenceCode;
  }
  if($data->TruckID)
  {
    $TruckID =$data->TruckID;
  }
  if($data->SupplierID)
  {
    $SupplierID =$data->SupplierID;
  }
  if($data->PurchaseOrderID)
  {
    $PurchaseOrderID =$data->PurchaseOrderID;
  }
  if($data->ShippingTransactionID)
  {
    $ShippingTransactionID=$data->ShippingTransactionID;
  }
  if($data->Status)
  {
    $Status = $data->Status;
  }
  if($data->UnloadingTransactionID)
  {
    $UnloadingTransactionID = $data->UnloadingTransactionID;
  }
  $sql = "SELECT DrNumber FROM UnloadingTransaction WHERE UnloadingTransactionID = ?";
          $params = array($UnloadingTransactionID);
          $stmt1 = sqlsrv_query($conn, $sql, $params);
          if($stmt1 === false)throw new Exception('Query 1: Select DRNumber on UnloadingTransaction');
          $OldData = "";
          while($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC))
          {
            sqlsrv_commit($conn);
            $OldData = $row['DrNumber'];
          }
    if($UnloadingTransactionID == 0)
    {
      $newID = 0;
      $WarehouseStockinglastID = 0;
      $sql = "INSERT INTO UnloadingTransaction 
      (isTransactionID,PurchaseOrderID,ShippingTransactionID,DateTimeUnload,DateUnload,DrNumber,LIMSReferenceCode,
      TruckID,SupplierID,Status,UserID)
      VALUES(?,?,?,?,?,?,?,?,?,?,?) SELECT SCOPE_IDENTITY()";
      $params = array($isTransactionID,$PurchaseOrderID,$ShippingTransactionID,$DateTimeUnload,$DateUnload,$DrNumber,$LIMSReferenceCode,
      $TruckID,$SupplierID,$Status,$UserID);
      $stmt = sqlsrv_query($conn, $sql, $params);
             if($stmt === false)throw new Exception('Query 2: INSERT on UnloadingTransaction');
      sqlsrv_next_result($stmt); 
      sqlsrv_fetch($stmt); 
      $newID = sqlsrv_get_field($stmt, 0);
      $RejectedWeight = 0;
 
      $array = $data->UnloadingDetail;
      $length = count($array);
      for($i = 0; $i<$length; $i++)
      {
            $PullOutID = 0;
            $RawMaterialID = 0;
            $WarehouseLocationID = 0;
            $WarehouseID = 0;
            $WarehousePartitionID = 0;
            $Quantity = 0;
            $Weight = 0;
            $UnloadingDetailID =0;
            $deleted = 0;
            if($array[$i]->PullOutID)
            {
              $PullOutID =$array[$i]->PullOutID;
            }
            if($array[$i]->RawMaterialID)
            {
              $RawMaterialID = $array[$i]->RawMaterialID;
            }
            if($array[$i]->WarehouseLocationID)
            {
              $WarehouseLocationID = $array[$i]->WarehouseLocationID;
            }
            if($array[$i]->WarehouseID)
            {
            $WarehouseID = $array[$i]->WarehouseID;
            }
            if($array[$i]->WarehousePartitionID)
            {
              $WarehousePartitionID = $array[$i]->WarehousePartitionID;
            }
            if($array[$i]->Quantity)
            {
              $Quantity = $array[$i]->Quantity;
            }
            if($array[$i]->Weight)
            {
              $Weight = $array[$i]->Weight;
            }
            if($UnloadingDetailID == 0)
            {
              $LastUnloadingDetailID = 0;
              $sqlInsertDetail = "INSERT INTO UnloadingDetail (UnloadingTransactionID,PullOutID,RawMaterialID, WarehouseLocationID, WarehouseID, 
              WarehousePartitionID,Quantity,Weight,deleted)VALUES(?,?,?,?,?,?,?,?,?) SELECT SCOPE_IDENTITY()";
              $paramInsertDetail = array($newID,$PullOutID,$RawMaterialID,$WarehouseLocationID,$WarehouseID,$WarehousePartitionID,$Quantity,$Weight,$deleted);
              $stmt20 = sqlsrv_query($conn, $sqlInsertDetail, $paramInsertDetail);
                    if($stmt20 === false)throw new Exception('Query 3: INSERT on Unloading Detail');
              sqlsrv_next_result($stmt20); 
              sqlsrv_fetch($stmt20); 
              $LastUnloadingDetailID = sqlsrv_get_field($stmt20, 0); 
                 // Warehouse Add Stocking
                $sql = "EXEC [dbo].[WarehouseAddStocking]
                @WarehousePartitionStockID = ?,
                @UnloadingTransactionID = ?,
                @UnloadingDetailID = ?,
                @RawMaterialID = ?,
                @WarehouseLocationID = ?,
                @WarehouseID = ?,
                @WarehousePartitionID = ?,
                @AcceptedWeight = ?,
                @RejectedWeight = ?,
                @UnloadedWeight = ?,
                @UnloadedQuantity = ?,
                @EndingQuantity = ?,
                @EndingWeight = ?,
                @StockingDate = ?";
                $WarehousePartitionStockID = 0;
                $params = array(
                  $WarehousePartitionStockID,
                  $newID,
                  $LastUnloadingDetailID,
                  $RawMaterialID,
                  $WarehouseLocationID,
                  $WarehouseID,
                  $WarehousePartitionID,
                  $Weight,
                  $RejectedWeight,
                  $Weight,
                  $Quantity,
                  $Quantity,
                  $Weight,
                  $DateUnload
                );
                $stmt20 = sqlsrv_query($conn, $sql, $params);
                  if($stmt20 === false)throw new Exception('Query 4: INSERT on WarehouseAddStocking');
                  while($row = sqlsrv_fetch_array($stmt20, SQLSRV_FETCH_ASSOC))
                      {
                        $WarehouseStockinglastID = $row['lastID'];
                      }
                      // Warehouse Inventory
                      $sql = "EXEC [dbo].[WarehouseInventories]
                      @WarehouseID = ?, 
                      @WarehouseLocationID = ?,
                      @WarehousePartitionID = ?,
                      @RawMaterialID = ?,
                      @AcceptedWeight = ?,
                      @RejectedWeight = ?,
                      @EndingQuantity = ?,
                      @EndingWeight = ?";
                      $params = array($WarehouseID,$WarehouseLocationID,$WarehousePartitionID,$RawMaterialID,$Weight,$RejectedWeight,$Quantity,$Weight);
                      $stmt = sqlsrv_query($conn,$sql,$params);
                     if($stmt === false)throw new Exception('Query 5: INSERT on WarehouseInventories');
            }
      }
    }
              // Transaction DIDs
              $TransactionDidID = 0;
              $TransactionTypeID = 1;
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
                $newID,
                $LastUnloadingDetailID,
                $WarehouseStockinglastID,
                $Quantity,
                $Weight,
                $UserID
              );
              $stmt6 = sqlsrv_query($conn, $sql,$params);
               if($stmt6 === false)throw new Exception('Query 6: INSERT on TransactionDids');
    // IMAGE UPLOAD (still part of transaction)
    // if($Status == 1 && isset($_FILES['files']))
    // {
    //   $files = $_FILES['files']['name'];
    //   $length = count($files);

    //   for ($i = 0; $i < $length; $i++)
    //   {
    //     $fileName = $_FILES['files']['name'][$i];
    //     $fileTmpName = $_FILES['files']['tmp_name'][$i];

    //     $fileExt = explode('.', $fileName);    
    //     $fileActualExt = strtolower(end($fileExt));

    //     $allowed = array('jpg','jpeg','png');

    //     if (!in_array($fileActualExt, $allowed)) {
    //       throw new Exception("Invalid file type");
    //     }

    //     $fileNameNew = uniqid('', true) . "." . $fileActualExt;
    //     $BeforeImage = 'UnloadingImages/' . $fileNameNew;

    //     if (!move_uploaded_file($fileTmpName, $BeforeImage)) {
    //       throw new Exception("File upload failed");
    //     }

    //     $sql = "INSERT INTO ImageTable 
    //     (TableID,ImageCategory,ImageUrl,TableName,deleted)
    //     VALUES (?,?,?,?,?)";

    //     $params = array($UnloadingTransactionID,0,$BeforeImage,$TableName,$deleted);

    //     $stmt = sqlsrv_query($conn, $sql, $params);
    //     if($stmt === false) throw new Exception(print_r(sqlsrv_errors(), true));
    //   }
    // }
    // SYSTEM LOG
    if($UnloadingTransactionID == 0)
    {
      $SystemLogID = 0;
      $FunctionID = 1; 
      $Activity = "Insert Unloading Transaction With a DrNumber : $DrNumber";
      $sql = "EXEC [dbo].[SystemLogs]
      @SystemLogID = ?,
      @UserID = ?,
      @FunctionID = ?,
      @TableName = ?,
      @Activity = ?,
      @UpdatedData = ?";
      $params = array($SystemLogID,$UserID,$FunctionID,$TableName,$Activity,"0");
      $stmt = sqlsrv_query($conn, $sql, $params);

         if($stmt === false)throw new Exception('Query 7: INSERT on SystemLogs');
    }
                  if(sqlsrv_commit($conn) === false)
                    {
                      throw new Exception(print_r(sqlsrv_errors(),true));
                    }
                    echo 1;
  }
}
catch(Exception $e)
{
  sqlsrv_rollback($conn);
  echo json_encode([
    "message" => $e->getMessage()
  ]);
}
?>