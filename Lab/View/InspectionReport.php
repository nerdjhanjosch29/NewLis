
<?php

// include_once "../functions/database_eval.php";
require_once('../vendor/autoload.php');

require_once '../../Database/connection.php';

// $mpdf = new \Mpdf\Mpdf(['default_font_size' => 7,


$InspectionID = "";


if(isset($_GET['id']))
{
$InspectionID = $_GET['id'];
}
$sql = "SELECT 
rir.BL
,rir.PO
,rir.DeliveryTypeID
,rir.EffectiveDate
,rir.VersionNo
,rir.InspectionDate
,rir.SampleCode
,rir.CategoryID
,rmc.CategoryName
,rir.SupplierID
,s.Supplier
,rir.DRNumber
,rir.PlateNo
,rir.Status
,rir.ContainerNumber
,rir.TimeOfSampling
,rir.DateTimeReleased
,rir.Remarks
FROM RawMatsInspectionReport rir
LEFT JOIN Supplier s ON s.SupplierID = rir.SupplierID
LEFT JOIN RawMaterialCategory rmc ON rmc.CategoryID = rir.CategoryID
WHERE InspectionReportID = ?";
$params = array($InspectionID);
$stmt = sqlsrv_query($conn,$sql,$params);
$BL = "";
$InspectionDate = "";
$SampleCode = "";
$DRNumber = "";
$TimeOfSampling = "";
$ContainerNumber = "";
$Status = "";
$Remarks = "";
$SampleName = "";
$DateTimeReleased = "";

$NoRemarks = "";
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
{
$BL = $row['BL'];
$InspectionDate = $row['InspectionDate'];
$SampleCode = $row['SampleCode'];
$DRNumber = $row['DRNumber'];
$Supplier = $row['Supplier'];
$ContainerNumber = $row['ContainerNumber'];
$TimeOfSampling = $row['TimeOfSampling'];
$Status = $row['Status'];
$PlateNo = $row['PlateNo'];
$SampleName = $row['CategoryName'];
$DateTimeReleased = $row['DateTimeReleased'];
$Remarks = $row['Remarks'];


  if($Remarks = $row['Remarks'] == "")
        {
            $NoRemarks .= '
                    <tr>
                    <td>Remarks:________________________________________________________________________________<td>
                    </tr>
                    <tr>
                    <td>__________________________________________________________________________________________<td>
                    </tr>
                    <tr>
                    <td>__________________________________________________________________________________________<td>
                    </tr>
            ';
        }
        else 
        {      
            $NoRemarks .= '
                            <tr>
                            <td  > Remarks: <span style="font-weight:bold;"> '. $Remarks = $row['Remarks'] .' </span> <td>
                            </tr>
                            <tr>
                            <td>__________________________________________________________________________________________<td>
                            </tr>
                            <tr>
                            <td>__________________________________________________________________________________________<td>
                    </tr>
            ';
        }
}

$sql1 = " SELECT 
                rpr.ParameterResultID
                ,rpr.ParameterID
                ,rmp.Parameter
                ,rpr.InspectionID
                , CASE
                    WHEN rmp.boolean = 1 AND rpr.StandardValue = 1 THEN 'POSITIVE'
                    WHEN rmp.boolean = 1 AND rpr.StandardValue = 0 THEN 'NEGATIVE'
                    ELSE CAST(CONVERT(decimal(10,2), rpr.StandardValue) AS varchar(20))
                END AS Value
                ,rpr.Result,
                rpr.Permission
				,rmp.OperatorID, 
                op.OperatorName,
                op.Operator,
                CASE
                    WHEN rmp.boolean = 1 AND rpr.Result = 1 THEN 'POSITIVE'
                    WHEN rmp.boolean = 1 AND rpr.Result = 0 THEN 'NEGATIVE'
                    ELSE CAST(CONVERT(decimal(10,2), rpr.Result) AS varchar(20))
                END AS FinalValue
                FROM RawMatsParameterResult  rpr
                LEFT  JOIN dbo.RawMatsParameters AS rmp ON rmp.ParameterID = rpr.ParameterID
                LEFT  JOIN dbo.Operator AS op ON op.OperatorID = rmp.OperatorID
                WHERE rpr.InspectionID = ? AND deleted = 0";
