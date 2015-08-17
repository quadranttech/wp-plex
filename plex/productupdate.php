<?php
	/* 
		This is the cron file.This will be run once a day. 
		This file is responsible for downloading all product info from plex
		and it will update woocommerce products withis these downloaded info  
	*/

	/* get all product details one by one and update woocommerce product metafields*/
	require_once('request.php');
	require_once('ResponseHandler.php');
	require_once('PlexDataSourceKey.php');
	require_once('plexlog.php');

	$plexServiceRequestObject = new plexServiceRequest();
	$responseHandlerObject = new ResponseHandler();

	/* Main Products */
	updateWoocommerceProductFromPlex( 'woo-commerce-product-name' , 'plex-pert-no' ,$plexServiceRequestObject , $responseHandlerObject);
	
	/*
		This function downloads  updated product info from plex; and update the info into corresponding woocommerce product.
	*/

	function updateWoocommerceProductFromPlex( $productName , $partNo ,$plexServiceRequestObject , $responseHandlerObject){
		try{
			$paramArray = array(
								array(
										'Name'		=>	'Part_No',
										'Value'		=>	''.$partNo,
										'Required'	=>	'false',
										'Output'	=>  'false'
									)								
				);			

			$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_PRODUCT_INFO, $paramArray);
			$plexProductResponse = $responseHandlerObject->getProductDataFromRawResponse($rawResponse);
	
			$ret = putPlexProductDataIntoWoocommerceProduct( $productName , $plexProductResponse);
	    	
		}catch(Exception $exec){

	    	PlexLog::addLog(' Error =>() Exception occure while updating product from plex. Message :'.$exec->getMessage());			
			return false;
		}
	}

function putPlexProductDataIntoWoocommerceProduct( $woocommerceProductName , $plexProductData ){
	
	if(0 == count($plexProductData)){ /* no data found */
		return;
	}
	try{

		global $wpdb;
		$post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$woocommerceProductName."'");
		
		if(!$post_id){
			$woocommerceProductObj = get_page_by_title( $woocommerceProductName, OBJECT, 'product' );
		 	$post_id =  $woocommerceProductObj->ID;
		 }		 	

		update_post_meta($post_id, 'plexPartKey', $plexProductData['Part_Key']);
		update_post_meta($post_id, 'plexPartNo', $plexProductData['Part_No']);
		update_post_meta($post_id, 'plexRevision', $plexProductData['Revision']);
		update_post_meta($post_id, 'plexPartName', $plexProductData['Part_Name']);
		update_post_meta($post_id, 'plexAttachmentHist', $plexProductData['Attachment_List']);
		update_post_meta($post_id, '_regular_price', $plexProductData['Price']);
		update_post_meta($post_id, 'plexPartStatus', $plexProductData['Part_Status']);
		update_post_meta($post_id, 'plexPartType', $plexProductData['Part_Type']);
		update_post_meta($post_id, 'plexCustomerPartNo', $plexProductData['Customer_Part_No']);
		update_post_meta($post_id, 'plexCustomerPartPrice', $plexProductData['Customer_Part_Price']);
		update_post_meta($post_id, 'plexImageName', $plexProductData['Image_Name']);		

		if($plexProductData['FG_Quantity'])		{
			update_post_meta($post_id, '_manage_stock', 'yes');
			update_post_meta($post_id, '_stock_status', 'instock');
		}	
		else{
			update_post_meta($post_id, '_stock_status', 'outofstock');			
		}

		update_post_meta($post_id, '_stock', $plexProductData['FG_Quantity']);

	}catch(Exception $exec){
    	PlexLog::addLog(' Error =>() Exception occure while putting product information in woo commerce . Message :'.$exec->getMessage());			
		return false;
	}

}

?>