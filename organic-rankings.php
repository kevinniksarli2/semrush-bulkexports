<?php require_once 'config.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<style type="text/css">
		form		{ font: 11px Verdana; margin: 40px; }
		.output		{ border: 1px solid #000; font: 11px Verdana; margin: 40px; }
		.output th	{ font-weight: bold; padding: 5px 8px; background-color: #e0e0e0; text-align: center; }
		.output td	{ padding: 3px 5px; background-color: #f0f0f0; }
	</style>
</head>
<body>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
  <textarea cols="35"  rows="10" name="text"></textarea><br />
  
<select name="db" id="db"/>
<option value="*">Select Region</option>
<option value="uk">UK</option>
<option value="us">US</option>
</select>

<select name="limit" id="limit"/>
<option value="0">Select limit</option>
<option value="1000">1000</option>
<option value="2500">2500</option>
<option value="5000">5000</option>
<option value="10000">10000</option>
</select>
  
  <input type="submit" value="Submit" />
</form>

<div class="test">
<?php

if (isset($_POST['text'])) {
  // get rid of whitespace...also, since not all OS use \r
  // but everyone has \n in it, let's get rid of the \r
   
  $kwtext1 = str_replace(" ", '+', ($_POST['text']));
  $kwtext = str_replace("\r", '', trim($kwtext1));

  // now that you have your raw text
  // we need an array to store the data
  
  $kwdata = array();

  // split line by line
  $kwlines = explode("\n", $kwtext);
  
  $db = $_POST['db'];
  $limit = $_POST['limit'];
  }
   
foreach ($kwlines as $kw) {

  // Get all the related keywords from SEMRush

usleep(100);
  
  $u = 'http://' . $db . '.api.semrush.com/?action=report&type=domain_organic&domain=' . $kw . '&key=' . $key . '&display_limit=' . $limit . '&display_offset=0&export=api&export_columns=Ph,Po,Nq,Cp,Ur,Tr,Tc,Co,Nr,Td';
 
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $u);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
  		$answer				 = curl_exec			( $ch );
    
    $kwdata = explode ( "\n", trim ( $answer ) );
	$kwfields = explode ( ";", array_shift ( $kwdata ) );
	
	
			if ( count ( $kwdata ) > 0 )
			{
?>
	<table class="output">
		<tr><th>Domain</th>
<?php $csv_output .= ' ' . ", " . 'Domain' . ", ";?>

<?
				foreach ( $kwfields as $field )
				{
?>
			<th><?= $field; ?></th>
<?php $csv_output .= $field . ", ";?>
<?
				}
?>
		</tr>
<?
				foreach ( $kwdata as $dataline )
				{
					$values = explode ( ";", $dataline, count ( $kwfields ) );
?>
		<tr><td><? echo $kw ?></td>
<?php $csv_output .= $kw . ", ";?>
<?
					foreach ( $values as $value )
					{
?>
			<td><?= $value; ?></td>
<?php $csv_output .= $value . ", ";?>
<?
					}
?>
		</tr>
<?
				}
?>
	</table>
<?
			}
			else
			{
?>
No data found for your request
<?
}
}
?>
</div>

<form name="export" action="export.php" method="post">
    <input type="submit" value="Export all to CSV">
    <input type="hidden" value="<? echo $csv_hdr; ?>" name="csv_hdr">
    <input type="hidden" value="<? echo $csv_output; ?>" name="csv_output">
</form>

</body>
</html>

