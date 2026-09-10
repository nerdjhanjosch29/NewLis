<?php 
require '../Database/connection.php';
$data = json_decode(file_get_contents('php://input'));
if ( sqlsrv_begin_transaction( $conn ) === false ) {
  die( print_r( sqlsrv_errors(), true ));
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
      $ShippingTransactionID = 0;
        if($data->ShippingTransactionID)
        {
          $ShippingTransactionID = $data->ShippingTransactionID;
        }
        if($ShippingTransactionID == 0)
        {
          function toSQLDate($value) {
          return (!empty($value) && strtotime($value) !== false) ? date('Y-m-d H:i:s', strtotime($value)) : null;
          }
            $Packaging = 0;
            if(isset($data->Packaging))
            { 
              $Packaging=$data->Packaging;
            }
            if($Packaging == 1) //$Packaging == 1 Containerized
            {
              $ContractPerformaID = 0;
              $Lot = 0;
              $RawMaterialImportLocalID = 0;
              $SupplierID = 0;
              $BAI_SPS_IC = "";
              $BPI_SPS_IC = "";
              $MBL = "";
              $ShippingLineID = 0;
              $Vessel = "";
              $HBL = "";
              $Forwarder = "";
              $ContainerTypeID = 0;
              $NoOfContainer = 0;
              $Quantity = 0;
              $BrokerID = 0;
              $BankID = 0;
              $Status = 0;   
              $LodgementBankID = 0;
              $Remarks = "";
              $UserID = "";  
              $SailingDate = date('Y-m-d H:i:s');
              $FromBPIValidity = NULL;
              $FromBAIValidity = NULL;
              $ToBAIValidity = NULL;
              $ToBPIValidity = NULL;
              $AdvanceDocumentsReceived = NULL;
              $ETD = NULL;
              $ETA = NULL;
              $DateDocsReceivedByBroker = NULL;
              $OriginalDocsAvailavilityDate = NULL;
              $DateOfPickup = NULL;
              if(isset($data->FromBPIValidity))
              { 
                $FromBPIValidity=$data->FromBPIValidity;
              }
              if(isset($data->FromBAIValidity))
              { 
                $FromBAIValidity=$data->FromBAIValidity;
              }
              if(isset($data->ToBAIValidity))
              { 
                $ToBAIValidity=$data->ToBAIValidity;
              }
              if(isset($data->ToBPIValidity))
              { 
                $ToBPIValidity=$data->ToBPIValidity;
              }
              if(isset($data->AdvanceDocumentsReceived))
              { 
                $AdvanceDocumentsReceived=$data->AdvanceDocumentsReceived;
              }
              if(isset($data->ETD))
              { 
                $ETD=$data->ETD;
              }
              if(isset($data->ETA))
              { 
                $ETA=$data->ETA;
              }
              if(isset($data->DateDocsReceivedByBroker))
              { 
                $DateDocsReceivedByBroker=$data->DateDocsReceivedByBroker;
              }
              if(isset($data->OriginalDocsAvailavilityDate))
              { 
                $OriginalDocsAvailavilityDate=$data->OriginalDocsAvailavilityDate;
              }
              if(isset($data->DateOfPickup))
              { 
                $DateOfPickup=$data->DateOfPickup;
              }
      
              if(isset($data->ShippingTransactionID))
              { 
                $ShippingTransactionID=$data->ShippingTransactionID;
              }
              if(isset($data->ContractPerformaID))
              { 
                $ContractPerformaID=$data->ContractPerformaID;
              }
              if(isset($data->Lot))
              { 
                $Lot=$data->Lot;
              }
              if(isset($data->RawMaterialImportLocalID))
              { 
                $RawMaterialImportLocalID=$data->RawMaterialImportLocalID;
              }
              if(isset($data->SupplierID))
              { 
                $SupplierID=$data->SupplierID;
              }
              if(isset($data->BAI_SPS_IC))
              { 
                $BAI_SPS_IC=$data->BAI_SPS_IC;
              }
              if(isset($data->BPI_SPS_IC))
              { 
                $BPI_SPS_IC=$data->BPI_SPS_IC;
              }
              if(isset($data->MBL))
              { 
                $MBL=$data->MBL;
              }
              if(isset($data->ShippingLineID))
              { 
                $ShippingLineID=$data->ShippingLineID;
              }
              if(isset($data->Vessel))
              { 
                $Vessel=$data->Vessel;
              }
              if(isset($data->HBL))
              { 
                $HBL=$data->HBL;
              }
              if(isset($data->Forwarder))
              { 
                $Forwarder=$data->Forwarder;
              }
              if(isset($data->ContainerTypeID))
              { 
                $ContainerTypeID=$data->ContainerTypeID;
              }
              if(isset($data->NoOfContainer))
              { 
                $NoOfContainer=$data->NoOfContainer;
              }
              if(isset($data->Quantity))
              { 
                $Quantity=$data->Quantity;
              }
              if(isset($data->BrokerID))
              { 
                $BrokerID=$data->BrokerID;
              }
              if(isset($data->BankID))
              { 
                $BankID=$data->BankID;
              }        
              if(isset($data->Status))
              { 
                $Status=$data->Status;
              }
              if(isset($data->LodgementBankID))
              { 
                $LodgementBankID=$data->LodgementBankID;
              }
              if(isset($data->Remarks))
              { 
                $Remarks=$data->Remarks;
              }
              if(isset($data->UserID))
              { 
                $UserID=$data->UserID;
              }
                $sql = "INSERT INTO ShippingTransaction (ContractPerformaID,Lot,RawMaterialImportLocalID,SupplierID,Packaging,
                AdvanceDocumentsReceived,BAI_SPS_IC,FromBAIValidity,
                ToBAIValidity,BPI_SPS_IC,FromBPIValidity,
                ToBPIValidity,MBL,
                ShippingLineID,Vessel,HBL,Forwarder,ETD,ETA,SailingDate,ContainerTypeID,NoOfContainer,Quantity,BrokerID,
                DateDocsReceivedByBroker,OriginalDocsAvailavilityDate,BankID,DateOfPickup,Status,
                ,Remarks,UserID)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $params = array($ContractPerformaID,$Lot,$RawMaterialImportLocalID,$SupplierID,$Packaging,$AdvanceDocumentsReceived,
                $BAI_SPS_IC,$FromBAIValidity,$ToBAIValidity, $BPI_SPS_IC,$FromBPIValidity,$ToBPIValidity,$MBL,
                $ShippingLineID,$Vessel,$HBL,$Forwarder,$ETD,$ETA,$SailingDate,$ContainerTypeID,$NoOfContainer,$Quantity,$BrokerID,
                $DateDocsReceivedByBroker,$OriginalDocsAvailavilityDate,$BankID,$DateOfPickup,$Status,
                $Remarks,$UserID);
                $stmt = sqlsrv_query($conn, $sql, $params);
                // sqlsrv_commit($conn);
           
                if($stmt)
                {
                  echo 1;
                }
          } //Brace for If $Packaging = 1
          else 
          {
            //$Packaging = 2 Bulk
             $ContractPerformaID = 0;
              $Lot = 0;
              $RawMaterialImportLocalID = 0;
              $SupplierID = 0;
              $BAI_SPS_IC = "";
              $BPI_SPS_IC = "";
              $MBL = "";
              $BL = "";
              $ShippingLineID = 0;
              $Vessel = "";
              $HBL = "";
              $Forwarder = "";
              $ContainerTypeID = 0;
              $NoOfContainer = 0;
              $NoOfTruck = 0;
              $Quantity = 0;
              $BrokerID = 0;
              $BankID = 0;
              $Status = 0;   
              $LodgementBankID = 0;
              $Remarks = "";
              $UserID = "";
              $SailingDate = date('Y-m-d H:i:s');
              $FromBPIValidity = NULL;
              $FromBAIValidity = NULL;
              $ToBAIValidity = NULL;
              $ToBPIValidity = NULL;
              $AdvanceDocumentsReceived = NULL;
              $ETD = NULL;
              $ETA = NULL;
              $DateDocsReceivedByBroker = NULL;
              $OriginalDocsAvailavilityDate = NULL;
              $DateOfPickup = NULL;
              if(isset($data->FromBPIValidity))
              { 
                $FromBPIValidity=$data->FromBPIValidity;
              }
              if(isset($data->FromBAIValidity))
              { 
                $FromBAIValidity=$data->FromBAIValidity;
              }
              if(isset($data->ToBAIValidity))
              { 
                $ToBAIValidity=$data->ToBAIValidity;
              }
              if(isset($data->ToBPIValidity))
              { 
                $ToBPIValidity=$data->ToBPIValidity;
              }
              if(isset($data->AdvanceDocumentsReceived))
              { 
                $AdvanceDocumentsReceived=$data->AdvanceDocumentsReceived;
              }
              if(isset($data->ETD))
              { 
                $ETD=$data->ETD;
              }
              if(isset($data->ETA))
              { 
                $ETA=$data->ETA;
              }
              if(isset($data->DateDocsReceivedByBroker))
              { 
                $DateDocsReceivedByBroker=$data->DateDocsReceivedByBroker;
              }
              if(isset($data->OriginalDocsAvailavilityDate))
              { 
                $OriginalDocsAvailavilityDate=$data->OriginalDocsAvailavilityDate;
              }
              if(isset($data->DateOfPickup))
              { 
                $DateOfPickup=$data->DateOfPickup;
              }
              if(isset($data->ShippingTransactionID))
              { 
                $ShippingTransactionID=$data->ShippingTransactionID;
              }
              if(isset($data->ContractPerformaID))
              { 
                $ContractPerformaID=$data->ContractPerformaID;
              }
              if(isset($data->Lot))
              { 
                $Lot=$data->Lot;
              }
              if(isset($data->RawMaterialImportLocalID))
              { 
                $RawMaterialImportLocalID=$data->RawMaterialImportLocalID;
              }
              if(isset($data->SupplierID))
              { 
                $SupplierID=$data->SupplierID;
              }
              if(isset($data->BAI_SPS_IC))
              { 
                $BAI_SPS_IC=$data->BAI_SPS_IC;
              }
              if(isset($data->BPI_SPS_IC))
              { 
                $BPI_SPS_IC=$data->BPI_SPS_IC;
              }
              if(isset($data->MBL))
              { 
                $MBL=$data->MBL;
              }
              if(isset($data->BL))
              { 
                $BL=$data->BL;
              }
              if(isset($data->ShippingLineID))
              { 
                $ShippingLineID=$data->ShippingLineID;
              }
              if(isset($data->Vessel))
              { 
                $Vessel=$data->Vessel;
              }
              if(isset($data->HBL))
              { 
                $HBL=$data->HBL;
              }
              if(isset($data->Forwarder))
              { 
                $Forwarder=$data->Forwarder;
              }

              if(isset($data->NoOfContainer))
              { 
                $NoOfContainer=$data->NoOfContainer;
              }
              if(isset($data->NoOfTruck))
              { 
                $NoOfTruck=$data->NoOfTruck;
              }
              if(isset($data->Quantity))
              { 
                $Quantity=$data->Quantity;
              }
              if(isset($data->BrokerID))
              { 
                $BrokerID=$data->BrokerID;
              }
              if(isset($data->BankID))
              { 
                $BankID=$data->BankID;
              }        
              if(isset($data->Status))
              { 
                $Status=$data->Status;
              }

              if(isset($data->Remarks))
              { 
                $Remarks=$data->Remarks;
              }
              if(isset($data->UserID))
              { 
                $UserID=$data->UserID;
              }
              $sql = "INSERT INTO ShippingTransaction (ContractPerformaID ,Lot,RawMaterialImportLocalID,SupplierID,Packaging,
              AdvanceDocumentsReceived,BAI_SPS_IC,FromBAIValidity,Forwarder,MBL,HBL,
              ToBAIValidity,BPI_SPS_IC, FromBPIValidity,ToBPIValidity,BL,ShippingLineID,
              Vessel,ETD,ETA,SailingDate,NoOfTruck, Quantity,BrokerID,
              DateDocsReceivedByBroker,OriginalDocsAvailavilityDate,BankID,DateOfPickup,Status,
              Remarks,UserID)
              VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
              $params = array($ContractPerformaID,$Lot,$RawMaterialImportLocalID,$SupplierID,$Packaging,$AdvanceDocumentsReceived,$BAI_SPS_IC,$FromBAIValidity,
              $Forwarder,$MBL,$HBL,
              $ToBAIValidity,
              $BPI_SPS_IC,$FromBPIValidity,$ToBPIValidity,$BL,$ShippingLineID,$Vessel,$ETD,$ETA,$SailingDate,$NoOfTruck,$Quantity,$BrokerID,
              $DateDocsReceivedByBroker,
              $OriginalDocsAvailavilityDate,$BankID,$DateOfPickup,$Status,
              $Remarks,$UserID);
              $stmt = sqlsrv_query($conn, $sql, $params);
              // var_dump($stmt);
              if($stmt)
              {
                echo 1;
              }
            
          } //Else for IF $Packaging = 1
    }//IF ShippingTransactionID = 0
    else
    {
            $Packaging = 0;
            if(isset($data->Packaging))
            { 
              $Packaging=$data->Packaging;
            }
                if($Packaging == 1) // Packaging = 1 is Containerized
                {
              $ContractPerformaID = 0;
                $Lot = 0;
                $RawMaterialImportLocalID = 0;
                $SupplierID = 0;
                $BAI_SPS_IC = "";
                $BPI_SPS_IC = "";
                $MBL = "";
                $HBL = "";
                $BL = "";
                $ShippingLineID = 0;
                $Vessel = "";
                $Forwarder = "";
                $ContainerTypeID = 0;
                $NoOfContainer = 0;
                $Quantity = 0;
                $BrokerID = 0;
                $BankID = 0;
                $NoOfTruck = 0;
                $Status = 0;   
                $LodgementBankID = 0;
                $Remarks = "";
                $UserID = "";
                $LandedDate = date('Y-m-d H:i:s');
                // Convert empty or invalid date strings to SQL NULL
               $FromBPIValidity = NULL;
              $FromBAIValidity = NULL;
              $ToBAIValidity = NULL;
              $ToBPIValidity = NULL;
              $AdvanceDocumentsReceived = NULL;
              $ETD = NULL;
              $ETA = NULL;
              $DateDocsReceivedByBroker = NULL;
              $OriginalDocsAvailavilityDate = NULL;
              $DateOfPickup = NULL;

              if(isset($data->FromBPIValidity))
              { 
                $FromBPIValidity=$data->FromBPIValidity;
              }
              if(isset($data->FromBAIValidity))
              { 
                $FromBAIValidity=$data->FromBAIValidity;
              }
              if(isset($data->ToBAIValidity))
              { 
                $ToBAIValidity=$data->ToBAIValidity;
              }
              if(isset($data->ToBPIValidity))
              { 
                $ToBPIValidity=$data->ToBPIValidity;
              }
              if(isset($data->AdvanceDocumentsReceived))
              { 
                $AdvanceDocumentsReceived=$data->AdvanceDocumentsReceived;
              }
              if(isset($data->ETD))
              { 
                $ETD=$data->ETD;
              }
              if(isset($data->ETA))
              { 
                $ETA=$data->ETA;
              }
              if(isset($data->DateDocsReceivedByBroker))
              { 
                $DateDocsReceivedByBroker=$data->DateDocsReceivedByBroker;
              }
              if(isset($data->OriginalDocsAvailavilityDate))
              { 
                $OriginalDocsAvailavilityDate=$data->OriginalDocsAvailavilityDate;
              }
              if(isset($data->DateOfPickup))
              { 
                $DateOfPickup=$data->DateOfPickup;
              }

                  if(isset($data->ShippingTransactionID)) { 
                      $ShippingTransactionID = $data->ShippingTransactionID;
                  }
                  if(isset($data->ContractPerformaID)) { 
                      $ContractPerformaID = $data->ContractPerformaID;
                  }
                  if(isset($data->Lot)) { 
                      $Lot = $data->Lot;
                  }
                  if(isset($data->RawMaterialImportLocalID)) { 
                      $RawMaterialImportLocalID = $data->RawMaterialImportLocalID;
                  }
                  if(isset($data->SupplierID)) { 
                      $SupplierID = $data->SupplierID;
                  }
                  if(isset($data->BAI_SPS_IC)) { 
                      $BAI_SPS_IC = $data->BAI_SPS_IC;
                  }
                  if(isset($data->BPI_SPS_IC)) { 
                      $BPI_SPS_IC = $data->BPI_SPS_IC;
                  }
                  if(isset($data->MBL)) { 
                      $MBL = $data->MBL;
                  }
                  if(isset($data->BL)) { 
                      $BL = $data->BL;
                  }
                  if(isset($data->ShippingLineID)) { 
                      $ShippingLineID = $data->ShippingLineID;
                  }
                  if(isset($data->Vessel)) { 
                      $Vessel = $data->Vessel;
                  }
                  if(isset($data->HBL)) { 
                      $HBL = $data->HBL;
                  }
                  if(isset($data->Forwarder)) { 
                      $Forwarder = $data->Forwarder;
                  }
                  if(isset($data->ContainerTypeID)) { 
                      $ContainerTypeID = $data->ContainerTypeID;
                  }
                  if(isset($data->NoOfTruck)) { 
                      $NoOfTruck = $data->NoOfTruck;
                  }
                  if(isset($data->NoOfContainer)) { 
                      $NoOfContainer = $data->NoOfContainer;
                  }
                  if(isset($data->Quantity)) { 
                      $Quantity = $data->Quantity;
                  }
                  if(isset($data->BrokerID)) { 
                      $BrokerID = $data->BrokerID;
                  }
                  if(isset($data->BankID)) { 
                      $BankID = $data->BankID;
                  }        
                  if(isset($data->Status)) { 
                      $Status = $data->Status;
                  }

                  if(isset($data->Remarks)) {
                      $Remarks = $data->Remarks;
                  }
                  if(isset($data->UserID)) { 
                      $UserID = $data->UserID;
                  }

                  $sql = "UPDATE ShippingTransaction SET ContractPerformaID = ?, Lot = ?, RawMaterialImportLocalID = ?, SupplierID = ?,
                  Packaging = ?, AdvanceDocumentsReceived = ?, BAI_SPS_IC = ?, FromBAIValidity = ?, ToBAIValidity = ?,BPI_SPS_IC = ?,
                  FromBPIValidity = ?, ToBPIValidity = ?,ContainerTypeID = ?,
                  MBL = ?,BL = ?, ShippingLineID = ?, 
                  Vessel = ?,HBL = ?, Forwarder = ?, ETD = ?, ETA = ?,  NoOfTruck = ?,NoOfContainer = ?,
                  Quantity = ?, BrokerID = ?, DateDocsReceivedByBroker = ?, 
                  OriginalDocsAvailavilityDate = ?, BankID = ?, DateOfPickup = ?,
                  Status = ?,
                  Remarks = ?, UserID = ? WHERE ShippingTransactionID = ?";
                  $params = array($ContractPerformaID,$Lot,$RawMaterialImportLocalID,$SupplierID,$Packaging,$AdvanceDocumentsReceived,$BAI_SPS_IC,
                  $FromBAIValidity,
                  $ToBAIValidity, 
                  $BPI_SPS_IC,$FromBPIValidity,
                  $ToBPIValidity,$ContainerTypeID,$MBL,$BL,
                  $ShippingLineID,$Vessel,
                  $HBL,$Forwarder,$ETD,$ETA,$NoOfTruck,$NoOfContainer,$Quantity,$BrokerID,
                  $DateDocsReceivedByBroker,$OriginalDocsAvailavilityDate,$BankID,$DateOfPickup,
                  $Status,
                 
                  $Remarks,$UserID, $ShippingTransactionID);
                  $stmt = sqlsrv_query($conn, $sql, $params);
                  // sqlsrv_commit($conn);
                  echo 2;
                  // var_dump($Quantity);
                  // var_dump($stmt);
  
              } //Brace for If $Packaging = 1
              else //Elseee for UPDATE
              {
              // Helper function
              $ContractPerformaID = 0;
              $Lot = 0;
              $RawMaterialImportLocalID = 0;
              $SupplierID = 0;
              $BAI_SPS_IC = "";
              $BPI_SPS_IC = "";
              $MBL = "";
              $BL = "";
              $HBL = "";
              $ShippingLineID = 0;
              $Vessel = "";
              $Forwarder = "";
              $ContainerTypeID = 0;
              $NoOfContainer = 0;
              $Quantity = 0;
              $BrokerID = 0;
              $BankID = 0;
              $NoOfTruck = 0;
              $Status = 0;   
              $LodgementBankID = 0;
              $Remarks = "";
              $UserID = "";
              $LandedDate = date('Y-m-d H:i:s'); // current timestamp
               $FromBPIValidity = NULL;
              $FromBAIValidity = NULL;
              $ToBAIValidity = NULL;
              $ToBPIValidity = NULL;
              $AdvanceDocumentsReceived = NULL;
              $ETD = NULL;
              $ETA = NULL;
              $DateDocsReceivedByBroker = NULL;
              $OriginalDocsAvailavilityDate = NULL;
              $DateOfPickup = NULL;
                if(isset($data->FromBPIValidity))
                  { 
                    $FromBPIValidity=$data->FromBPIValidity;
                  }
                  if(isset($data->FromBAIValidity))
                  { 
                    $FromBAIValidity=$data->FromBAIValidity;
                  }
                  if(isset($data->ToBAIValidity))
                  { 
                    $ToBAIValidity=$data->ToBAIValidity;
                  }
                  if(isset($data->ToBPIValidity))
                  { 
                    $ToBPIValidity=$data->ToBPIValidity;
                  }
                  if(isset($data->AdvanceDocumentsReceived))
                  { 
                    $AdvanceDocumentsReceived=$data->AdvanceDocumentsReceived;
                  }
                  if(isset($data->ETD))
                  { 
                    $ETD=$data->ETD;
                  }
                  if(isset($data->ETA))
                  { 
                    $ETA=$data->ETA;
                  }
                  if(isset($data->DateDocsReceivedByBroker))
                  { 
                    $DateDocsReceivedByBroker=$data->DateDocsReceivedByBroker;
                  }
                  if(isset($data->OriginalDocsAvailavilityDate))
                  { 
                    $OriginalDocsAvailavilityDate=$data->OriginalDocsAvailavilityDate;
                  }
                  if(isset($data->DateOfPickup))
                  { 
                    $DateOfPickup=$data->DateOfPickup;
                  }
                  if(isset($data->ShippingTransactionID)) { 
                      $ShippingTransactionID = $data->ShippingTransactionID;
                  }
                  if(isset($data->ContractPerformaID)) { 
                      $ContractPerformaID = $data->ContractPerformaID;
                  }
                  if(isset($data->Lot)) { 
                      $Lot = $data->Lot;
                  }
                  if(isset($data->RawMaterialImportLocalID)) { 
                      $RawMaterialImportLocalID = $data->RawMaterialImportLocalID;
                  }
                  if(isset($data->SupplierID)) { 
                      $SupplierID = $data->SupplierID;
                  }
                  if(isset($data->BAI_SPS_IC)) { 
                      $BAI_SPS_IC = $data->BAI_SPS_IC;
                  }
                  if(isset($data->BPI_SPS_IC)) { 
                      $BPI_SPS_IC = $data->BPI_SPS_IC;
                  }
                  if(isset($data->MBL)) { 
                      $MBL = $data->MBL;
                  }
                  if(isset($data->BL)) { 
                      $BL = $data->BL;
                  }
                  if(isset($data->ShippingLineID)) { 
                      $ShippingLineID = $data->ShippingLineID;
                  }
                  if(isset($data->Vessel)) { 
                      $Vessel = $data->Vessel;
                  }
                  if(isset($data->HBL)) { 
                      $HBL = $data->HBL;
                  }
                  if(isset($data->Forwarder)) { 
                      $Forwarder = $data->Forwarder;
                  }
                  if(isset($data->ContainerTypeID)) { 
                      $ContainerTypeID = $data->ContainerTypeID;
                  }
                  if(isset($data->NoOfTruck)) { 
                      $NoOfTruck = $data->NoOfTruck;
                  }
                  if(isset($data->NoOfContainer)) { 
                      $NoOfContainer = $data->NoOfContainer;
                  }
                  if(isset($data->Quantity)) { 
                      $Quantity = $data->Quantity;
                  }
                  if(isset($data->BrokerID)) { 
                      $BrokerID = $data->BrokerID;
                  }
                  if(isset($data->BankID)) { 
                      $BankID = $data->BankID;
                  }        
                  if(isset($data->Status)) { 
                      $Status = $data->Status;
                  }

                  if(isset($data->Remarks)) {
                      $Remarks = $data->Remarks;
                  }
                  if(isset($data->UserID)) { 
                      $UserID = $data->UserID;
                  }

                  $sql = "UPDATE ShippingTransaction SET ContractPerformaID = ?, Lot = ?, RawMaterialImportLocalID = ?, SupplierID = ?,
                  Packaging = ?, AdvanceDocumentsReceived = ?, BAI_SPS_IC = ?, FromBAIValidity = ?, ToBAIValidity = ?,BPI_SPS_IC = ?,
                  FromBPIValidity = ?, ToBPIValidity = ?,ContainerTypeID =?,
                  MBL = ?,BL = ?, ShippingLineID = ?, 
                  Vessel = ?,HBL = ?, Forwarder = ?, ETD = ?, ETA = ?,   NoOfTruck = ?,NoOfContainer = ?,
                  Quantity = ?, BrokerID = ?, DateDocsReceivedByBroker = ?, 
                  OriginalDocsAvailavilityDate = ?, BankID = ?, DateOfPickup = ?, 
                  Status = ?,
                  Remarks = ?, UserID = ? WHERE ShippingTransactionID = ?";
                  $params = array($ContractPerformaID,$Lot,$RawMaterialImportLocalID,$SupplierID,$Packaging,$AdvanceDocumentsReceived,$BAI_SPS_IC,
                  $FromBAIValidity,
                  $ToBAIValidity, 
                  $BPI_SPS_IC,$FromBPIValidity,
                  $ToBPIValidity,$ContainerTypeID,$MBL,$BL,
                  $ShippingLineID,$Vessel,
                  $HBL,$Forwarder,$ETD,$ETA,$NoOfTruck, $NoOfContainer, $Quantity,$BrokerID,
                  $DateDocsReceivedByBroker,$OriginalDocsAvailavilityDate,$BankID,$DateOfPickup,
                  $Status,
                  $Remarks,$UserID, $ShippingTransactionID);
                  $stmt = sqlsrv_query($conn, $sql, $params);
                  // sqlsrv_commit($conn);
                  echo 2;
                  // var_dump($Quantity);
                  // var_dump($stmt);


              }
    }
            if($ShippingTransactionID == 0)
      {

            $SystemLogID = 0;
            $FunctionID = 1;
            $TableName = "ShippingTransaction";
            $Activity = 'Insert ShippingTransaction :'.  ($MBL != "" ? $MBL : $BL);
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
      else
      {
        
            $SystemLogID = 0;
            $FunctionID = 2;
            $TableName = "ShippingTransaction";
            $Activity = 'Update ShippingTransaction :'. ($MBL != "" ? $MBL : $BL);
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
   
         } sqlsrv_commit($conn);
    }
    catch(Exception $e)
    {
    sqlsrv_rollback($conn);

    header('Content-Type: application/json; charset=utf-8');
    echo 0;
    }
        
    
     
    

?>