$params = array($InspectionID);
$stmt1 = sqlsrv_query($conn, $sql1,$params); 
$parameters = "";


while ($resultRows = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    // $StandardID = $resultRows['StandardID'];
$Permission = $resultRows['Permission'];

        if($Permission == 1)
        {
 $parameters .= '<tr>
            <td style="font-size:12px;">' . $resultRows['Parameter'] . '</td>
            <td style="font-size:12px;">' . $resultRows['Value'] . '</td>
            <td style="font-size:12px;background-color:#FFD0CE;">' . $resultRows['FinalValue'] . '</td>
        </tr>';
       
        }
        else
        {
$parameters .= '<tr>
            <td style="font-size:12px;">' . $resultRows['Parameter'] . '</td>
            <td style="font-size:12px;">' . $resultRows['Value'] . '</td>
            <td style="font-size:12px;">' . $resultRows['FinalValue'] . '</td>
        </tr>';
        }
      
    
}

$mpdf->useDefaultCSS = true;
$mpdf = new \Mpdf\Mpdf(['default_font_size' => 7,'orientation'=> 'L',
'mode' => 'utf-8', 'format' => 'A4-L', //SET into A4 size of paper

'default_font' => 'Tahoma']);
$mpdf->restrictColorSpace = 0;
$mpdf->showWatermarkImage = true;
$mpdf->useSubstitutions = false;
$mpdf->SetDisplayMode('fullpage');

// Preserve colors when printing
$mpdf->SetDefaultBodyCSS('color-adjust', 'exact');
$mpdf->SetDefaultBodyCSS('print-color-adjust', 'exact');

if ($Status == 0) {
     $css = '
    <style>
    .background-watermark {
        background-image: url("../Approved.jpg");

        background-position: center;
        background-repeat: no-repeat;
      
    }

    </style>';

             $ReportStatus = '
             <tr>
            <th style="padding-left:2em;color:green;">✔️ APPROVED<th>
    <th style="padding-left:4em;">□ REJECTED<th>
     <th style="padding-left:3.5em;">□ WAIVED<th>
      </tr>'; 
} 
else if ($Status == 1) {
    $css = '
    <style>
    .background-watermark {
        background-image: url("../Rejected.jpg");
  
        background-position: center;
        background-repeat: no-repeat;
    
        
    }

    </style>';
     // add CSS for background image
            $ReportStatus = '
             <tr>
            <th style="padding-left:2em;">□ APPROVED<th>
    <th style="padding-left:4em; color:red;">✔️ REJECTED<th>
     <th style="padding-left:3.5em;">□ WAIVED<th>
      </tr>'; 

}
else if ($Status == 2) {
    $css = '
    <style>
    .background-watermark {
        background-image: url("../Waived.jpg");
  
        background-position: center;
        background-repeat: no-repeat;
       
       
    }
    </style>';
     // add CSS for background image
         $ReportStatus = '
             <tr>
            <th style="padding-left:2em;">□ APPROVED<th>
    <th style="padding-left:4em;">□ REJECTED<th>
     <th style="padding-left:3.5em;  color:gold;">✔️ WAIVED<th>
      </tr>'; 
}
$stylesheet = file_get_contents('pdf.css'); // external css
$mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
date_default_timezone_set('Asia/Manila');

