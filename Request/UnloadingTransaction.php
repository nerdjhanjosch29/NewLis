<?php 

require '../Database/connection.php';
if ( sqlsrv_begin_transaction( $conn ) === false ) {
  die( print_r( sqlsrv_errors(), true ));
} 
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
    // $data = $data->data;
    $isTransactionID = 0;
    $UnloadingTransactionID = 0;
    $PO = 0;
    $BL = 0;
    $BeforeImage = "";
    $ContainerNumber = "";
    $DateUnload = "";
    $BeforeImage ="";
    $DrNumber = "";
    $DateTimeUnload = "";
    $TruckID = 0;
    $SupplierID = 0;
    $RawMaterialID = 0;
    $WarehouseLocationID = 0;
    $WarehouseID = 0;
    $WarehousePartitionID = 0;
    $Quantity = 0;
    $Weight = 0;
    $Status = 0;
    $TableName = "Unloading";
    $deleted = 0;
    $ImageID = 0;
    
  if($data->isTransactionID)
  {
      $isTransactionID = $data->isTransactionID;
  }
  if($data->UnloadingTransactionID)
  {
    $UnloadingTransactionID = $data->UnloadingTransactionID;
  }
  $sql = "SELECT DrNumber FROM UnloadingTransaction WHERE UnloadingTransactionID = ?";
          $params = array($UnloadingTransactionID);
          $stmt = sqlsrv_query($conn, $sql, $params);
          $OldData = "";
          while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
          {
            sqlsrv_commit($conn);
            $OldData = $row['DrNumber'];
          }
  if($data->PO)
  {
    $PO =$data->PO;
  }
  if($data->BL)
  {
    $BL=$data->BL;
  }
  if($data->ContainerNumber)
  {
    $ContainerNumber =$data->ContainerNumber;
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
  if($data->TruckID)
  {
    $TruckID =$data->TruckID;
  }
  if($data->SupplierID)
  {
    $SupplierID =$data->SupplierID;
  }
  if($data->RawMaterialID)
  {
    $RawMaterialID = $data->RawMaterialID;
  }
  if($data->WarehouseLocationID)
  {
    $WarehouseLocationID = $data->WarehouseLocationID;
  }
  if($data->WarehouseID)
  {
  $WarehouseID = $data->WarehouseID;
  }
  if($data->WarehousePartitionID)
  {
    $WarehousePartitionID = $data->WarehousePartitionID;
  }
  if($data->Quantity)
  {
    $Quantity = $data->Quantity;
  }
  if($data->Weight)
  {
    $Weight = $data->Weight;
  }
  if($data->Status)
  {
    $Status = $data->Status;
  }

  if($UnloadingTransactionID == 0)
  {
      // $DateTimeUnload = NULL;
      $newID = 0;
      $sql="INSERT INTO UnloadingTransaction (isTransactionID,PO,BL,ContainerNumber,DateTimeUnload, DateUnload, DrNumber,
      TruckID,SupplierID,RawMaterialID, 
      WarehouseLocationID,WarehouseID,WarehousePartitionID,Quantity,Weight,Status,UserID)
      VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) SELECT SCOPE_IDENTITY()";
      $params = array($isTransactionID,$PO,$BL,$ContainerNumber,$DateTimeUnload,$DateUnload, $DrNumber, $TruckID,
      $SupplierID,$RawMaterialID,$WarehouseLocationID, $WarehouseID, $WarehousePartitionID,$Quantity,$Weight,$Status, $UserID);
      $stmt = sqlsrv_query($conn, $sql, $params);
      // var_dump($stmt);
      sqlsrv_next_result($stmt); 
      sqlsrv_fetch($stmt); 
      $newID = sqlsrv_get_field($stmt, 0);   

      if($stmt)
      {
      echo 1;

            $ExpirationDate = NULL;
            $WarehousePartitionStockID = 0;
            $WarehouseStockinglastID = 0;
            $sql="EXEC	[dbo].[WarehouseAddStocking]
            @WarehousePartitionStockID = ?,
            @UnloadingTransactionID = ?,
            @RawMaterialID = ?,
            @WarehouseLocationID = ?,
            @WarehouseID = ?,
            @WarehousePartitionID = ?,
            @UnloadedQuantity = ?,
            @UnloadedWeight = ?,
            @EndingQuantity = ?,
            @EndingWeight = ?,
            @StockingDate = ?";
            $params = array($WarehousePartitionStockID,$newID,$RawMaterialID,
            $WarehouseLocationID,$WarehouseID,$WarehousePartitionID,
            $Quantity,$Weight,$Quantity,$Weight,$DateUnload);
              $stmt20 = sqlsrv_query($conn, $sql, $params);
          // var_dump($newID);
          // sqlsrv_commit($conn);
          ///Save the Latest Stocking that generated once Successfull Unloading
          while($row = sqlsrv_fetch_array($stmt20, SQLSRV_FETCH_ASSOC))
              {
                $WarehouseStockinglastID = $row['lastID'];
              }
            //  var_dump($WarehouseStockinglastID);
            //Warehouse Inventory
            $sql="EXEC [dbo].[WarehouseInventories]
                @WarehouseID = ?,
                @RawMaterialID = ?,
                @Quantity = ?,
                @Weight = ?";
              $params = array($WarehouseID,$RawMaterialID,$Quantity,
              $Weight);
              $stmt = sqlsrv_query($conn, $sql, $params);
              sqlsrv_commit($conn);
      }       
  }
        // if($Status == 1)
          // {
            //TransactionDidID = 3 is for Unloading Transaction traceability
            $TransactionDidID = 0;
            $TransactionTypeID = 1;
            $sql = "EXEC[dbo].[TransactionDids]
            @TransactionDidID = ?,
            @TransactionTypeID = ?,
            @MainTransactionID = ?,
            @TransactionID = ?,
            @WarehousePartitionStockID = ?,
            @Quantity = ?,
            @Weight = ?,
            @UserID = ?";
            $params = array($TransactionDidID,$TransactionTypeID,$newID,$newID,$WarehouseStockinglastID,$Quantity,$Weight,$UserID);
            $stmt6 = sqlsrv_query($conn, $sql,$params);

            // var_dump($stmt6);
          // }
    if($Status == 1)
    {
      if(isset($_FILES['files']))
      {
        $data = json_decode($_POST['data']);
        $files = $_FILES['files']['name'];
        $length = count($files); // count the number of files uploaded
        for ($i = 0; $i<$length; $i++) { 
        $fileName = $_FILES['files']['name'][$i];
        $fileTmpName = $_FILES['files']['tmp_name'][$i];
        $fileSize = $_FILES['files']['size'][$i];
        $fileError = $_FILES['files']['error'][$i];
        $fileType = $_FILES['files']['type'][$i];
        // var_dump($length);
        $fileExt = explode('.', $fileName);    
        $fileActualExt = strtolower(end($fileExt));
        $allowed = array('jpg', 'jpeg', 'png');   
        if (in_array($fileActualExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 4500000) {
                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $BeforeImage = 'UnloadingImages/' . $fileNameNew;
                    if (move_uploaded_file($fileTmpName, $BeforeImage)) {                 
                        $sql = "INSERT INTO ImageTable (TableID,ImageCategory,ImageUrl,TableName,deleted) VALUES (?,?,?,?,?)";
                        $params = array($UnloadingTransactionID,$ImageCategory,$BeforeImage,$TableName,$deleted);
                        $stmt = sqlsrv_query($conn, $sql, $params);

                        // var_dump($stmt);
                        sqlsrv_commit($conn);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'File size exceeds limit']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error uploading file']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
        }
      }
    }

    }
      if($stmt)
      {
        if($UnloadingTransactionID == 0 )
          {       
              $SystemLogID = 0;
              $FunctionID = 1; 
              $TableName = "Unloading";
              $Activity = "Insert Unloading Transaction With a DrNumber : $DrNumber";
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
}
?>