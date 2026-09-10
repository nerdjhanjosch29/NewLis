<?php
require '../Database/connection.php';

// Get JSON data from request body
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

try {

    if (isset($data)) {

        // Extract data from JSON
        $ContractPerformaID = $data->ContractPerformaID;

        $sql = "SELECT ContractNo FROM ContractPerforma WHERE ContractPerformaID = ?";
        $params = array($ContractPerformaID);
        $stmt = sqlsrv_query($conn, $sql, $params);

        if($stmt === false)
            throw new Exception(print_r(sqlsrv_errors(), true));

        $OldData = "";

        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
        {
            $OldData = $row['ContractNo'];
        }

        $ContractNo = $data->ContractNo;
        $Quantity = $data->Quantity;
        $EstimatedContainer = $data->EstimatedContainer;
        $Packaging = $data->Packaging;
        $PackedInID = $data->PackedInID;
        $RawMaterialImportLocalID = $data->RawMaterialImportLocalID;
        $SupplierID = $data->SupplierID;
        $SupplierAddress = $data->SupplierAddress;
        $PortOfDischargeID = $data->PortOfDischargeID;
        $FromShipmentPeriod = $data->FromShipmentPeriod;
        $ToShipmentPeriod = $data->ToShipmentPeriod;
        $CountryOfOrigin = $data->CountryOfOrigin;
        $Status = 0;

        // Define the stored procedure call
        $sql = "EXEC [dbo].[ContractPerformas]
            @ContractPerformaID = ?,
            @ContractNo = ?,
            @Quantity = ?,
            @EstimatedContainer = ?,
            @Packaging = ?,
            @PackedInID = ?,
            @RawMaterialImportLocalID = ?,
            @SupplierID = ?,
            @SupplierAddress = ?,
            @PortOfDischargeID = ?,
            @FromShipmentPeriod = ?,
            @ToShipmentPeriod = ?,
            @CountryOfOrigin = ?,
            @Status = ?,
            @UserID = ?";

        $params = array(
            $ContractPerformaID,
            $ContractNo,
            $Quantity,
            $EstimatedContainer,
            $Packaging,
            $PackedInID,
            $RawMaterialImportLocalID,
            $SupplierID,
            $SupplierAddress,
            $PortOfDischargeID,
            $FromShipmentPeriod,
            $ToShipmentPeriod,
            $CountryOfOrigin,
            $Status,
            $UserID
        );

        $stmt = sqlsrv_query($conn, $sql, $params);

        if($stmt === false)
            throw new Exception(print_r(sqlsrv_errors(), true));

        $lastID = 0;
        $result = 0;

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
        {
            echo $result = $row['result'];
            $lastID = $row['lastID'];
        }

        $ContractPerformaIDs = 0;
        if($ContractPerformaID == 0)
        {
            $ContractPerformaIDs = $lastID;
        }
        else
        {
            $ContractPerformaIDs = $ContractPerformaID;
        }
        if($result != 0)
        {
            $array = $data->ContractInventoryItem;
            $length = count($array);

            // Mark all as deleted first
            $sql = "UPDATE ContractInventoryItem
                    SET deleted = 1
                    WHERE ContractPerformaID = ?";
            $params = array($ContractPerformaIDs);
            $stmtDelete = sqlsrv_query($conn,$sql,$params);

            if($stmtDelete === false)
                throw new Exception(print_r(sqlsrv_errors(), true));
            for($i = 0; $i < $length; $i++)
            {
                $ContractInventoryItemID = 0;
                $CategoryID = 0;
                $deleted = 0;
                if(isset($array[$i]->ContractInventoryItemID))
                {
                    $ContractInventoryItemID = $array[$i]->ContractInventoryItemID;
                }
                if(isset($array[$i]->CategoryID))
                {
                    $CategoryID = $array[$i]->CategoryID;
                }
                if($ContractInventoryItemID == 0)
                {
                    $sql = "INSERT INTO ContractInventoryItem
                            (ContractPerformaID,RawMaterialID,deleted,UserID)
                            VALUES (?,?,?,?)";
                    $params = array(
                        $ContractPerformaIDs,
                        $CategoryID,
                        $deleted,
                        $UserID
                    );
                    $stmt1 = sqlsrv_query($conn,$sql,$params);
                    if($stmt1 === false)
                        throw new Exception(print_r(sqlsrv_errors(), true));
                }
                else
                {
                    $sqlUpdate = "UPDATE ContractInventoryItem
                                  SET ContractPerformaID = ?,
                                      RawMaterialID = ?,
                                      deleted = ?
                                  WHERE ContractInventoryItemID = ?";
                    $paramsUpdate = array(
                        $ContractPerformaIDs,
                        $CategoryID,
                        $deleted,
                        $ContractInventoryItemID
                    );
                    $stmt3 = sqlsrv_query($conn,$sqlUpdate,$paramsUpdate);
                    if($stmt3 === false)
                        throw new Exception(print_r(sqlsrv_errors(), true));
                }
            }
        }
        if($result != 0)
        {
            $SystemLogID = 0;
            $FunctionID = 1;
            $TableName = "Contract";
            $Activity = "Insert Contract : $ContractNo";
            $UpdatedData = "0";

            $sql1 = "EXEC [dbo].[SystemLogs]
                    @SystemLogID = ?,
                    @UserID = ?,
                    @FunctionID = ?,
                    @TableName = ?,
                    @Activity = ?,
                    @UpdatedData = ?";
            $paramss = array(
                $SystemLogID,
                $UserID, 
                $FunctionID,
                $TableName,
                $Activity,
                $UpdatedData
            );
            $stmtLog = sqlsrv_query($conn, $sql1, $paramss);
            if($stmtLog === false)
                throw new Exception(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_commit($conn);
    }
}
catch (Exception $e)
{
    sqlsrv_rollback($conn);
    die($e->getMessage());
}