$mpdf->SetHTMLFooter('
<hr>
<table width="100%">
    <tr>
        <td width="80%" align="left"><font size="5">Printed: '. date('m/d/Y h:i:s A') .'</font></td>
        <td width="80%" align="right"><font size="5">{PAGENO}/{nbpg}</font></td> 
    </tr>
</table> </hr>');
$mpdf->WriteHTML('

<div class="background-watermark" style="float: left; width: 49%; " '.$css.'> 

    <table class="header">
        <tr>
            <th  colspan="2"><img src="../feedmixlogo.jpg " height="30px" ></th>
            <th colspan="2" style="font-family:Times New Roman;font-size:13px;">RAW MATERIAL <br>INSPECTION REPORT</th>
        </tr>
        <tr>
            <td colspan="2">Quality Assurance Department</td>
            <td rowspan="3" class="approved-by">Approved by <br>
           SABRINA C. DAGARAT<br>
            Q.A Manager
            </td>
            <td rowspan="3">Document No.:
            <br><br>
            QARM-F0003
            </td>
        </tr>
        <tr>
            <td>Effective Date:</td>
            <td>1 October 2020</td>
        </tr>
        <tr>
            <td>Version No:</td>
            <td>003</td>
        </tr>
        <tr>
            <td colspan="4"><p>
            This document is a property of Feedmix Specialist Inc. II - Quality Assurance Department. Any unauthorized reproduction or use is strictly prohibited.</p></td>
        </tr>
    </table>

    <table style="margin-top:2em; margin-left: auto; margin-right: auto; 
    font-family:Arial; font-size:10px;
    ">
        <tr>
            <th  style="text-align:left">DATE:<th>
<td class="Report">'.($InspectionDate instanceof DateTime ? $InspectionDate->format("m/d/Y") : '').'<td>

            <th style="text-align:left">SI / DR No.:<th>
            <td class="Report">'.$DRNumber.'<td>
        </tr>

        <tr>
            <th>SAMPLE CODE:<th>
            <td class="Report">'.$SampleCode.'<td>
            <th style="text-align:left">PLATE NO.:<th>
            <td class="Report">'.$PlateNo.'<td>
        </tr>

        <tr>
            <th>SAMPLE NAME:<th>
            <td class="Report">'.$SampleName.'<td>
            <th style="text-align:left">CONTAINER NO.:<th>
            <td class="Report">'.$ContainerNumber.'<td>
        </tr>

<tr>
    <th style="text-align:left">SUPPLIER:<th>
    <td class="Report">'.$Supplier.'<td>
    <th>TIME OF SAMPLING:<th>
    <td class="Report">'.($TimeOfSampling instanceof DateTime ? $TimeOfSampling->format('l, F j, Y g:i A') : $TimeOfSampling).'<td>
</tr>


    </table>

    <table style="margin-top: 2em;font-family:Arial; font-size:10px;">
        <tr>
            <th>INSPECTION RESULT:<th>
        </tr>
    </table>

    <table class="standard">
        <tr>

            <th>PARAMETER/S</th>
            <th>STANDARD/S</th>
            <th>RESULT/S</th>
        </tr>

        '. $parameters.'
 
    </table>
  
    <table style="font-family:Arial; font-size:15px;">

'.$ReportStatus.'
   
    </table>
    

<table  style="margin-top: 10px;">
<tr>
<td class="timeDate" >TIME AND DATE RELEASED: '.($DateTimeReleased instanceof DateTime ? $DateTimeReleased->format('l, F j, Y g:i A') : $DateTimeReleased).'<td>
</tr>


</table>
<table style="margin-top: 10px;font-size: 12px;font-family: times New Roman; border: 1px;">

'. $NoRemarks.'

</table>

<table style="margin-top: 1em;font-family:Arial;">
<tr>
<td style="font-style:italic;font-size:10px;">To be filled by QA Department<td>
<td style="padding-left:11em; font-style:italic;font-size:10px;">To be filled by Logistics Department<td>
</tr>
</table>


<table style="margin-top: 3em;font-family:Times New Roman;font-size:12px;">
<tr>
<td  style="font-size:12px;">Inspected by:<td>
<td  style="padding-left:9em;font-size:12px;">Result Received by:<td>
</tr>
<tr>
<td  style="font-size:12px;">___________________________<td>
<td  style="padding-left:9em;font-size:12px;">__________________________<td>
</tr>
</table>

<table style="margin-top: 1em;font-family:Times New Roman;font-size:12px;">
<tr>
<td  style="font-size:12px;">Noted by:<td>
<td  style="padding-left:9em;font-size:12px;">Noted  by:<td>
</tr>
<tr>
<td  style="font-size:12px;">___________________________<td>
<td  style="padding-left:9em;font-size:12px;">__________________________<td>
</tr>
</table>

</div>







<div class="background-watermark"   style="float: right; width: 49%"> 

      <table class="header">
        <tr>
            <th  colspan="2"><img src="../feedmixlogo.jpg " height="30px" ></th>
            <th colspan="2" style="font-family:Times New Roman;font-size:13px;">RAW MATERIAL <br>INSPECTION REPORT</th>
        </tr>
        <tr>
            <td colspan="2">Quality Assurance Department</td>
            <td rowspan="3" class="approved-by">Approved by <br>
           SABRINA C. DAGARAT<br>
            Q.A Manager
            </td>
            <td rowspan="3">Document No.:
            <br><br>
            QARM-F0003
            </td>
        </tr>
        <tr>
            <td>Effective Date:</td>
            <td>1 October 2020</td>
        </tr>
        <tr>
            <td>Version No:</td>
            <td>003</td>
        </tr>
        <tr>
            <td colspan="4"><p>
            This document is a property of Feedmix Specialist Inc. II - Quality Assurance Department. Any unauthorized reproduction or use is strictly prohibited.</p></td>
        </tr>
    </table>

    <table style="margin-top:2em; margin-left: auto; margin-right: auto; 
    font-family:Arial; font-size:10px;
    ">
        <tr>
            <th  style="text-align:left">DATE:<th>
<td class="Report">'.($InspectionDate instanceof DateTime ? $InspectionDate->format("m/d/Y") : '').'<td>

            <th style="text-align:left">SI / DR No.:<th>
            <td class="Report">'.$DRNumber.'<td>
        </tr>

        <tr>
            <th>SAMPLE CODE:<th>
            <td class="Report">'.$SampleCode.'<td>
            <th style="text-align:left">PLATE NO.:<th>
            <td class="Report">'.$PlateNo.'<td>
        </tr>

        <tr>
            <th>SAMPLE NAME:<th>
            <td class="Report">'.$SampleName.'<td>
            <th style="text-align:left">CONTAINER NO.:<th>
            <td class="Report">'.$ContainerNumber.'<td>
        </tr>

<tr>
    <th style="text-align:left">SUPPLIER:<th>
    <td class="Report">'.$Supplier.'<td>
    <th>TIME OF SAMPLING:<th>
    <td class="Report">'.($TimeOfSampling instanceof DateTime ? $TimeOfSampling->format('l, F j, Y g:i A') : $TimeOfSampling).'<td>
</tr>


    </table>

    <table style="margin-top: 2em;font-family:Arial; font-size:10px;">
        <tr>
            <th>INSPECTION RESULT:<th>
        </tr>
    </table>

    <table class="standard">
        <tr>

            <th>PARAMETER/S</th>
            <th>STANDARD/S</th>
            <th>RESULT/S</th>
        </tr>

        '. $parameters.'
 
    </table>

    <table style="font-family:Arial; font-size:15px;">
'.$ReportStatus.'
    </table>
    
<table  style="margin-top: 10px;">
<tr>
<td class="timeDate" >TIME AND DATE RELEASED: '.($DateTimeReleased instanceof DateTime ? $DateTimeReleased->format('l, F j, Y g:i A') : $DateTimeReleased).'<td>
</tr>


</table>
<table style="margin-top: 10px;font-size: 12px;font-family: times New Roman;">
'. $NoRemarks.'
</table>

<table style="margin-top: 1em;font-family:Arial;">
<tr>
<td style="font-style:italic;font-size:10px;">To be filled by QA Department<td>
<td style="padding-left:11em; font-style:italic;font-size:10px;">To be filled by Logistics Department<td>
</tr>
</table>


<table style="margin-top: 3em;font-family:Times New Roman;font-size:12px;">
<tr>
<td  style="font-size:12px;">Inspected by:<td>
<td  style="padding-left:9em;font-size:12px;">Result Received by:<td>
</tr>
<tr>
<td  style="font-size:12px;">___________________________<td>
<td  style="padding-left:9em;font-size:12px;">__________________________<td>
</tr>
</table>

<table style="margin-top: 1em;font-family:Times New Roman;font-size:12px;">
<tr>
<td  style="font-size:12px;">Noted by:<td>
<td  style="padding-left:9em;font-size:12px;">Noted  by:<td>
</tr>
<tr>
<td  style="font-size:12px;">___________________________<td>
<td  style="padding-left:9em;font-size:12px;">__________________________<td>
</tr>
</table>

</div>





');



$mpdf->Output();


?>