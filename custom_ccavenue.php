<?php

/*

Plugin Name: Custom ccavenue

Plugin URI: https://4ok.in/wp/plugins/custom_ccavenue

Description: A Plugin to simple intigrate ccavenue payment gateway in wordpress.

Author: 4ok Team

Author URI : https://4ok.in

Author Email : manojkanyan1@gmail.com

Version: 1.0

*/

ob_start();
global $table_prefix, $wpdb;
define('ALL_REVIEWER', $table_prefix . 'custom_ccavenue_data');

//-------------adding plugin menu hook--------------
add_action('admin_menu', 'al_admin_menu');

//-------------adding plugin menu function--------------
function al_admin_menu()
{

	add_menu_page('Custom Ccavenue', 'Custom Ccavenue', 'manage_options', 'custom_ccavenue', 'custom_ccavenue', $icon_url = '', 21);
}


//--------------Get list of registered post type-----------------

function get_registered_post_types()
{
	global $wp_post_types;
	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	return array_keys(get_post_types($args));
}


//-------------Getting all reviewer data--------------

function custom_ccavenue()
{
	global $wpdb;
	if (isset($_GET['al_export'])) {
		ob_end_clean();
		$filename = 'all_reviewer-' . time() . '.csv';
		$fp = fopen('php://output', 'w');
		$header_row =
			array(
				0 => 'Name',
				1 => 'Email',
			);

		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);
		fputcsv($fp, $header_row);
		$users = $wpdb->get_results("Select * from " . ALL_REVIEWER . ";");
		foreach ($users as $u) {
			$row = array();
			$row[0] = $u->name;
			$row[1] = $u->email;
			$row[2] = $u->price;
			fputcsv($fp, $row);
		}

		exit;
	}

	$rows = $wpdb->get_results("Select * from " . ALL_REVIEWER . ";");

	if (isset($_POST['submit'])) {
		update_option('ccca_sandbox', $_POST['sandbox'], 'yes');
		update_option('ccca_merchant_id', $_POST['merchant_id'], 'yes');
		update_option('ccca_key', $_POST['key'], 'yes');
		update_option('ccca_access_code', $_POST['access_code'], 'yes');
		update_option('ccca_redirect_url', $_POST['redirect_url'], 'yes');
		update_option('ccca_cancel_url', $_POST['cancel_url'], 'yes');
	}
	$ccca_sandbox = get_option('ccca_sandbox');
	$ccca_merchant_id = get_option('ccca_merchant_id');
	$ccca_key = get_option('ccca_key');
	$ccca_access_code = get_option('ccca_access_code');
	$ccca_redirect_url = get_option('ccca_redirect_url');
	$ccca_cancel_url = get_option('ccca_cancel_url');

