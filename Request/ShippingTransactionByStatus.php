<?php 

require_once '../Database/connection.php';
if (sqlsrv_begin_transaction($conn) === false) {
  die( print_r( sqlsrv_errors(), true ));
}
    //ShippingTransaction ID
    $Status=0;
    if(isset($_GET['Status']))
    {
    $Status= $_GET['Status'];
    }

      $sql = "SELECT 
   st.ShippingTransactionID
  ,cp.ContractPerformaID
  ,cp.ContractNo
  ,cp.CountryOfOrigin
  ,st.Lot
  ,bk.BankID
  ,bk.Bank
  ,bkl.BankID
  ,bkl.Bank AS LodgementBank
  ,st.LodgementBankID
  ,cp.RawMaterialImportLocalID
  ,rl.RawMaterial
 ,cp.Packaging
  ,cp.SupplierAddress
  ,ct.Container
  ,ct.ContainerTypeID
  ,st.NoOfContainer
  ,s.Supplier
  ,cp.SupplierID
  ,st.AdvanceDocumentsReceived
  ,b.Broker
  ,st.BAI_SPS_IC
  ,st.FromBAIValidity
  ,st.ToBAIValidity
  ,st.BPI_SPS_IC
  ,st.FromBPIValidity
  ,st.ToBPIValidity
  ,st.MBL
  ,st.BL
  ,sl.ShippingLineID
  ,sl.ShippingLine
  ,st.Vessel
  ,st.HBL
  ,st.Forwarder
  ,st.ETD
  ,st.ETA
  ,st.ATA
  ,st.NoOfTruck
  ,st.Quantity
  ,st.BrokerID
  ,st.DateDocsReceivedByBroker
  ,st.OriginalDocsAvailavilityDate
  ,st.BankID
  ,st.DateOfPickup
  ,pd.PortOfDischarge
  ,cp.PortOfDischargeID
  ,st.Status
   ,st.COAStatus
  ,st.DateofDischarge
  ,st.LodgementDate
  ,st.LodgementBankID
  ,st.GatepassRecieved
  ,st.AcknowledgeByLogistics
  ,st.StorageLastFreeDate
  ,st.DemurrageDate
  ,st.DetentionDate
  ,st.Remarks
  ,st.UserID
  FROM ShippingTransaction st
      LEFT JOIN ShippingLine sl ON st.ShippingLineID = sl.ShippingLineID
      LEFT JOIN ContainerType ct ON st.ContainerTypeID = ct.ContainerTypeID
      LEFT JOIN ContractPerforma cp ON st.ContractPerformaID = cp.ContractPerformaID
         LEFT JOIN Supplier s ON cp.SupplierID = s.SupplierID
		       LEFT JOIN RawMaterialImportLocal rl ON cp.RawMaterialImportLocalID = rl.RawMaterialImportLocalID
      LEFT JOIN Bank bk ON st.BankID = bk.BankID
      LEFT JOIN Bank bkl ON st.LodgementBankID = bkl.BankID
      LEFT JOIN Broker b ON st.BrokerID = b.BrokerID
        LEFT JOIN PortOfDischarge pd ON cp.PortOfDischargeID = pd.PortOfDischargeID
  WHERE  st.Status = ?";
        $params = array($Status);     
        $stmt1 = sqlsrv_query($conn,$sql,$params); 
        if($stmt1)
        {
          $json = array();
          do {
            while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
            $json[] = $row;     	
            }
          } while (sqlsrv_next_result($stmt1));
        
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode($json);
        }
        else
        {
        sqlsrv_rollback($conn);
        echo "Rollback";
        }
?>