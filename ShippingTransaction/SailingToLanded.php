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
if (isset($data)) {
    $ShippingTransactionID = 0;

    $ATA = "";
    $MBL = "";
    $BL = "";
    if ($data->ShippingTransactionID) {
        $ShippingTransactionID = $data->ShippingTransactionID;
    }
    if ($data->ATA) {
        $ATA = $data->ATA;
    }
    $sql = "SELECT MBL, BL FROM ShippingTransaction WHERE ShippingTransactionID = ?";
    $params = array($ShippingTransactionID);
    $stmt1 = sqlsrv_query($conn, $sql, $params);
    while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
        $MBL = $row['MBL'];
        $BL = $row['BL'];
    }
    $sql = "UPDATE ShippingTransaction SET Status = 2, ATA = ?, LandedDate = GETDATE() WHERE ShippingTransactionID = ?";
    $params = array($ATA, $ShippingTransactionID);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        sqlsrv_commit($conn);
        echo 2; // respond to client
        // if (function_exists('fastcgi_finish_request')) {
        //     fastcgi_finish_request();
        // }
    }
    // ✅ PARALLEL SMS SENDING START
    $sql = "SELECT ContactNumber, ContactPerson FROM ShippingContactPersons WHERE EventID = 1";
    $stmt = sqlsrv_query($conn, $sql);

    // if ($stmt) {
    //     $multiHandle = curl_multi_init();
    //     $curlHandles = [];
    //     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    //         $ContactNumber = $row['ContactNumber'];
    //         $ContactPerson = $row['ContactPerson'];

    //         if ($MBL != "") {
    //             $Message = "Good day, $ContactPerson. The MBL: $MBL has landed.";
    //         } else {
    //             $Message = "Good day, $ContactPerson. The BL: $BL has landed.";
    //         }

    //         $postfields = array(
    //             'spans[1][2]' => '2',
    //             'dest_num' => $ContactNumber,
    //             'msg' => $Message,
    //             'send' => 'Send'
    //         );
    //         $ch = curl_init();
    //         curl_setopt_array($ch, array(
    //             CURLOPT_URL => 'http://10.10.2.206/cgi-bin/php/gsm-smssender.php',
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => '',
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => 'POST',
    //             CURLOPT_POSTFIELDS => $postfields,
    //             CURLOPT_HTTPHEADER => array(
    //                 'Authorization: Basic YWRtaW46YWRtaW4='
    //             ),
    //         ));
    //         curl_multi_add_handle($multiHandle, $ch);
    //         $curlHandles[] = $ch;
    //     }
    //     // Execute all requests in parallel
    //     $running = null;
    //     do {
    //         curl_multi_exec($multiHandle, $running);
    //         curl_multi_select($multiHandle);
    //     } while ($running > 0);
    //     // Close all handles
    //     foreach ($curlHandles as $ch) {
    //         curl_multi_remove_handle($multiHandle, $ch);
    //         curl_close($ch);
    //     }
    //     curl_multi_close($multiHandle);
    // }
    // ✅ PARALLEL SMS SENDING END
    // Logging remains unchanged
    if ($stmt) {
           $SystemLogID = 0;
        $FunctionID = 2; 
        $TableName = "ShippingTransaction";
        $Activity = 'Update ShippingTransaction Status into Compeleted :'. ($MBL != "" ? $MBL : $BL);
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
