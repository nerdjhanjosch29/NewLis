<?php
require '../Database/connection.php';

if (sqlsrv_begin_transaction($conn) === false) {
  die(print_r(sqlsrv_errors(), true));
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

try
{
    if(isset($data))
    {
        
            $ShippingTransactionID =0;
            $StorageDate = NULL;
            $DemurrageDate = NULL;
            $DateOfLastDischarge = NULL;
              $DetentionDate = NULL;
            $LodgementDate = NULL;
            $LodgementBankID = 0;
            $GatepassReceived = NULL;
            $AcknowledgementDate = NULL;
            if($data->ShippingTransactionID)
            {
                $ShippingTransactionID = $data->ShippingTransactionID;
            }
            if($data->StorageDate)
            {
                $StorageDate = $data->StorageDate;
            }
            if($data->DemurrageDate)
            {
                $DemurrageDate = $data->DemurrageDate;
            }
            if($data->DetentionDate)
            {
                $DetentionDate = $data->DetentionDate;
            }
            if($data->DateOfLastDischarge)
            {
                $DateOfLastDischarge = $data->DateOfLastDischarge;
            }
            if($data->LodgementDate)
            {
                $LodgementDate = $data->LodgementDate;
            }
            if($data->LodgementBankID)
            {
                $LodgementBankID = $data->LodgementBankID;
            }
            if($data->GatepassReceived)
            {
                $GatepassReceived = $data->GatepassReceived;
            }
            if($data->AcknowledgementDate)
            {
                $AcknowledgementDate = $data->AcknowledgementDate;
            }

            $sqlUpdate = "UPDATE ShippingTransaction SET StorageLastFreeDate = ?, DemurrageDate = ?, DetentionDate= ?, DateOfDischarge = ?, GatepassRecieved = ?,
            LodgementDate = ?, LodgementBankID = ?,
            AcknowledgeByLogistics = ? WHERE ShippingTransactionID = ?";
            $UpdateParams = array($StorageDate,$DemurrageDate,$DetentionDate,$DateOfLastDischarge,$GatepassReceived, $LodgementDate, $LodgementBankID, 
             $AcknowledgementDate,$ShippingTransactionID);
            $sqlQuery = sqlsrv_query($conn,$sqlUpdate,$UpdateParams);
            // var_dump($sqlQuery);
            if($sqlQuery)
            {
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