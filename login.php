<?php 
    // header('Access-Control-Allow-Origin:*');
    // header('Access-Control-Allow-Method:POST');
    // header('Content-Type:application/json');
    require 'vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    require 'database/connection.php';
    $data = json_decode(file_get_contents('php://input'));
    if(isset($data))
    { 
     $UName = $data->UName;
     $PWord = $data->PWord;
     $decrypt = md5($PWord);
          $sql = "SELECT
                 ua.UserID
                ,ua.AvatarUrl
                ,ua.UName
                ,ua.PWord
                ,ua.ULevel
                ,ua.Name
                ,ua.EmailAdd 
                ,m.ModuleName
                FROM UserAccount ua 
                LEFT JOIN Module m ON ua.ULevel = m.ModuleID
                WHERE ua.UName = ? AND ua.PWord = ?";
          $params = array($UName, $decrypt);
          $stmt = sqlsrv_query($conn, $sql, $params);
          $user = "";          
          $pass = "";
          $user_id = "";
          $ULevel = "";
          $ModuleName = "";
          $AvatarUrl = 0;
          while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
          $user = $row['UName'];
          $pass = $row['PWord'];
          $user_id = $row['UserID'];
          $ULevel = $row['ULevel'];
          $ModuleName = $row['ModuleName'];
          $AvatarUrl = $row['AvatarUrl'];
          }      
            if($user == $UName && $pass == $decrypt)
            {    
              $sql ="SELECT UserID, ULevel FROM UserAccount WHERE UserID = ? AND UName = ? "; // to get user_id that equals to username
              $params = array($user_id,$user);
              $stmt = sqlsrv_query($conn,$sql,$params);
              while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
              {
                $ULevel = $row['ULevel'];
              }
              $AccessDetails = "";
              $UserID = "";
                 $sqlAccess = "SELECT a.AccessRight, ma.AccessName FROM Access a
                 LEFT JOIN ModuleAccess ma ON a.AccessRight = ma.AccessRight 
                 WHERE a.UserID = ? ORDER BY AccessRight ASC";
                  $paramsAccess = array($user_id);
                  $stmt2 = sqlsrv_query($conn, $sqlAccess, $paramsAccess);
                  $AccessDetails = array();
                  if ($stmt2) {
                      while ($AccessRow = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
                          $AccessDetails[] = $AccessRow;
                      }
                  }
                  $sqls = "SELECT WarehouseLocationID,DateRotation,TypeID,PlantID FROM CheckerSchedule WHERE UserID = ?";
                  $params = array($user_id);
                  $stmts = sqlsrv_query($conn, $sqls,$params);
                  $LocationID = "";
                  $DateRotation = "";
                  $TypeID = "";
                  $PlantID = "";
                  while($AccountsRow = sqlsrv_fetch_array($stmts, SQLSRV_FETCH_ASSOC))
                  {
                    $LocationID = $AccountsRow['WarehouseLocationID'];
                    $DateRotation = $AccountsRow['DateRotation'];
                    $TypeID= $AccountsRow['TypeID'];
                    $PlantID = $AccountsRow['PlantID'];
                  }
                header('Content-Type: application/json; charset=utf-8');
                $issuedAt = time();
                $expiresIn = $issuedAt + 60 * 300; //1hr
                $sec_key= 'FeedmixKaibiganKo';
                $payload = array(
                'isd'=>'localhost',
                'username' => $UName,
                'UserID'   => $user_id, 
                'usl'=> $ULevel,
                'aud'=> 'localhost',
                'iat' => $issuedAt,
                'exp'=> $expiresIn, //1hr 
                );    

                $encode = JWT :: encode($payload, $sec_key, 'HS256');
                echo json_encode([  
                'data' => [
                'user_id' => $user_id,
                'WarehouseLocationID' => $LocationID,
                'DateRotation' => $DateRotation,
                'username' => $UName,
                'password' => $decrypt, 
                'ModulenName' => $ModuleName,
                'Avatar' => $AvatarUrl,
                'token' => $encode,
                'usl'=> $ULevel,
                'PlantID' => $PlantID, 
                'expiresIn' => 60 * 300,
                'Category' => $TypeID,
                'AccessDetail' => $getAccess['AccessDetail'] = $AccessDetails
                ],  
                ]); 
                  
        
              $SystemLogID = 0;
              $FunctionID = 5; 
              $TableName = "Login";
              $Activity = "Login User";
              $UpdatedData = "0";
              $sql1 = "EXEC [dbo].[SystemLogInsert]
                          @SystemLogID = ?,
                          @UserID = ?,
                          @FunctionID = ?,
                          @TableName = ?,
                          @Activity = ?,  
                          @UpdatedData = ?";
                $paramss = array($SystemLogID,$user_id,$FunctionID,$TableName,$Activity,$UpdatedData);         
                $stmt = sqlsrv_query($conn, $sql1, $paramss);
                // var_dump($stmt);
          
            sqlsrv_commit($conn);   
                    
                // $sql1 = "EXEC [dbo].[FinishProdInsertDailyInventorys]";
                // $stmt1 = sqlsrv_query($conn, $sql1);
                // $sql2 = "EXEC [dbo].[RawMatInsertDailyInventorys]";
                // $stmt2 = sqlsrv_query($conn, $sql2);
                // $sql3 = "EXEC	[dbo].[WarehouseDailyInventorys]";
                // $stmt3 = sqlsrv_query($conn, $sql3);
                // $sql4 = "EXEC	[dbo].[WarehouseRmDailyInventorys]";
                // // $stmt4 = sqlsrv_query($conn, $sql4); 
                // $sql5 = "EXEC	[dbo].[WarehousePartitionRmDailyInventorys]";
                // $stmt5 = sqlsrv_query($conn, $sql5); 
                // $sql6 = "EXEC [dbo].[WarehouseLocationRmDailyInventorys]";
                // $stmt6 = sqlsrv_query($conn, $sql6);  
            }
            else
            {
              echo json_encode([
              'status' => -1,
              'message' => 'Invalid Email or Password',
            ]);        
            } 
    }
  

?>