?>

	<div class="wrap">
		<div class="row">
			<div class="col-md-12">
				<h4>Enter Merchant Details</h4>
			</div>
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<form method="post" class="">
							<div class="form-group">
								<input type="checkbox" name="sandbox" value="1" <?php if ($ccca_sandbox == 1) {
																																	echo "checked";
																																} ?> class="form-control"> Enable sanbox
							</div>
							<div class="form-group">
								<label>Merchant Id</label>
								<input type="text" name="merchant_id" value="<?= $ccca_merchant_id ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Access Code</label>
								<input type="text" name="access_code" value="<?= $ccca_access_code ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Working Key</label>
								<input type="text" name="key" value="<?= $ccca_key ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Redirect URL</label>
								<input type="url" name="redirect_url" value="<?= $ccca_redirect_url ?>" class="form-control">
							</div>
							<div class="form-group">
								<label>Cancel URL</label>
								<input type="url" name="cancel_url" value="<?= $ccca_cancel_url ?>" class="form-control">
							</div>
							<button type="submit" name="submit" class="btn btn-danger">Submit</button>
						</form>
					</div>
				</div>

			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-6">
				<h4>All Transaction Data</h4>
			</div>
			<!--<div class="col-md-6 text-right"><a href="admin.php?page=all_reviewer&al_export" class="btn btn-small btn-danger">Export CSV</a></div>-->
		</div>
		<hr>

		<table class="table table-bordered wp-list-table widefat fixed striped posts llll">
			<thead>
				<tr>
					<th>Name</th>
					<th>Email</th>
					<th>Plan Id</th>
					<th>Price</th>
					<th>Transaction Id</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php
				if (!empty($rows)) {
					foreach ($rows as $row_data) { ?>
						<tr>
							<td><?php echo $row_data->name; ?></td>
							<td><?php echo $row_data->email; ?></td>
							<td><?php echo $row_data->order_id; ?></td>
							<td><?php echo $row_data->price; ?></td>
							<td><?php echo $row_data->tid; ?></td>
							<td><?php echo $row_data->time; ?></td>
						</tr>
				<?php }
				} ?>
			</tbody>
		</table>
	</div>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
	<link href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
	<script src="http://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script>
		jQuery(document).ready(function() {
			jQuery('.llll').DataTable();
		});
	</script>

	<?php
}
//-------Function for save reviewer data----------------------
function saverdata($data=[])
{
	global $table_prefix, $wpdb;
	//parse_str($_POST['da'], $newFormRecherche);
	//die( json_encode( $newFormRecherche ) );
	//echo "<pre>";print_r($newFormRecherche['name']);echo "</pre>";die();
	//echo "<pre>";print_r($data);echo "</pre>";
	
	$result = $wpdb->insert(
		ALL_REVIEWER,
		array(
			'name' => $data['name'],
			'email' => $data['email'],
			'tid' => $data['tid'],
			'price' => $data['price'],
			'time' => date('Y-m-d h:m:s'),
			'order_id' => $data['order_id'],
		)
	);
	$url="https://www.bhanjasamitimbjod.org/thank-you/";
	wp_redirect( $url );
    exit;
}

add_action('wp_ajax_saverdata', 'saverdata');
add_action('wp_ajax_nopriv_saverdata', 'saverdata');





//----------------create table function for all reviewer data----------------------

function ar_create_tables()
{
	global $table_prefix, $wpdb;
	require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
	if ($wpdb->get_var("show tables like '" . ALL_REVIEWER . "'") != ALL_REVIEWER) {
		$sql = "CREATE TABLE `" . ALL_REVIEWER . "` (
      `id` int(11) NOT NULL  auto_increment, `name` varchar(155) NOT NULL, `email` varchar(155) DEFAULT null,`tid` text DEFAULT null,`price` text DEFAULT null,`time` datetime,`order_id` varchar(155) DEFAULT null, PRIMARY KEY (`id`)
    );";
		dbDelta($sql);
	}
}

//---------------------Initialize plugin hook-----------------

register_activation_hook(__FILE__, 'ar_create_tables');

//------for export data----------------------
function bbg_csv_export()
{

	if (!isset($_GET['al_export'])) {
		return;
	}
	$filename = 'all_reviewer-' . time() . '.csv';
	$header_row = array(
		0 => 'Name',
		1 => 'Email',
	);
	$data_rows = array();
	global $wpdb;
}


/*
* @param1 : Plain String
* @param2 : Working key provided by CCAvenue
* @return : Decrypted String
*/
function encrypt($plainText, $key)
{
	$key = hextobin(md5($key));
	$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
	$openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
	$encryptedText = bin2hex($openMode);
	return $encryptedText;
}

/*
* @param1 : Encrypted String
* @param2 : Working key provided by CCAvenue
* @return : Plain String
*/
function decrypt($encryptedText, $key)
{
	$key = hextobin(md5($key));
	$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
	$encryptedText = hextobin($encryptedText);
	$decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
	return $decryptedText;
}

function hextobin($hexString)
{
	$length = strlen($hexString);
	$binString = "";
	$count = 0;
	while ($count < $length) {
		$subString = substr($hexString, $count, 2);
		$packedString = pack("H*", $subString);
		if ($count == 0) {
			$binString = $packedString;
		} else {
			$binString .= $packedString;
		}

		$count += 2;
	}
	return $binString;
}

