<?php

require_once '../Database/connection.php';
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


                $BinloadingRequestID = 0;
                $PlantID = 0;
                $DriverID = 0;
                $TruckID = 0;
                $RequestDate = "";
                $RawMaterialID = 0;
                $Quantity = 0;
                $BinloadUomID = 0;
                $Status = 0;
                $ControlNo = "";

                if($data->BinloadingRequestID)
                {
                    $BinloadingRequestID = $data->BinloadingRequestID;
                }
                if($data->PlantID)
                {
                    $PlantID = $data->PlantID;
                }
                if($data->DriverID)
                {
                    $DriverID = $data->DriverID;
                }
                if($data->TruckID)
                {
                    $TruckID = $data->TruckID;
                }
                if($data->ControlNo)
                {
                    $ControlNo = $data->ControlNo;
                }
                if($data->RequestDate)
                {
                    $RequestDate = $data->RequestDate;
                }
                if($data->RawMaterialID)
                {
                    $RawMaterialID = $data->RawMaterialID;
                }
                if($data->Quantity)
                {
                    $Quantity = $data->Quantity;
                }
                if($data->BinloadUomID)
                {
                    $BinloadUomID = $data->BinloadUomID;
                }

                if($BinloadingRequestID == 0)
                {   
                    $newID = 0;
                    $sql = "INSERT INTO BinloadingRequest (PlantID,DriverID,TruckID,RequestDate,RawMaterialID,ControlNo,
                    Quantity,BinloadUomID,Status,UserID) VALUES(?,?,?,?,?,?,?,?,?,?)";
                    $params = array($PlantID,$DriverID,$TruckID,$RequestDate,$RawMaterialID,$ControlNo,$Quantity,$BinloadUomID,$Status,$UserID);
                    $stmt = sqlsrv_query($conn,$sql,$params);
                    sqlsrv_next_result($stmt); 
                    sqlsrv_fetch($stmt); 
                    $newID = sqlsrv_get_field($stmt, 0);
                    if($stmt)
                    {
                            echo 1;
                    }
                }



                $array = $data->BinloadingDetail;
                $length = count($array);
                for($i=0; $i<$length; $i++)
                {
                    $BinloadingID = 0;
                    $WarehousePartitionStockID = 0;
                    $IntakeID = 0;
                    $BinloadingDate = "";
                    $BinloadingDateTime = "";
                    $WarehouseID = 0;
                    $RawMaterialID = 0;
                    $Quantity = 0;
                    $Weight = 0;
                    $Status = 0;
                    
                    if($array[$i]->BinloadingID)
                    {
                        $BinloadingID = $array[$i]->BinloadingID;
                    }
                    if($array[$i]->WarehousePartitionStockID)
                    {
                        $WarehousePartitionStockID = $array[$i]->WarehousePartitionStockID;
                        $StockQuantity = 0;
                        $StockWeight = 0;
                        $BinloadWeight = 0;
                        $sql = "SELECT EndingQuantity, EndingWeight,BinloadWeight FROM WarehousePartitionStock WHERE WarehousePartitionStockID = ?";
                        $params = array($WarehousePartitionStockID);
                        $stmt = sqlsrv_query($conn, $sql, $params);
                        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
                        {
                            $StockQuantity = $row['EndingQuantity'];
                            $StockWeight = $row['EndingWeight'];
                            $BinloadWeight = $row['BinloadWeight'];
                        }
                    }
                        if($array[$i]->IntakeID)
                    {
                        $IntakeID = $array[$i]->IntakeID;
                    }
                        if($array[$i]->BinloadingDate)
                    {
                        $BinloadingDate = $array[$i]->BinloadingDate;
                    }
                        if($array[$i]->BinloadingDateTime)
                    {
                        $BinloadingDateTime = $array[$i]->BinloadingDateTime;
                    }
                        if($array[$i]->WarehouseID)
                    {
                        $WarehouseID = $array[$i]->WarehouseID;
                    }
                        if($array[$i]->RawMaterialID)
                    {
                        $RawMaterialID = $array[$i]->RawMaterialID;
                    }
                        if($array[$i]->Quantity)
                    {
                        $Quantity = $array[$i]->Quantity;
                    }
                        if($array[$i]->Weight)
                    {
                        $Weight = $array[$i]->Weight;
                    }
                    if($BinloadingID == 0)
                    {
                        $FinalQuantity = $StockQuantity - $Quantity;
                        $FinalWeight = $StockWeight - $Weight;
                        $FinalBinloadWeight = $BinloadWeight + $Quantity;

                        $sql="INSERT INTO Binloading(BinloadingRequestID,WarehousePartitionStockID,PlantID,IntakeID,BinloadingDate,
                        BinloadingDateTime,RawMaterialID,Quantity,Weight)VALUES(?,?,?,?,?,?,?,?,?)";
                        $params = array($newID,$WarehousePartitionStockID,$PlantID,$IntakeID,$BinloadingDate,$BinloadingDateTime,$RawMaterialID, $Quantity,$Weight);
                        $stmt = sqlsrv_query($conn,$sql,$params);
                    
                            
                            if($stmt)
                            {
                                //WarehouseStocking
                                $sql = "UPDATE WarehousePartitionStock SET EndingQuantity = ?, EndingWeight = ?, BinloadWeight = ?
                                        WHERE WarehousePartitionStockID = ?";
                                $params = array($FinalQuantity,$FinalWeight,$FinalBinloadWeight,$WarehousePartitionStockID);
                                $stmt = sqlsrv_query($conn, $sql,$params);
                                //WarehouseInventories
                                $WarehouseQuantity = 0;
                                $WarehouseWeight = 0;
                                $sql = "SELECT Quantity, Weight FROM WarehouseInventory WHERE WarehouseID = ? AND RawMaterialID = ?";
                                $params = array($WarehouseID,$RawMaterialID);
                                $stmt = sqlsrv_query($conn,$sql,$params);
                                while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
                                {
                                    $WarehouseQuantity = $row['Quantity'];
                                    $WarehouseWeight = $row['Weight'];
                                }
                                $TotalWarehouseQuantity = $WarehouseQuantity - $Quantity;
                                $TotalWarehouseWeight = $WarehouseWeight - $Weight;
                                if($Quantity > $WarehouseQuantity || $Weight > $WarehouseWeight)
                                {
                                    echo 0;
                                }
                                else
                                {
                                    $sql = "UPDATE WarehouseInventory SET Quantity = ?, Weight = ?
                                    WHERE WarehouseID = ? AND RawMaterialID = ?";
                                    $params = array($TotalWarehouseQuantity,$TotalWarehouseWeight, $WarehouseID, $RawMaterialID);
                                    $stmt = sqlsrv_query($conn, $sql,$params);
                                }  
                            }

                    }

                }
 }



?>