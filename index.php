<!DOCTYPE html>
<html lang="nl">

<head>

<title>Automatisch matchen van e-facturen</title>
<meta name="robots" content="index, follow" />
<meta name="description" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

<!-- Android -->
<meta name="mobile-web-app-capable" content="yes" />
<meta name="mobile-web-app-status-bar-style" content="black" />
<!-- iOS -->
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" other="black-translucent" />


<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet" />

<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<style type="text/css">
</style>

</head>

<body>

<div class="container">
<h1><a href="">Automatisch matchen van e-facturen</a></h1>
<!--<p class="lead">Lorem ipsum</p>-->


<?php if ($_SERVER['REQUEST_METHOD'] == 'POST'):?>

<?php
$invoice = new StdClass();
?>

<table class="table table-striped table-hover">

<thead>
<tr>
<th>Category</th>
<th>Task</th>
<th>Result</th>
<th>Value</th>
</tr>
</thead>

<tbody>

<tr>
<td>HTTP</td>
<td>POST-data and FILES-data submitted</td>
<td>
<?php
if (!isset($_POST['webservices']) || !isset($_POST['token']) || !isset($_FILES['ublfile'])) {
	die('<i class="glyphicon glyphicon-remove-sign text-danger" />');
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td></td>
</tr>

<tr>
<td>HTTP</td>
<td>Check file type of uploaded file for text/xml</td>
<td>
<?php
if ($_FILES['ublfile']['type'] != 'text/xml') {
	die('<i class="glyphicon glyphicon-remove-sign text-danger" />');
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $_FILES['ublfile']['type']?></td>
</tr>

<tr>
<td>UBL</td>
<td>Parse incoming XML file for valid XML file</td>
<td>
<?php
$xml = simplexml_load_file($_FILES['ublfile']['tmp_name']);
if ($xml == FALSE) {
	die('<i class="glyphicon glyphicon-remove-sign text-danger" />');
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $_FILES['ublfile']['tmp_name']?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get namespaces from XML file</td>
<td>
<?php
$namespaces = $xml->getDocNamespaces();
$xml->registerXPathNamespace('cbc', $namespaces['cbc']);
$xml->registerXPathNamespace('cac', $namespaces['cac']);
$cbc = $xml->children($namespaces['cbc']);
$cac = $xml->children($namespaces['cac']);
if ($namespaces == FALSE) {
	die('<i class="glyphicon glyphicon-remove-sign text-danger" />');
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo implode(',', array_keys($namespaces))?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get ID</td>
<td>
<?php
$invoice->ID = isset($cbc->ID) ? $cbc->ID : FALSE;
if ($invoice->ID == FALSE) {
	echo '<i class="glyphicon glyphicon-remove-sign text-danger" />';
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $invoice->ID?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get IssueDate</td>
<td>
<?php
$invoice->IssueDate = isset($cbc->IssueDate) ? $cbc->IssueDate : FALSE;
if ($invoice->IssueDate == FALSE) {
	echo '<i class="glyphicon glyphicon-remove-sign text-danger" />';
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $invoice->IssueDate?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get DocumentCurrencyCode</td>
<td>
<?php
$invoice->DocumentCurrencyCode = isset($cbc->DocumentCurrencyCode) ? $cbc->DocumentCurrencyCode : FALSE;
if ($invoice->DocumentCurrencyCode == FALSE) {
	echo '<i class="glyphicon glyphicon-remove-sign text-danger" />';
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $invoice->DocumentCurrencyCode?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get OrderReference</td>
<td>
<?php
$invoice->OrderReference = isset($cac->OrderReference) ? $cac->OrderReference->children('cbc', TRUE)->ID : FALSE;
if ($invoice->OrderReference == FALSE) {
	echo '<i class="glyphicon glyphicon-remove-sign text-danger" />';
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $invoice->OrderReference?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get AccountingSupplierParty</td>
<td>
<?php
$invoice->AccountingSupplierParty = isset($cac->AccountingSupplierParty->Party->PartyName->children('cbc', TRUE)->Name) ? $cac->AccountingSupplierParty->Party->PartyName->children('cbc', TRUE)->Name : FALSE;
if ($invoice->AccountingSupplierParty == FALSE) {
	echo '<i class="glyphicon glyphicon-remove-sign text-danger" />';
} else { 
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $invoice->AccountingSupplierParty?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get PartyTaxScheme</td>
<td>
<?php
$invoice->PartyTaxScheme = (isset($cac->AccountingSupplierParty->Party->PartyTaxScheme->children('cbc', TRUE)->CompanyID) && isset($cac->AccountingSupplierParty->Party->PartyTaxScheme->TaxScheme->children('cbc', TRUE)->ID) && $cac->AccountingSupplierParty->Party->PartyTaxScheme->TaxScheme->children('cbc', TRUE)->ID == 'VAT') ? $cac->AccountingSupplierParty->Party->PartyTaxScheme->children('cbc', TRUE)->CompanyID : FALSE;
if ($invoice->PartyTaxScheme == FALSE) {
	echo '<i class="glyphicon glyphicon-remove-sign text-danger" />';
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $invoice->PartyTaxScheme?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get PartyLegalEntity</td>
<td>
<?php
$invoice->PartyLegalEntity = isset($cac->AccountingSupplierParty->Party->PartyLegalEntity->children('cbc', TRUE)->CompanyID) ? $cac->AccountingSupplierParty->Party->PartyLegalEntity->children('cbc', TRUE)->CompanyID : FALSE;
if ($invoice->PartyLegalEntity == FALSE) {
	echo '<i class="glyphicon glyphicon-remove-sign text-danger" />';
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $invoice->PartyLegalEntity?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get PayeeFinancialAccount</td>
<td>
<?php
$invoice->PayeeFinancialAccount = isset($cac->PaymentMeans->PayeeFinancialAccount->children('cbc', TRUE)->ID) ? $cac->PaymentMeans->PayeeFinancialAccount->children('cbc', TRUE)->ID : FALSE;
if ($invoice->PayeeFinancialAccount == FALSE) {
	echo '<i class="glyphicon glyphicon-remove-sign text-danger" />';
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
?>
</td>
<td><?php echo $invoice->PayeeFinancialAccount?></td>
</tr>

<tr>
<td>UBL</td>
<td>Get Invoice lines</td>
<td>
<?php
$invoice->InvoiceLines = array();
foreach ($cac->InvoiceLine as $i) {
	$j = new stdClass();
	$j->InvoicedQuantity = (float) $i->children('cbc', TRUE)->InvoicedQuantity;
	$j->LineExtensionAmount = (float) $i->children('cbc', TRUE)->LineExtensionAmount;
	$j->currencyID = (string) $i->children('cbc', TRUE)->LineExtensionAmount->attributes()['currencyID'];
	$j->unitCode = (string) $i->children('cbc', TRUE)->InvoicedQuantity->attributes()['unitCode'];
	$j->Description = (string) $i->Item->children('cbc', TRUE)->Description;
	$j->Name = (string) $i->Item->children('cbc', TRUE)->Name;
	$j->SellersItemIdentification = (string) $i->Item->SellersItemIdentification->children('cbc', TRUE)->ID;
	$j->ClassifiedTaxCategory = (float) $i->Item->ClassifiedTaxCategory->children('cbc', TRUE)->Percent;
	$j->PriceAmount = (float) $i->Price->children('cbc', TRUE)->PriceAmount;
	$j->BaseQuantity = (float) $i->Price->children('cbc', TRUE)->BaseQuantity;
	$invoice->InvoiceLines[] = $j;
}
echo count($invoice->InvoiceLines) > 0 ? '<i class="glyphicon glyphicon-ok-sign text-success" />': '<i class="glyphicon glyphicon-remove-sign text-danger" />';
?>
</td>
<td><?php echo count($invoice->InvoiceLines);?> invoice lines</td>
</tr>

<tr>
<td>ERP</td>
<td>Authenticate to Profit Webservices</td>
<td>
<?php
$curl = curl_init();
$headers = array();
$headers[] = 'User-Agent: UBL matching (32772.gnr)';
$headers[] = 'Authorization: AfasToken '.base64_encode($_POST['token']);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $_POST['webservices'].'metainfo',
	CURLOPT_SSL_VERIFYPEER => FALSE
));
$response = curl_exec($curl);
if (curl_errno($curl) || !isset(curl_getinfo($curl)['http_code']) || curl_getinfo($curl)['http_code'] != 200) {
	die('<i class="glyphicon glyphicon-remove-sign text-danger" />');
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
$metainfo = json_decode($response);
curl_close($curl);
?>
</td>
<td><?php echo $metainfo->info->envid.'/'.$metainfo->info->appName;?></td>
</tr>

<tr>
<td>ERP</td>
<td>Get Order lines</td>
<td>
<?php
$curl = curl_init();
$headers = array();
$headers[] = 'User-Agent: UBL matching (32772.gnr)';
$headers[] = 'Authorization: AfasToken '.base64_encode($_POST['token']);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $_POST['webservices'].'connectors/GNR_Matching_Inkooporderregels/Ordernummer/'.$invoice->OrderReference,
	CURLOPT_SSL_VERIFYPEER => FALSE
));
$response = curl_exec($curl);
$orderlines = json_decode($response)->rows;
if (curl_errno($curl) || !isset(curl_getinfo($curl)['http_code']) || curl_getinfo($curl)['http_code'] != 200 || count($orderlines) == 0) {
	echo '<i class="glyphicon glyphicon-remove-sign text-danger" />';
} else {
	echo '<i class="glyphicon glyphicon-ok-sign text-success" />';
}
curl_close($curl);
?>
</td>
<td><?php echo count($orderlines);?> orderlines</td>
</tr>

</tbody>
</table>

<h2>Invoice lines</h2>
<?php foreach ($invoice->InvoiceLines as $line):?>
<table class="table table-condensed table-striped table-bordered">
<?php foreach (get_object_vars($line) as $key => $value):?>
<tr>
<td width="40%"><?php echo $key?></td>
<td width="60%"><?php echo $value?></td>
</tr>
<?php endforeach?>
</table>
<?php endforeach?>

<?php if (count($orderlines) > 0):?>
<h2>Order lines</h2>
<div class="table-responsive">
<table class="table table-condensed table-striped">
<tr>
<?php foreach (array_keys(get_object_vars($orderlines[0])) as $key):?><th><?php echo $key?></th><?php endforeach?>
</tr>
<?php foreach ($orderlines as $orderline):?>
<tr>
<?php foreach (get_object_vars($orderline) as $value):?>
<td><?php echo $value?></td>
<?php endforeach?>
</tr>
<?php endforeach?>
</table>
</div>
<?php endif?>



<div class="btn btn-default" data-toggle="collapse" data-target="#showPOST">Show POST</div>
<div class="btn btn-default" data-toggle="collapse" data-target="#showFILES">Show FILES</div>
<div class="btn btn-default" data-toggle="collapse" data-target="#showNAMESPACES">Show NAMESPACES</div>
<div class="collapse" id="showPOST"><pre><?php var_dump($_POST)?></pre></div>
<div class="collapse" id="showFILES"><pre><?php var_dump($_FILES['ublfile'])?></pre></div>
<div class="collapse" id="showNAMESPACES"><pre><?php var_dump($namespaces)?></pre></div>

<h2>XML</h2>
<pre><?php echo htmlentities($xml->asXml())?></pre>


<?php else: ?>


<div class="panel panel-primary">
	<div class="panel-heading">
		<h2>Input</h2>
	</div>
	<div class="panel-body">
		<form role="form" method="post" action="" enctype="multipart/form-data">
			<div class="form-group">
				<label for="ublfile">UBL-bestand</label>
				<input type="file" name="ublfile" id="ublfile" />
			</div>
			<div class="form-group">
				<label for="webservices">Webservices-adres</label>
				<input type="url" name="webservices" class="form-control" id="webservices" value="https://acceptXXXXX.afasonlineconnector.nl/profitrestservices/" />
			</div>
			<div class="form-group">
				<label for="token">Token</label>
				<input type="text" name="token" class="form-control" id="token" value="<token><version>1</version><data>10426E73C025455DA088C00BBF841AB87BExxx</data></token>" />
			</div>
			<button type="submit" class="btn btn-default btn-block btn-primary">Submit</button>
		</form>
	</div>
</div>

<?php endif?>

</div>


<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

</body>

</html>
