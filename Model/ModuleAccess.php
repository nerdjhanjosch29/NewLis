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
        $AccessRight = "";
        $ModuleAccessID = 0;
        $Title = "";
        $Category = "";
        $AccessName = "";
        $Viewing = "";
        if($data->ModuleAccessID)
        {
        $ModuleAccessID = $data->ModuleAccessID;
        }
        if($data->AccessRight)
        {
        $AccessRight = $data->AccessRight;
        }
        if($data->Title)
        {
        $Title = $data->Title;
        }
        if($data->Category)
        {
        $Category = $data->Category;
        }
        if($data->AccessName)
        {
        $AccessName = $data->AccessName;
        }
        if($data->Viewing)
        {
        $Viewing = $data->Viewing;
        }
            $sql = "EXEC [dbo].[UserAccess]
                    @ModuleAccessID = ?,
                    @AccessRight = ?,
                    @Title = ?,
                    @Category = ?,
                    @AccessName = ?,
                    @Viewing = ?,
                    @UserID = ?";

            $params = array($ModuleAccessID,$AccessRight,$Title,$Category,$AccessName,$Viewing,$UserID);
            $stmt = sqlsrv_query($conn, $sql, $params);
            $result = 0;
            while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
            {
                echo $result = $row['result'];
            }
            if($result != 0 )
            {       
                $SystemLogID = 0;
                $FunctionID = 1; 
                $TableName = "Module Access";
                $Activity = "Insert Access Right : $AccessRight";
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
              sqlsrv_commit($conn);   
}
?>