<?php
require_once __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

// 80mm thermal printer size
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => [80, 200], // width = 80mm, height auto-ish
    'margin_left' => 5,
    'margin_right' => 5,
    'margin_top' => 5,
    'margin_bottom' => 5
]);



$html = '
<style>
body {
    font-family: Arial;
    font-size: 10px;
}
.center {
    text-align: center;
    font-weight: 600;
}
.hr {
    border-top: 1px dashed #000;
    margin: 5px 0;
}
.center title
{
    text-alignL center;
}

</style>


<div>
Trip Count : 1
</div>
<br>
<div class="center title">
<img src="../feedmix.png" width="150">
</div>
<br>
<div class="center">
BINLOADING REQUEST TICKET
</div>
<div class="hr"></div>
<table width="100%">
<tr>
    <td><b>Ref No:</b></td>
    <td>BL-000123</td>
</tr>
<tr>
    <td><b>Date:</b></td>
    <td>' . date("Y-m-d H:i") . '</td>
</tr>
<tr>
    <td><b>Driver</b></td>
    <td>Nerdjhan</td>
</tr>
<tr>
    <td><b>Truck:</b></td>
    <td>ABC-1234</td>
</tr>
<tr>
    <td><b>Plant:</b></td>
    <td>Plant 1</td>
</tr>

<tr>
    <td><b>Raw Material:</b></td>
    <td>Wheat Bran Pollard</td>
</tr>
</table>
<div class="hr"></div>
<table width="100%">
<tr>
    <td><b>Request Weight</b></td>
        <td><b>Delivered Weight</b></td>
</tr>
<tr>
    <td>1000 (kg)</td>
        <td>500 (kg)</td>
</tr>
</table>
<div class="hr"></div>
<div class="center">
Request By: Checker<br>
Processed By: Dispatcher<br>
Approved By: Supervisor<br><br>

</div>
';
$mpdf->WriteHTML($html);
$mpdf->Output("binloading_receipt.pdf", "I");