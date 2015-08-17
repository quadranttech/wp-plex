<?php



require_once('request.php');

require_once('ResponseHandler.php');

require_once('PlexDataSourceKey.php');

require_once('plexlog.php');



class PlexOrder{

 	/* 

 		this function is responsible for writting a orser in ples and assigning product on that order id;

 	*/

 	public function WriteNewPlexOrder($orderObject, $billingAddressCode, $shippingAddressCode, $Prepaid_Authorization,$poNo){

 		try{

	 		$plexServiceRequestObject = new plexServiceRequest();

			$responseHandlerObject = new ResponseHandler();

	 		

	 		if($Prepaid_Authorization=='test' || empty($Prepaid_Authorization)){

	 			$Prepaid_Authorization='1324567890'; /*  This Prepaid_Authorization is for testing */

	 		}

	 		//echo 'Prepaid_Authorization='.$Prepaid_Authorization;

	 		$plexuserUserId = 'PWPU'.str_pad($orderObject->get_user_id(), 6, '0', STR_PAD_LEFT);

	 		$freightTerms = "PrePaid &amp; Add";
	 		$shipping_service = str_replace(' ', '_', strtoupper ( $orderObject->get_shipping_method() ) );
	 		if('FREE_SHIPPING' == trim($shipping_service)){
	 			$shipping_service = 'UPS_GROUND';
	 			$freightTerms = 'PrePaid';
	 		}else if('FLAT_RATE' == trim($shipping_service)){
	 			$shipping_service = 'UPS_GROUND';
	 		}

	 		/*request parameter*/

	 		$paramArray = array(

								array(

										'Name'		=>	'Customer_Code',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Ship_To_Customer_Address_Code',

										'Value'		=>	$shippingAddressCode,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Bill_To_Customer_Address_Code',

										'Value'		=>	$billingAddressCode,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Tax_Amount',

										'Value'		=>	$orderObject->get_total_tax(),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Prepaid_Authorization',

										'Value'		=>	$Prepaid_Authorization,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'PO_No',

										'Value'		=>	$poNo,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Order_No_Prefix',

										'Value'		=>	'PWPU',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Carrier Code',

										'Value'		=>	'UNITED_PARCEL_SERVICE_INC',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Shipping_Service',

										'Value'		=>	$shipping_service,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Ship_From_Building_Code',

										'Value'		=>	"AND",

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Prepaid_Amount',

										'Value'		=>	$orderObject->get_total(),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Freight_Terms',

										'Value'		=>	$freightTerms,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Freight_Amount',

										'Value'		=>	$orderObject->get_total_shipping(),

										'Required'	=>	'false',

										'Output'	=>  'false'

									)


								



				);

	    	PlexLog::addLog(' Info =>('.$orderObject->get_user_id().') shipping cost ('.$orderObject->get_total_shipping().') ');
	    		
	    		foreach ($paramArray as $key => $value) {
    			PlexLog::addLog(' Info =>('.$orderObject->get_user_id().') order creation paramArray => '.$value["Name"].' : '.$value["Value"]);
	    			# code...
	    		}


		

			/* call the service*/

			$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_WRITE_NEW_ORDER, $paramArray);

			// echo "<pre>";

			// print_r($rawResponse);

			// echo "</pre>";

			/* get the order id from the raw response data */

			$plexOrderId = $responseHandlerObject->getOrderNoFromRawResponse($rawResponse);    	

	    	PlexLog::addLog(' Info =>('.$orderObject->get_user_id().') A new Plex Order is generated ('.$plexOrderId.') ');

	    	/* add all item present in the wooCommerce order object to plex order */

			$allItems = $orderObject->get_items();

			foreach ($allItems as $key => $value) {

				$productId 	= $value['product_id'];

				$Part_No 	= get_post_meta($productId, 'plexPartNo', true);

				$Quantity 	= $value['qty']; 

				/*$Price 		= get_post_meta($productId, '_regular_price', true);*/
				$lineTotalPrice = $value['line_total']; // total price /*  as we have to send discounted price to plex*/
				$Price = $lineTotalPrice/$Quantity;

				/* adding product to plex order*/

				$retVal = $this->addProductwithPlexOrder($plexOrderId,$orderObject,$Part_No,$Price,$Quantity);				

				PlexLog::addLog(' Info =>('.$orderObject->get_user_id().') Adding products('.$value['product_id'].') to Plex order( id:'.$plexOrderId.') : Resault='.$retVal.' ( Part_No = '.$Part_No.' ; Quantity = '.$Quantity.' ; Price='.$Price.') ');

			}



			return $plexOrderId;



		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while WriteNewPlexOrder. Message :'.$exec->getMessage());			

			return '';

		}

 	}





 	/* This function is responsible for adding each product with the plex order*/

 	private function addProductwithPlexOrder($plexOrderId,$orderObject,$Part_No,$Price,$Quantity){

 		try{

	 		$plexServiceRequestObject = new plexServiceRequest();

			$responseHandlerObject = new ResponseHandler(); 		

	 		

	 		$plexuserUserId = 'PWPU'.str_pad($orderObject->get_user_id(), 6, '0', STR_PAD_LEFT);

	 		/*request parameter*/

	 		$paramArray = array(

								array(

										'Name'		=>	'Customer_Code',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Order_No',

										'Value'		=>	$plexOrderId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Part_No',

										'Value'		=>	$Part_No,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Price',

										'Value'		=>	$Price,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Quantity',

										'Value'		=>	$Quantity,

										'Required'	=>	'false',

										'Output'	=>  'false'

									)

								



				);	 		



	 		/* adding product to plex order*/

			$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_WRITE_NEW_ORDER_LINE, $paramArray);

			// echo '<pre>';

			// print_r($rawResponse);

			// echo '</pre>';

			return $rawResponse->ExecuteDataSourceResult->Message;

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while addProductwithPlexOrder. Message :'.$exec->getMessage());			

			return '';

		}

 	}

 }



?>