add_shortcode('ccavenue_response', 'ccavenue_response');
function ccavenue_response()
{
	error_reporting(0);

	$workingKey = get_option('ccca_key');;		//Working Key should be provided here.
	$encResponse = $_POST["encResp"];			//This is the response sent by the CCAvenue Server
	$rcvdString = decrypt($encResponse, $workingKey);		//Crypto Decryption used as per the specified working key.
	$order_status = "";
	$decryptValues = explode('&', $rcvdString);
	$dataSize = sizeof($decryptValues);
	echo "<center>";
    $data=[];
	for ($i = 0; $i < $dataSize; $i++) {
		$information = explode('=', $decryptValues[$i]);
		if ($i == 3)	$order_status = $information[1];
		if($information[0]=='order_id'){
		    $data['order_id']=$information[1];
		}
		if($information[0]=='tracking_id'){
		    $data['tid']=$information[1];
		}
		if($information[0]=='amount'){
		    $data['price']=$information[1];
		}
		if($information[0]=='trans_date'){
		    $data['trans_date']=$information[1];
		}
		if($information[0]=='billing_name'){
		    $data['name']=$information[1];
		}
		if($information[0]=='billing_email'){
		    $data['email']=$information[1];
		}
	}
    
	if ($order_status === "Success") {
	    saverdata($data);
		echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
	} else if ($order_status === "Aborted") {
		echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";
	} else if ($order_status === "Failure") {
		echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
	} else {
		echo "<br>Security Error. Illegal access detected";
	}

	echo "<br><br>";

	echo "<table cellspacing=4 cellpadding=4>";
	for ($i = 0; $i < $dataSize; $i++) {
		$information = explode('=', $decryptValues[$i]);
		echo '<tr><td>' . $information[0] . '</td><td>' . $information[1] . '</td></tr>';
	}

	echo "</table><br>";
	echo "</center>";
}
add_shortcode('ccavenue_button', 'ccavenue_form');
function ccavenue_form($atts)
{

	$atts = array_change_key_case((array) $atts, CASE_LOWER);
	$a = shortcode_atts(
		array(
			'type' => 'patron'
		),
		$atts
	);
	if (is_user_logged_in()) {
		$current_user = wp_get_current_user();
		$order_id = rand(0, 100) . 'MEM-' . $a['type'] . $current_user->ID;
		if ($a['type'] == 'patron') {
			$price = 5000;
		} elseif ($a['type'] == '10yrs') {
			$price = 1000;
		} elseif ($a['type'] == 'general') {
			$price = 200;
		} else {
			$price = 0;
		}
		$ccca_sandbox = get_option('ccca_sandbox');
		$ccca_merchant_id = get_option('ccca_merchant_id');
		$ccca_key = get_option('ccca_key');
		$ccca_access_code = get_option('ccca_access_code');
		$ccca_redirect_url = get_option('ccca_redirect_url');
		$ccca_cancel_url = get_option('ccca_cancel_url');
		include_once('form.php');
		error_reporting(0);
		if (isset($_POST['tid'])) :
			$merchant_data = '';
			$working_key = $ccca_key; //Shared by CCAVENUES
			$access_code = $ccca_access_code; //Shared by CCAVENUES
			if ($ccca_sandbox == 1) {
				$r_url = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
			} else {
				$r_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
			}
			foreach ($_POST as $key => $value) {
				$merchant_data .= $key . '=' . $value . '&';
			}

			$encrypted_data = encrypt($merchant_data, $working_key); // Method for encrypting the data.

	?>
			<form method="post" name="redirect" action="<?= $r_url ?>">
				<?php
				echo "<input type=hidden name=encRequest value=$encrypted_data>";
				echo "<input type=hidden name=access_code value=$access_code>";
				?>
			</form>
			</center>
			<script language='javascript'>
				document.redirect.submit();
			</script>
<?php
		endif;
	} else {
		echo "<h1>Please login or register to get the membership</h1>";
	}
}
