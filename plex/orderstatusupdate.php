<?php
	require_once('request.php');
	require_once('ResponseHandler.php');
	require_once('PlexDataSourceKey.php');

	wp_head();

	$args = array(
		'posts_per_page' => -1,
		'post_type' => 'shop_order',
		'post_status' => 'publish'		
	);
	 
	 $starting_time = microtime(true);

	$orders=new WP_Query($args);
	$inc=0;
	if($orders->have_posts()):
	    while($orders->have_posts()): $orders->the_post();
			$order = new WC_Order($post->ID);
		
			if('processing' == $order->get_status()){
				echo '<br>';
				$inc++;
				$plexOrderId = get_post_meta( $post->ID, 'plexOrderId', true );
				if(''!=$plexOrderId){
					$orderStatus = getOrderStatusFromPlex($plexOrderId);
					echo 'orderStatus for plex order id='.$plexOrderId.' is = '.$orderStatus;
							
				}
				echo '<br>';
			}
			else{				
			
			}		
	    endwhile;
	endif;
	 
	$end_time = microtime(true);


/* This function will return the status string */
function getOrderStatusFromPlex($plexOrderId){
	$plexServiceRequestObject = new plexServiceRequest();
	$responseHandlerObject = new ResponseHandler();


	$paramArray = array(
							array(
									'Name'		=>	'Customer_Code',
									'Value'		=>	'',
									'Required'	=>	'false',
									'Output'	=>  'false'
								),
							array(
									'Name'		=>	'Email_Address',
									'Value'		=>	'',
									'Required'	=>	'false',
									'Output'	=>  'false'
								),
							array(
									'Name'		=>	'Order_No',
									'Value'		=>	$plexOrderId,
									'Required'	=>	'false',
									'Output'	=>  'false'
								)

			);

	$rawResponse = $plexServiceRequestObject->call('ExecuteDataSource' ,PLEX_GET_ORDER_STATUS, $paramArray);

	return $responseHandlerObject->getOrderStatusFromRawResponse($rawResponse);
		
}

?>