<?php

require_once '../Database/connection.php';
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
        $ContractPerformaID = 0;
        if($data->ContractPerformaID)
        {
            $ContractPerformaID = $data->ContractPerformaID;
        }
        if($ContractPerformaID != 0)
        {
        $array = $data->ContractInventoryItem;

        $length = count($array);
        for($i = 0; $i<$length; $i++)
        {
                $ContractInventoryItemID =0;
                $RawMaterialID = 0; //AS InventoryItemID
                $deleted = 0;
                if($array[$i]->ContractInventoryItemID)
                {
                    $ContractInventoryItemID = $array[$i]->ContractInventoryItemID;
                }
                if($array[$i]->RawMaterialID)
                {
                    $RawMaterialID = $array[$i]->RawMaterialID;
                }
                if($array[$i]->Quantity)
                {
                    $Quantity = $array[$i]->Quantity;
                }
                if($ContractInventoryItemID == 0)
                {
                    $sql = "INSERT INTO ContractInventoryItem (ContractPerformaID,RawMaterialID,Quantity,deleted,UserID) VALUES (?,?,?,?,?)";
                    $params = array($ContractPerformaID,$RawMaterialID,$Quantity,$deleted,$UserID);
                    $stmt1 = sqlsrv_query($conn,$sql,$params);
                }
                else
                {
                    
                    $sql = "UPDATE ContractInventoryItem SET deleted = 1 WHERE ContractPerformaID = ?";
                    $params = array($ContractPerformaID);
                    $stmt2 = sqlsrv_query($conn,$sql,$params);


                    $sqlUpdate = "UPDATE ContractInventoryItem SET ContractPerformaID = ?, RawMaterialID = ?, Quantity = ?, deleted = ? 
                    WHERE ContractInventoryItemID = ? ";
                    $paramsUpdate = array($ContractPerformaID,$RawMaterialID,$Quantity,$deleted,$ContractInventoryItemID);
                    $stmt3 = sqlsrv_query($conn,$sqlUpdate,$paramsUpdate);

                }
                sqlsrv_commit($conn);

                // var_dump($stmt1);
                // var_dump($stmt2);
                // var_dump($stmt3);
        }
        }

        
    }
}
catch(Exception $e)
{
      sqlsrv_rollback($conn);
    header('Content-Type: application/json; charset=utf-8');
    echo 0;
}

?>