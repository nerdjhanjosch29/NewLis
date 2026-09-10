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
             $isTransactionID = 1;
            $ShippingTransactionID = 0;
            $DateTimeUnload = "";
            $DateUnload = "";
            $DrNumber = "";
            $TruckID = 0;
            $SupplierID = 0;
            $Status = 0;
            $UnloadingTransactionID = 0;
            $PurchaseOrderID = 0;
            $UserID = "";
            if($data->UnloadingTransactionID)
            {
                $UnloadingTransactionID = $data->UnloadingTransactionID;
            }
            if($data->isTransactionID)
            {
                $isTransactionID = $data->isTransactionID;
            }
            if($data->DateTimeUnload)
            {
                $DateTimeUnload = $data->DateTimeUnload;
            }
            if($data->DateUnload)
            {
                $DateUnload = $data->DateUnload;
            }
            if($data->DrNumber)
            {
                $DrNumber = $data->DrNumber;
            }
            if($data->TruckID)
            {
                $TruckID = $data->TruckID;
            }
            if($data->SupplierID)
            {
                $SupplierID = $data->SupplierID;
            }     
            if($UnloadingTransactionID == 0)
            {
                $sq1 = "INSERT INTO UnloadingTransaction (isTransactionID,PurchaseOrderID,ShippingTransactionID,DateTimeUnload,DateUnload,DrNumber,
                TruckID,SupplierID,Status,UserID)VALUES(?,?,?,?,?,?,?,?,?,?)";
                $params = array($isTransactionID,$PurchaseOrderID,$ShippingTransactionID,$DateTimeUnload,$DateUnload,$DrNumber,
                $TruckID,$SupplierID,$Status,$UserID);
                $stmt = sqlsrv_query($conn,$sq1,$params);
                echo 1;
            }
            else
            {
                    $PurchaseOrderID = 0;
                if($data->PurchaseOrderID)
                    {
                        $PurchaseOrderID = $data->PurchaseOrderID;
                    }
                    $sql = "UPDATE UnloadingTransaction SET PurchaseOrderID = ?, DrNumber = ?,
                                TruckID = ?, SupplierID = ? WHERE UnloadingTransactionID = ?";
                    $params = array($PurchaseOrderID,$DrNumber,$TruckID,$SupplierID,$UnloadingTransactionID);
                    $stmt = sqlsrv_query($conn,$sql,$params);
                    echo 2;
            }
              sqlsrv_commit($conn);  
        }
    }
    catch(Exception $e)
    {
    sqlsrv_rollback($conn);
    header('Content-Type: application/json; charset=utf-8');
    echo 0;
    }   

?>