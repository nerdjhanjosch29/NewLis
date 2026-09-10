<?php 
require '../Database/connection.php ';
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
      $ShippingTransactionID=$data->ShippingTransactionID;
    }
        $array=$data->PullOutDetail;
        $length=count($array); // count array
        $sql = "UPDATE PullOut SET deleted = 1 WHERE ShippingTransactionID = ?";
        $params3 = array($ShippingTransactionID);
        $stmt3 = sqlsrv_query($conn, $sql, $params3);
        sqlsrv_commit($conn);
        if($length !== 0)
        {
            for($i=0; $i<=$length-1; $i++)
            { 
              $deleted = 0;
              $PullOutID = 0;
              $ContainerNumber = 0;         
              $TruckingID = 0;
              $Remarks = "";
              $DateOfDischarge=NULL;
              $Storage=NULL;
              $Demurrage=NULL;
              $Detention=NULL;
              $DateIn=NULL;
              $DateOut=NULL;
              $ReturnDate=NULL;
              $PullOutDate=NULL;
              $TruckingID=0;
              
                if($array[$i]->ContainerNumber)
                {
                  $ContainerNumber=$array[$i]->ContainerNumber;
                  
                }
                if($array[$i]->PullOutID)
                {
                  $PullOutID=$array[$i]->PullOutID;
                }
                if($array[$i]->DateOfDischarge)
                {
                $DateOfDischarge = $array[$i]->DateOfDischarge;
                }
                if($array[$i]->Storage)
                {
                  $Storage = $array[$i]->Storage;
                }
                if($array[$i]->Demurrage)
                {
                  $Demurrage=$array[$i]->Demurrage;
                }
                if($array[$i]->Detention)
                {
                  $Detention = $array[$i]->Detention;
                }
                if($array[$i]->DateIn)
                {
                  $DateIn = $array[$i]->DateIn;
                }
                if($array[$i]->DateOut)
                {
                  $DateOut = $array[$i]->DateOut;
                }
                if($array[$i]->ReturnDate)
                {
                  $ReturnDate = $array[$i]->ReturnDate;
                }
                if($array[$i]->PullOutDate)
                {
                  $PullOutDate = $array[$i]->PullOutDate;
                }
                if($array[$i]->TruckingID)
                {
                  $TruckingID = $array[$i]->TruckingID;
                }

                if($array[$i]->Remarks)
                {
                  $Remarks=$array[$i]->Remarks;
                }
                if($PullOutID == 0)
                {
                  // Check if ContainerNumber already exists for this MBL
                      $sqlCheck = "SELECT PullOutID
                                  FROM PullOut
                                  WHERE ShippingTransactionID = ?
                                  AND ContainerNumber = ?";
                              $paramsCheck = array($ShippingTransactionID,$ContainerNumber);
                              $stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);
                              if(sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC))
                              {
                                  echo 0;
                                  sqlsrv_rollback($conn);
                                  exit;
                              }
                              $sql="INSERT INTO PullOut (ShippingTransactionID,DateOfDischarge,ContainerNumber,Storage,Demurrage,
                              Detention,PullOutDate,DateIn,DateOut,ReturnDate,TruckingID,
                              deleted,Remarks,UserID)
                              VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                              $params = array($ShippingTransactionID,$DateOfDischarge,$ContainerNumber,$Storage, $Demurrage, $Detention,
                              $PullOutDate,$DateIn,$DateOut,$ReturnDate,$TruckingID,$deleted,$Remarks,$UserID);
                              $stmt1 = sqlsrv_query($conn,$sql,$params);
                          
                    }
                    else
                    {
                    $sql = "UPDATE PullOut SET DateOfDischarge = ?, ContainerNumber = ?, Storage = ?,Demurrage =?, Detention = ?, 
                    PullOutDate = ?, DateIn = ?, DateOut = ?, ReturnDate = ?, 
                    TruckingID = ?, deleted = ?, Remarks = ?, UserID = ? WHERE PullOutID = ?";
                    $params = array($DateOfDischarge, $ContainerNumber,$Storage, $Demurrage, $Detention, $PullOutDate, $DateIn, $DateOut, $ReturnDate,
                    $TruckingID, $deleted,$Remarks, $UserID, $PullOutID);
                    $stmt2 = sqlsrv_query($conn, $sql, $params);
                    sqlsrv_commit($conn);      
                    }  
              }
              if($PullOutID == 0)
              {
                echo 1;
                $SystemLogID = 0;
                $FunctionID = 1; 
                $TableName = "PullOut";
                $Activity = "Insert PullOut : $ContainerNumber";
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
              else
              {
                echo 2;
                $SystemLogID = 0;
                $FunctionID = 2; 
                $TableName = "PullOut";
                $Activity = "Update PullOut : $ContainerNumber";
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
        }
        else
        {
          echo 2;
        }
  }sqlsrv_commit($conn);
}

catch(Exception $e)
{
                      echo 0;
                    sqlsrv_rollback($conn);
                    exit;
}

?>