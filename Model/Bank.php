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
    $Bank = "";
    $BankID = 0;
    $BankName = "";

    if($data->BankID) 
    {
    $BankID = $data->BankID;
    }
   
    if($data->Bank) 
    {
        $Bank = $data->Bank;
    }
    if($data->BankName) 
    {
        $BankName = $data->BankName;
    }

    $sql = "EXEC [dbo].[Banks]
            @BankID = ?,
            @Bank = ?,
            @BankName = ?,
            @UserID = ?";

    $params = array($BankID,$Bank,$BankName,$UserID);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if($stmt === false)
      throw new Exception(print_r(sqlsrv_errors(), true));

    $result = 0;

    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
    {
      $result = $row['result'];
    }

    if(sqlsrv_rollback($conn))
    {
        echo "rollback";
    }
    else
    {
        // =========================
    // EXISTING RECORD
    // =========================
    if($result == 0)
    {
      sqlsrv_rollback($conn);

      $json = array(
        "result" => $result,
        "message" => "Already exist"
      );

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
      exit;
    }

    // =========================
    // UPDATE
    // =========================
    else if($result == 2)
    {
      $sql = "SELECT b.BankID, b.Bank, b.BankName
              FROM Bank b
              WHERE b.isDel = 'False' AND b.BankID = ?";

      $params = array($BankID);
      $stmt1 = sqlsrv_query($conn,$sql,$params);

      if($stmt1 === false)
        throw new Exception(print_r(sqlsrv_errors(), true));

      $dataRow = null;

      while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
        $dataRow = $row;
      }

      $json = array(
        "message" => "Successfully updated.",
        "result" => $result,
        "data" => $dataRow
      );

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // =========================
    // INSERT
    // =========================
    else
    {
      $sql = "SELECT TOP(1) b.BankID, b.Bank, b.BankName
              FROM Bank b
              WHERE b.isDel = 'False'
              ORDER BY b.BankID DESC";
      $stmt1 = sqlsrv_query($conn,$sql);
      if($stmt1 === false)
        throw new Exception(print_r(sqlsrv_errors(), true));

      $dataRow = null;

      while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
        $dataRow = $row;
      }

      $json = array(
        "message" => "Successfully added.",
        "result" => $result,
        "data" => $dataRow
      );

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // =========================
    // SYSTEM LOG (ONLY IF SUCCESS)
    // =========================
    if($result != 0)
    {
      $SystemLogID = 0;
      $FunctionID = 1; 
      $TableName = "Bank";
      $Activity = "Insert Bank : $Bank";
      $UpdatedData = "0";

      $sql1 = "EXEC [dbo].[SystemLogs]
              @SystemLogID = ?,
              @UserID = ?,
              @FunctionID = ?,
              @TableName = ?,
              @Activity = ?,
              @UpdatedData = ?";

      $paramss = array($SystemLogID,$UserID,$FunctionID,$TableName,$Activity,$UpdatedData);
      $stmtLog = sqlsrv_query($conn, $sql1, $paramss);

      if($stmtLog === false)
        throw new Exception(print_r(sqlsrv_errors(), true));
    }

    // =========================
    // FINAL COMMIT
    // =========================
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