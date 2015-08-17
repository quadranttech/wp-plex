<?php
require_once('request.php');
require_once('ResponseHandler.php');
require_once('PlexDataSourceKey.php');
require_once('plexlog.php');

class PlexCustomer{

	/* 
		this Methods only creats  an new customer in plex with basic customer information;
		It doesn't add shipping address or billing address; 
		it neighter checks whether a customer of the provided id is already present or not;
		If an customer with same id is found then it returnd 101 response code , means duplicate data; 
	*/

	private function createNewPlexCustomer( $customerId ){
		try{
			$plexServiceRequestObject = new plexServiceRequest();
			$responseHandlerObject = new ResponseHandler();
	 		$user_info = get_userdata($customerId);
	 		$plexuserUserId = 'PWPU'.str_pad($customerId, 6, '0', STR_PAD_LEFT);

	 		/*request parameter*/
	 		$paramArray = array(

								array(

										'Name'		=>	'PCN',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Customer_Code',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Name',

										'Value'		=>	get_user_meta( $customerId, 'billing_first_name', true ).' '.get_user_meta( $customerId, 'billing_last_name', true ),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Customer_Type',

										'Value'		=>	'Consumer',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Phone',

										'Value'		=>	get_user_meta( $customerId, 'billing_phone', true ),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Email',

										'Value'		=>	$user_info->data->user_email,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Customer_Category',

										'Value'		=>	'Customer',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Terms',

										'Value'		=>	'Credit Card Web',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Customer_Since',

										'Value'		=>	date('m/d/Y',strtotime($user_info->data->user_registered)),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),								

								array(

										'Name'		=>	'Drop_Ship_Email',

										'Value'		=>	'1',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Customer_Parent_Code',

										'Value'		=>	'Consumer Parent',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'resultcode',

										'Value'		=>	'',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'ResultMessage',

										'Value'		=>	'',

										'Required'	=>	'false',

										'Output'	=>  'false'

									)

				);

			foreach ($paramArray as $key => $value) {
    			PlexLog::addLog(' Info =>() customer creation paramArray => '.$value["Name"].' : '.$value["Value"]);
	    		}

			/* creating new customer in plex*/
			$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_WRITE_NEW_CUSTOMER, $paramArray);

			PlexLog::addLog(' Info =>('.$customerId.') A new Plex customer added ('.$plexuserUserId.') ');

			return $rawResponse;

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure whilecreating new plex customer. Message :'.$exec->getMessage());

			return 0;

		}

	}



	public function createNewPlexCustomerTEST( $customerId ){

		try{

			$plexServiceRequestObject = new plexServiceRequest();

			$responseHandlerObject = new ResponseHandler();

	 		$user_info = get_userdata($customerId);

	 		$plexuserUserId = 'PWPU'.str_pad($customerId, 6, '0', STR_PAD_LEFT);

	 		/*request parameter*/

	 		$paramArray = array(

								array(

										'Name'		=>	'PCN',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Customer_Code',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Name',

										'Value'		=>	get_user_meta( $customerId, 'billing_first_name', true ).' '.get_user_meta( $customerId, 'billing_last_name', true ),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Customer_Type',

										'Value'		=>	'Consumer',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Phone',

										'Value'		=>	get_user_meta( $customerId, 'billing_phone', true ),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Email',

										'Value'		=>	$user_info->data->user_email,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Customer_Category',

										'Value'		=>	'Customer',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Terms',

										'Value'		=>	'Credit Card Web',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Customer_Since',

										'Value'		=>	date('m/d/Y',strtotime($user_info->data->user_registered)),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),								

								array(

										'Name'		=>	'resultcode',

										'Value'		=>	'',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'ResultMessage',

										'Value'		=>	'',

										'Required'	=>	'false',

										'Output'	=>  'false'

									)

				);



			/* creating new customer in plex*/

			$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_WRITE_NEW_CUSTOMER, $paramArray);

	    	PlexLog::addLog(' Info =>('.$customerId.') A new Plex customer added ('.$plexuserUserId.') ');

			return $rawResponse;

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure whilecreating new plex customer. Message :'.$exec->getMessage());

			return 0;

		}

	}

	/*
		This function checks if any customer already exists with the given id or not,
		if customer already exists then it reterns a blank string;
		else it creats a new customer and returns the plex customer id;
	*/

	public function createNewPlexCustomerIfNotExists( $customerId ){

		try{

			$success =  $this->updatePlexCustomer($customerId);

			if(!$success){

				/* user not exists , hence create a new customer*/

				$this->createNewPlexCustomer( $customerId );
	 			$plexuserUserId = 'PWPU'.str_pad($customerId, 6, '0', STR_PAD_LEFT);

				return $plexuserUserId;

			}
			else{

	    		PlexLog::addLog(' Info =>('.$customerId.') Plex customer already exists found while adding a new customer ');
	    		return '' ;

			}

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while createNewPlexCustomerIfNotExists. Message :'.$exec->getMessage());			
			return '';

		}

	}


	/*

		This method is responsible for updating an existing plex customer's informsation;
		if user doesn't exists or any failure to update the user, it returns false;
		else for successfully update it returns true;	
	*/

	public function updatePlexCustomer($customerId){

		try{

			$plexServiceRequestObject = new plexServiceRequest();

			$responseHandlerObject = new ResponseHandler();

	 		$user_info = get_userdata($customerId);

	 		$plexuserUserId = 'PWPU'.str_pad($customerId, 6, '0', STR_PAD_LEFT);

	 		
	 		/*request parameter*/

	 		$paramArray = array(								

								array(

										'Name'		=>	'Customer_Code',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Name',

										'Value'		=>	get_user_meta( $customerId, 'billing_first_name', true ).' '.get_user_meta( $customerId, 'billing_last_name', true ),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Phone',

										'Value'		=>	get_user_meta( $customerId, 'billing_phone', true ),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Email',

										'Value'		=>	$user_info->data->user_email,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'resultcode',

										'Value'		=>	'',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'ResultMessage',

										'Value'		=>	'',

										'Required'	=>	'false',

										'Output'	=>  'false'

									)

				);			

			foreach ($paramArray as $key => $value) {
    			PlexLog::addLog(' Info =>() customer update paramArray => '.$value["Name"].' : '.$value["Value"]);
	    		}

			/* updating plex customer information */

			$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_UPDATE_CUSTOMER, $paramArray);

		

			$errno = $rawResponse->ExecuteDataSourceResult->ErrorNo;

			if(200==$errno){ /* success */

	    		PlexLog::addLog(' Info =>('.$customerId.') Plex customer update successfully ');

	    		return true;

			}

			else{	/* failure*/		

	    		PlexLog::addLog(' Info =>('.$customerId.') Plex customer update failed ; due to '.$rawResponse->ExecuteDataSourceResult->Message);

				return false;

			}

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while Updating Plex customer . Message :'.$exec->getMessage());			

			return false;

		}

	}



	/* 
		this function adds billing and shipping address to the plex customer;
		use this method only if billing and shipping address is same for a customer...
	*/

	public function createBillingAndShippingAddress($customerId){

		try{

	 		$user_info = get_userdata($customerId);



	 		/* reading user's billing and shipping address from user meta data*/

			$Address 	= get_user_meta( $customerId, 'billing_address_1', true );

			$Address 	.= ' '.get_user_meta( $customerId, 'billing_address_2', true );

			$City 		= get_user_meta( $customerId, 'billing_city', true );

			$State 		= get_user_meta( $customerId, 'billing_state', true );

			$Country 	=get_user_meta( $customerId, 'billing_country', true );

			$Zip 		= get_user_meta( $customerId, 'billing_postcode', true );



			/* adding billibg and shipping address to a customer*/

			$returnVal =  $this->createPlexBillingAndOrShippingAddress($customerId,1,1,$Address,$City,$Country,$State,$Zip);

			if($returnVal == ' '){ /* failure*/

	    		PlexLog::addLog(' Error =>('.$customerId.') failed add Billing And Shipping address  ');

			}

			else{ /* success */

	    		PlexLog::addLog(' Info =>('.$customerId.') successfully added Billing And Shipping address ('.$returnVal.') ');

			}

			return $returnVal; /* returning the address code */

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while creating billing and shipping address. Message :'.$exec->getMessage());			

			return '';

		}

	}


	/* 
		this function adds billing address to the plex customer;
		use this method only if billing and shipping address are different...
	*/

	public function createBillingAddress($customerId){

		try{

			$user_info = get_userdata($customerId);

			/* reading user's billing address from user meta data*/

			$Address 	= get_user_meta( $customerId, 'billing_address_1', true );

			$Address 	.= ' '.get_user_meta( $customerId, 'billing_address_2', true );

			$City 		= get_user_meta( $customerId, 'billing_city', true );

			$State 		= get_user_meta( $customerId, 'billing_state', true );

			$Country 	=get_user_meta( $customerId, 'billing_country', true );

			$Zip 		= get_user_meta( $customerId, 'billing_postcode', true );

			/* adding billibg address to a customer*/

			$returnVal = $this->createPlexBillingAndOrShippingAddress($customerId,1,0,$Address,$City,$Country,$State,$Zip);

			if($returnVal == ''){ /* failure*/

	    		PlexLog::addLog(' Error =>('.$customerId.') failed add Billing address  ');

			}

			else{	/*success*/

	    		PlexLog::addLog(' Info =>('.$customerId.') successfully added Billing address ('.$returnVal.') ');

			}

			return $returnVal;/* returning the address code */

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while creating Billing. Message :'.$exec->getMessage());			

			return '';

		}

	}



	/* 
		this function adds shipping address to the plex customer;
		use this method only if billing and shipping address are different...
	*/

	public function createShippingAddress($customerId){

		try{

			$user_info = get_userdata($customerId);

			/* reading user's shipping address from user meta data*/
			$Address 	= get_user_meta( $customerId, 'shipping_address_1', true );
			$Address 	.= ' '.get_user_meta( $customerId, 'shipping_address_2', true );
			$City 		= get_user_meta( $customerId, 'shipping_city', true );
			$State 		= get_user_meta( $customerId, 'shipping_state', true );
			$Country 	=get_user_meta( $customerId, 'shipping_country', true );
			$Zip 		= get_user_meta( $customerId, 'shipping_postcode', true );

			/* adding billibg address to a customer*/
			$returnVal = $this->createPlexBillingAndOrShippingAddress($customerId,0,1,$Address,$City,$Country,$State,$Zip);
			if($returnVal == ''){ /* failure*/
	    		PlexLog::addLog(' Error =>('.$customerId.') failed add Shipping address  ');
			}

			else{	/*success*/
	    		PlexLog::addLog(' Info =>('.$customerId.') successfully added Shipping address ('.$returnVal.') ');
			}

			return $returnVal;/* returning the address code */

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while creating shipping address . Message :'.$exec->getMessage());			
			return '';
		}

	}



	/*
		this methods actually creats billoing and/or shipping address depending on the parameters;
		if $billtoValue is set to 1 and $shiptoValue is set to 0 then billing address will be created;
		else if $billtoValue is set to 0 and $shiptoValue is set to 1 then shipping address will be created;
		else if both $billtoValue and $shiptoValue is set to 1 then both the address will be created;
		else no address will be created;

	*/

	private function createPlexBillingAndOrShippingAddress($customerId,$billtoValue,$shiptoValue,$Address,$City,$Country,$State,$Zip){



		if( 0 == $billtoValue && 0 == $shiptoValue){

    		PlexLog::addLog(' Info =>('.$customerId.') returning from createPlexBillingAndOrShippingAddress as both billtoValue and shiptoValue is set to 0');

			return '';

		}
		try{

			if('US' == $Country){
				$Country='USA';
			}
			

	 		$plexuserUserId = 'PWPU'.str_pad($customerId, 6, '0', STR_PAD_LEFT);
	 		$plexuseraddressId = 'PWPA'.$customerId.'ADDR_'.$billtoValue.'_'.$shiptoValue;
	 		
	 		if($billtoValue){
				$latestaddresscode = get_user_meta( $customerId, 'latestPlexBillingAddressCode', true );
				$tmp_arr  = explode('_', $latestaddresscode);
				if( 3 == count($tmp_arr) ){
					$plexuseraddressId.='_1';
				}
				else if( 4 == count($tmp_arr)){
					$tmp_index = (int) $tmp_arr[3]; 
					$tmp_index++;
					$plexuseraddressId.='_'.$tmp_index;
				}

			}

			if($shiptoValue){
				$latestaddresscode = get_user_meta( $customerId, 'latestPlexShippingAddressCode', true );
				$tmp_arr  = explode('_', $latestaddresscode);
				if( 3 == count($tmp_arr) ){
					$plexuseraddressId.='_1';
				}
				else if( 4 == count($tmp_arr)){
					$tmp_index = (int) $tmp_arr[3]; 
					$tmp_index++;
					$plexuseraddressId.='_'.$tmp_index;
				}
			}

			PlexLog::addLog(' Info =>('.$customerId.') going to add address in plec; address code : '.$plexuseraddressId);


	 		$user_info = get_userdata($customerId);
	 		/*request parameter*/

			$paramArray = array(								

								array(

										'Name'		=>	'Customer_Code',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'true',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Customer_Address_Code',

										'Value'		=>	$plexuseraddressId,

										'Required'	=>	'true',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Ship_To',

										'Value'		=>	$shiptoValue,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Bill_To',

										'Value'		=>	$billtoValue,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Active',

										'Value'		=>	true,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Address',

										'Value'		=>	$Address,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'City',

										'Value'		=>	$City,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Country',

										'Value'		=>	$Country,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'State',

										'Value'		=>	$State,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Zip',

										'Value'		=>	$Zip,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'resultcode',

										'Value'		=>	'',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'ResultMessage',

										'Value'		=>	'',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Residential',

										'Value'		=>	'1',

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Customer_Address_Name',

										'Value'		=>	get_user_meta( $customerId, 'shipping_first_name', true ).' '.get_user_meta( $customerId, 'shipping_last_name', true ),

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Email',

										'Value'		=>	$user_info->data->user_email,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Phone',

										'Value'		=>	get_user_meta( $customerId, 'billing_phone', true ),

										'Required'	=>	'false',

										'Output'	=>  'false'

									)

				);



			

	 		$plexServiceRequestObject = new plexServiceRequest();

			$responseHandlerObject = new ResponseHandler();



			/* sending request to create  billing and shipping address */

			$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_WRITE_NEW_BILL_TO_SHIP_TO_ADDRESS, $paramArray);


			/* As in plex we have to store more than one address of shipping and billing address, store the latest address code .. */
			if($rawResponse->ExecuteDataSourceResult->Message=='Success'){
				if($billtoValue){
					update_user_meta( $customerId, 'latestPlexBillingAddressCode', $plexuseraddressId );
				}

				if($shiptoValue){
					update_user_meta( $customerId, 'latestPlexShippingAddressCode', $plexuseraddressId );
				}

				return $plexuseraddressId;
			}
			else{
				return '';
			}

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while sending plex request to create billing and shipping address. Message :'.$exec->getMessage());			

			return '';

		}

	}


	/*
		this method  returnns billing address of the given user  , if billing address is assign to that user;
		elae it returns a blank array;

	*/



	public function getBillingAddress($customerId){

		try{

			$plexServiceRequestObject = new plexServiceRequest();

			$responseHandlerObject = new ResponseHandler();



	 		$plexuserUserId = 'PWPU'.str_pad($customerId, 6, '0', STR_PAD_LEFT);

			/**/

	 		$paramArray = array(								

								array(

										'Name'		=>	'Customer_Code',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Ship_To',

										'Value'		=>	0,		/* shipping to = 0 means looking for billing address*/

										'Required'	=>	'false',

										'Output'	=>  'false'

									)

								

				);



			$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_GET_SHIP_TO_BILL_TO_ADDRESS, $paramArray);

			/*
				return data format same as getShippingAddress;

			*/

			return $responseHandlerObject->getCustomerAddressResponse($rawResponse);

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while reading billing address. Message :'.$exec->getMessage());			

			return array();

		}

	}



	/*
		this method  returnns shipping address of the given user  , if shipping address is assign to that user;
		elae it returns a blank array;
	*/

	public function getShippingAddress($customerId){

		try{

			$plexServiceRequestObject = new plexServiceRequest();

			$responseHandlerObject = new ResponseHandler();

	 		$plexuserUserId = 'PWPU'.str_pad($customerId, 6, '0', STR_PAD_LEFT);
	 		$user_info = get_userdata($customerId);


	 		$paramArray = array(								

								array(

										'Name'		=>	'Customer_Code',

										'Value'		=>	$plexuserUserId,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),

								array(

										'Name'		=>	'Ship_To',

										'Value'		=>	1,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Email',

										'Value'		=>	$user_info->data->user_email,

										'Required'	=>	'false',

										'Output'	=>  'false'

									),
								array(

										'Name'		=>	'Phone',

										'Value'		=>	get_user_meta( $customerId, 'billing_phone', true ),

										'Required'	=>	'false',

										'Output'	=>  'false'

									)
				);



			$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_GET_SHIP_TO_BILL_TO_ADDRESS, $paramArray);

			$ret =  $responseHandlerObject->getCustomerAddressResponse($rawResponse);

			return $ret;

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while reading shipping address. Message :'.$exec->getMessage());			
			return array();
		}

	}

	/*
		this Method returns the Billing Address Code; if billing address is assigned to the user
		else return blank string

	*/


	public function getBillingAddressCode($customerId){

		try{

			$response = $this->getBillingAddress($customerId);

			foreach ($response as $address) {

				$address_address='';

				$address_zip='';

				$Customer_Address_Code='';

				foreach ($address as $key => $value) {

					if($value->Name == 'Address'){
						$address_address = $value->Value;
					}

					if($value->Name == 'Zip'){
						$address_zip = $value->Value;
					}



					if($value->Name == 'Customer_Address_Code'){
						$Customer_Address_Code = $value->Value;
					}

				}



				$current_address = trim(get_user_meta( $customerId, 'billing_address_1', true ).' '.get_user_meta( $customerId, 'billing_address_2', true ) );

				$current_zip = trim(get_user_meta( $customerId, 'billing_postcode', true ) );



				if(trim($address_address) == $current_address && $address_zip == $current_zip){

					return $Customer_Address_Code;

				}

				else{

				}

			}

	    	return $this->createBillingAddress($customerId);

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while reading billing code. Message :'.$exec->getMessage());			

			return '';
		}

	}


	/*
		this Method returns the Shipping Address Code; if shipping address is assigned to the user
		else return blank string
	*/

	public function getShippingAddressCode($customerId){

		try{

			$response = $this->getShippingAddress($customerId);		

			foreach ($response as $address) {

				$address_address='';

				$address_zip='';

				$Customer_Address_Code='';

				foreach ($address as $key => $value) {

					if($value->Name == 'Address'){

						$address_address = $value->Value;

					}



					if($value->Name == 'Zip'){

						$address_zip = $value->Value;

					}



					if($value->Name == 'Customer_Address_Code'){

						$Customer_Address_Code = $value->Value;

					}

				}



				$current_address =trim( get_user_meta( $customerId, 'shipping_address_1', true ).' '.get_user_meta( $customerId, 'shipping_address_2', true ) );

				$current_zip =trim( get_user_meta( $customerId, 'shipping_postcode', true ) );

				$str = "current_address<".$current_address."> address_address<".$address_address."> current_zip<".$current_zip."> address_zip<".$address_zip."> ";
				PlexLog::addLog(' info =>('.$customerId.') '.$str );

				if(trim($address_address) == $current_address && $address_zip == $current_zip){

					return $Customer_Address_Code;

				}

				else{

				}

			}

	    	return $this->createShippingAddress($customerId);

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while reading shipping code. Message :'.$exec->getMessage());			

			return '';

		}

	}

	/*
		this method returnd true if the billing address present against the customer;
		else returns false;
	*/

	public function isBillingAddressPresent($customerId){

		try{

			$response = $this->getBillingAddress($customerId);

			if(count($response)){ return true;}

			else{return false;}

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while checkimg if billing address exists. Message :'.$exec->getMessage());			

			return false;

		}

	}


	/*
		this method returnd true if the shipping address present against the customer;
		else returns false;
	*/

	public function isShippingAddressPresent($customerId){

		try{

			$response = $this->getShippingAddress($customerId);

			if(count($response)){ return true;}

			else{return false;}

		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>('.$customerId.') Exception occure while checkimg if shipping address exists. Message :'.$exec->getMessage());			

			return false;

		}

	}

}

?>