<?php 
	include("koneksi.php"); 
	header('Content-type: application/json');
	
	$receive = "NULL";
	$last_id = 0;
	
	$FILTER_FIELD_NAME 	= "nama_barang";
	$TABLE_PENJUAL 		= "penjual";
	$LIMIT_DATA			= 10;
	$FIELD_ID_BARANG	= "id_barang";
	$FIELD_NAMA_BARANG	= "nama_barang";
	
	$header_last_inserted_id = "LAST_INSERTED_ID";
	$header_data_size	= "DATA_PENJUAL_SIZE";
	$header_returned_data = "PENJUAL";
	$header_query		= "QUERY";
	$header_receive_data= "RECEIVE_DATA";
	
	if( isset( $_POST['nama_toko'] ) || isset( $_POST['kontak_toko'] ) || isset( $_POST['alamat_toko'] ) ) {
		
		$receive = mysql_escape_string( json_encode($_POST) );
		
		$nama_penjual = ""; //Kosong karena data yang dikirim hanya nama_toko
		$nama_toko = mysql_escape_string($_POST['nama_toko']);
		$alamat_toko = mysql_escape_string($_POST['alamat_toko']);
		$geolocation = ""; //Geolocation belum, tahap berikutnya utk integrasi dengan Google Maps
		$kontak_toko = mysql_escape_string($_POST['kontak_toko']);
		$email_toko = ""; //Email toko belum ada di kirim, dengan data ini pengguna bisa mengirim langsung email ke toko tujuan
		$nama_barang = $_POST['nama_barang'];			
		
		$QUERY_INSERT_PENJUAL = sprintf("INSERT INTO %s (`id_penjual`, `nama_penjual`, `nama_toko`, `alamat_toko`, `geolocation`, `kontak_toko`, `email_toko`) VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s')", $TABLE_PENJUAL, $nama_penjual, $nama_toko, $alamat_toko, $geolocation, $kontak_toko, $email_toko );
		
		$q_insert = mysql_query($QUERY_INSERT_PENJUAL);
		
		$last_id = mysql_insert_id();
		
		if(!$q_insert) {
			errorMassage();
			return;
		}
		
	
	} else {
		$QUERY = sprintf("SELECT * FROM %s", $TABLE_PENJUAL); 
	}
	
	//Array utk menampung data dari database
	$dataTable = array();
	
	
	$q = mysql_query($QUERY)/* or die(mysql_error() . " No : " . mysql_errno())*/;
	
	if(!$q) {
		
		errorMassage();
		return;

	}
	
	// Cara Simpel make mysql_fetch_object
	while($data = mysql_fetch_object($q)){
		$dataTable[] = $data; 
	}
	
	$data_size = sizeof($dataTable);
	
	//Bentuk data yang akan dikembalikan

//	{
// 	"PENJUAL": [],
// 	"DATA_PENJUAL_SIZE": 6,
// 	"QUERY": "SELECT * FROM penjual",
// 	"RECEIVE_DATA": "NULL",
// 	"LAST_INSERTED_ID": 0
// 	}
	
	//Final data will send to client side
	$data = sprintf(
	'{  
	"%s" : %s , 
	"%s" : %d , 
	"%s" : "%s", 
	"%s" : "%s", 
	"%s" : %d 
	}', 
		$header_returned_data, json_encode($dataTable, true), 
		$header_data_size, $data_size, 
		$header_query, $QUERY, 
		$header_receive_data, $receive, 
		$header_last_inserted_id, $last_id 
	);		

	print_r($data);		//Informasi yg dikirimkan kepada client	
	
	function errorMassage() {		
		$message = array("msg" => "Gagal");
		json_encode($message, true);
		print_r(json_encode($message, true));		//Informasi yg dikirimkan kepada client
	}

?>