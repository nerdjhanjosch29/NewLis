<?php 
require_once 'Database/connection.php';
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
    $ReceivingInspectionID = 0;
     $UnloadingTransactionID = 0;
      $RejectedWeight = 0;


    if(isset($data))
    {
        if($data->ReceivingInspectionID)
        {
          $ReceivingInspectionID = $data->ReceivingInspectionID;
        }
        if($data->UnloadingTransactionID)
        {
          $UnloadingTransactionID = $data->UnloadingTransactionID;

              $AcceptedWeight = 0;
              $sq1 = "SELECT AcceptedWeight FROM UnloadingTransaction WHERE UnloadingTransactionID = ?";
              $params = array($UnloadingTransactionID);
              $stmt = sqlsrv_query($conn, $sql, $params);
              while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
              {
                $AcceptedWeight = $row['AcceptedWeight'];
              }
        }
        if($data->RejectedWeight)
        {
          $FinalWeight = 0;
          $RejectedWeight = $data->RejectedWeight;
           $FinalWeight = $RejectedWeight + $AcceptedWeight;

        }

        if($ReceivingInspectionID == 0)
        {
            $sql = "INSERT INTO ReceivingInspection (ReceivingInspectionID, UnloadingTransactionID, RejectedWeight, UserID)
                    VALUES(?,?,?,?)";
                  $params = array($ReceivingInspectionID, $UnloadingTransactionID, $RejectedWeight, $UserID);
            $stmt = sqlsrv_query($conn,$sql,$params);
      // Warehouse Add Stocking
      $sql = "EXEC [dbo].[WarehouseAddStocking]
      @WarehousePartitionStockID = ?,
      @UnloadingTransactionID = ?,
      @RawMaterialID = ?,
      @WarehouseLocationID = ?,
      @WarehouseID = ?,
      @WarehousePartitionID = ?,
      @AcceptedWeight = ?,
      @RejectedWeight = ?,
      @UnloadedQuantity = ?,
      @UnloadedWeight = ?,
      @EndingQuantity = ?,
      @EndingWeight = ?,
      @StockingDate = ?";

      $WarehousePartitionStockID = 0;

      $params = array(
        $WarehousePartitionStockID,
        $newID,
        $RawMaterialID,
        $WarehouseLocationID,
        $WarehouseID,
        $WarehousePartitionID,
        $AcceptedWeight,
        $RejectedWeight ,
        $Quantity,
        $FinalWeight,
        $Quantity,
        $FinalWeight,
        $DateUnload
      );

      $stmt20 = sqlsrv_query($conn, $sql, $params);
      if($stmt20 === false) throw new Exception(print_r(sqlsrv_errors(), true));

      while($row = sqlsrv_fetch_array($stmt20, SQLSRV_FETCH_ASSOC))
      {
        $WarehouseStockinglastID = $row['lastID'];
      }

      // Warehouse Inventory
      $sql = "EXEC [dbo].[WarehouseInventories]
      @WarehouseID = ?,
      @RawMaterialID = ?,
      @AcceptedWeight = ?,
      @RejectedWeight = ?,
      @EndingQuantity = ?,
      @EndingWeight = ?";

      $params = array($WarehouseID,$RawMaterialID,$AcceptedWeight,$RejectedWeight,$EndingQuantity,$FinalWeight);
      $stmt = sqlsrv_query($conn, $sql, $params);

        }
    }
?>