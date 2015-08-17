<?php

	/* this file will set all hooks with woocommerce those we need to give plex support */
	require_once('plexOrder.php');
	require_once('plexCustomer.php');
	require_once('plexlog.php');

	add_action( 'woocommerce_add_to_cart', 'trackAddToCarttest',10, 6);
    function trackAddToCarttest($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
		PlexLog::addLog(' Info =>('.get_current_user_id().') @trackAddToCart product_id ='.$product_id);        
    }    

	/* 
		on payment complete create a Plex Order
		while creating order check if billing and shipping address is present or not?, 
		if not present then add billing and shipping address to the customer. 
	*/

	add_action( 'woocommerce_thankyou', 'createAPlexOrder',10, 1);
	
    function createAPlexOrder( $order_id ) {

	    try{
	    	if ( ! empty( $order_id ) ) {

				PlexLog::addLog(' Info =>('.get_current_user_id().') order_id ='.$order_id);
				$savedPlexOrderId = get_post_meta( $order_id, 'plexOrderId', true );

				if(''!= $savedPlexOrderId){
					return false;
				}
	        	$plexOrderObject = new PlexOrder();
				$plexCustomerObj = new PlexCustomer();


	        	/* create order object from order id*/
				$wooCommerceOrderObject = new WC_Order( $order_id );

				if(!$wooCommerceOrderObject){
					PlexLog::addLog(' Error =>('.get_current_user_id().') unable to create wooCommerceOrderObject ');
	    			return;
				}

				$user_id = $wooCommerceOrderObject->get_user_id();

				if(!isset($user_id) || empty($user_id)){
					$user_id = plexuserGuestCheckoutUserCb($order_id);
				}

				if($user_id === false){
					PlexLog::addLog(' Error =>(' . $order_id . ') unable to create WP User ');
	    			return;
	    		}

				$plexCustomerObj->createNewPlexCustomerIfNotExists($user_id);

				$Prepaid_Authorization = get_post_meta($order_id, '_transaction_id', true);
				if('' == $Prepaid_Authorization){
					PlexLog::addLog(' Error =>('.$user_id.') unable to to read PrepaidAuthorization ');
				}

				$billingAddressCode='';
				$shippingAddressCode='';

				if(! $plexCustomerObj->isBillingAddressPresent($user_id) ){
					PlexLog::addLog(' Info =>('.$user_id.') billing address not present; hence adding new billing address ');
					$billingAddressCode = $plexCustomerObj->createBillingAddress($user_id);
				}
				else{
					$billingAddressCode = $plexCustomerObj->getBillingAddressCode($user_id);
				}

				if(! $plexCustomerObj->isShippingAddressPresent($user_id) ){
					PlexLog::addLog(' Info =>('.$user_id.') shipping  address not present; hence adding new shipping  address ');
					$shippingAddressCode = $plexCustomerObj->createShippingAddress($user_id);
				}
				else{
					$shippingAddressCode = $plexCustomerObj->getShippingAddressCode($user_id);
				}

			
				PlexLog::addLog(' Info =>('.$user_id.') billingAddressCode = '.$billingAddressCode);
		    	PlexLog::addLog(' Info =>('.$user_id.') shippingAddressCode = '.$shippingAddressCode);
		    	PlexLog::addLog(' Info =>('.$user_id.') Prepaid_Authorization = '.$Prepaid_Authorization);
		    	PlexLog::addLog(' Info =>('.$user_id.') shipping method  = '. str_replace(' ', '_', strtoupper ( $wooCommerceOrderObject->get_shipping_method() ) ) );

				$poNo = get_user_meta( $user_id, 'billing_first_name', true ).' '.get_user_meta( $user_id, 'billing_last_name', true );

				$plexOrderId = $plexOrderObject->WriteNewPlexOrder($wooCommerceOrderObject, $billingAddressCode, $shippingAddressCode,$Prepaid_Authorization,$poNo);

				if(''!=$plexOrderId){ 
					/* an order is successfully created*/
					/*  save the order id in post meta field */
					$metaid = update_post_meta($order_id, 'plexOrderId', $plexOrderId);
					PlexLog::addLog(' Info =>('.$user_id.') plexOrderId('.$plexOrderId.')  order_id('.$order_id.') ; metaid='.$metaid);

					/* send order invoice */
					sendCustomerInvoice($order_id);

					/* create a new order feed */

					$savedPlexOrderIdInStoreOrderPostType = get_post_meta( $order_id, 'plexOrderId', true ); 
					if($savedPlexOrderIdInStoreOrderPostType){
						PlexLog::addLog(' Info =>('.$user_id.') plexOrderId('.$savedPlexOrderIdInStoreOrderPostType.') successfully saved in Plex Order Id In Store Order Post Type');
					}
					else{
						PlexLog::addLog(' Info =>('.$user_id.') plexOrderId() orderid is not saved');
					}
				}

				if($user_id !==false){
					do_action('plexuser_order_invoice', $order_id);
				}
			}
	        else{
				PlexLog::addLog(' Error =>('.get_current_user_id().') order id not found ');
	        }

	    }catch(Exception $exec){

	    	PlexLog::addLog(' Error =>() Exception occure while createAPlexOrder. Message :'.$exec->getMessage());			

			return false;

		}

    }

    /* on user registration create a plex customer */
	add_action('user_register','createAPlexUser');

	function createAPlexUser($user_id){	
		$plexCustomerObj = new PlexCustomer();
		$plexCustomerObj->createNewPlexCustomerIfNotExists($user_id);

		$userobj = get_userdata( $user_id );
		$emailId = $userobj->user_email;
		$username = $userobj->user_login;
		$first_name = $userobj->first_name;
		if( isset($_SESSION["woocomGuestCheckOut"]) && 1==$_SESSION["woocomGuestCheckOut"] ){
			$_SESSION["woocomGuestCheckOut"]='0';
		}else{
			sendNewRegistrationMail( $first_name , $emailId ,$username);
		}

	}


	function createOrderFeed( ){
		$args = array(
                 'post_type' => 'shop_order',
                 'posts_per_page' => -1 ,
                // 'order' => 'DESC'

            );

        $list_of_order = get_posts( $args );    	
    	for($i=0;$i<count($list_of_order);$i++){
    	}
	}


/* code to show plex order id in woo commerce order table ( admin page ) */
/*1. Define columns position and names*/
add_filter( 'manage_edit-shop_order_columns', 'PLEX_ORDER_ID_COLUMNS_FUNCTION' ,20,1);
function PLEX_ORDER_ID_COLUMNS_FUNCTION($columns){
    $new_columns = (is_array($columns)) ? $columns : array();
    unset( $new_columns['order_actions'] );
    $new_columns['PLEX_ORDER_ID'] = 'PLEX_ORDER_ID';

  //  $new_columns['order_actions'] = $columns['order_actions'];
    return $new_columns;
}

/* 2. For each custom column, show the values */
add_action( 'manage_shop_order_posts_custom_column', 'PLEX_ORDER_ID_COLUMNS_VALUES_FUNCTION', 20,1 );
function PLEX_ORDER_ID_COLUMNS_VALUES_FUNCTION($column){
    global $post;
    //$data = get_post_meta( $post->ID );
    $plexOrderNo = get_post_meta( $post->ID, 'plexOrderId', true );
    if ( $column == 'PLEX_ORDER_ID' ) {    
        echo ($plexOrderNo);
    }
    
}

function plexuserGuestCheckoutUserCb($order_id = ''){
	try {
        if(!empty($order_id)){
        	$wooFactoryObject    = new WC_Order_Factory();

			$wooOrderObject      = $wooFactoryObject->get_order($order_id);

			$user_id 		     = $wooOrderObject->get_user_id();

			$update_customer 	 = false;

			if(empty($user_id)){
				$_billing_first_name = get_post_meta($order_id, '_billing_first_name', true);

				$_billing_first_name = (isset($_billing_first_name) && !empty($_billing_first_name)) ? trim($_billing_first_name) : ''; 


				$_billing_email 	 = get_post_meta($order_id, '_billing_email', true);

				$_billing_email      = (isset($_billing_email) && !empty($_billing_email)) ? trim($_billing_email) : '';


				if(!empty($_billing_email)){
					if(email_exists($_billing_email) == true){
						$user 		= get_user_by( 'email', $_billing_email );

						$unam       = (isset($user->user_login) && !empty($user->user_login)) ? $user->user_login : '';

						$user_id    = (isset($user->ID) && !empty($user->ID)) ? $user->ID : false;

						$update_customer = true;
					}
					else{
						$unam     = $_billing_email;

						$upass    = wp_generate_password(12, false);

						$_SESSION["woocomGuestCheckOut"] = "1";

						$user_id  = wp_create_user( $unam, $upass, $_billing_email );

						if(!empty($user_id)){
							$usrObj   = new WP_User($user_id);

							$usrObj->remove_role('subscriber');

							$usrObj->add_role('customer');

							$update_customer = true;
						}
						else{
							$user_id  = false;
						}
					}
				}
				else{
					$unam    = 'plexuser.guest_' . $order_id . '_' . time();

					$umail   = 'plexuser.guest_' . $order_id . '@plexuser.com';

					if ( email_exists($umail) == false ) {
						$upass    = wp_generate_password(12, false);

						$_SESSION["woocomGuestCheckOut"] = "1";

						$user_id  = wp_create_user( $unam, $upass, $umail );

						$usrObj   = new WP_User($user_id);

						$usrObj->remove_role('subscriber');

						$usrObj->add_role('customer');

						$update_customer = true;
					}
					else {
						$user_id  = false;
					}
				}
			}
			else{
				$user_id  = $user_id;
			}
			
			
			if($update_customer && !empty($user_id) && !empty($unam)){
				$custom_field_keys = get_post_custom_keys($order_id);

				if(!in_array('_customer_user', $custom_field_keys)){
					add_post_meta($order_id, '_customer_user', $user_id, false);
				}
				else{
					update_post_meta($order_id, '_customer_user', $user_id);
				}

				$pattern = '#(^_billing|^_shipping)#';

				foreach($custom_field_keys as $ky => $val){
					if(is_string($val)){
						$ord_meta = get_post_meta($order_id, $val, true);

						$ord_meta = (isset($ord_meta) && !empty($ord_meta)) ? trim($ord_meta) : ''; 

						if (preg_match($pattern, $val) === 1) {
	    					$xkey = trim($val, '_');

	    					if( get_user_meta($user_id, $xkey, true) == '' ){
	    						add_user_meta( $user_id, $xkey, $ord_meta, true );
	    					}
	    					else{
	    						update_user_meta( $user_id, $xkey, $ord_meta );
	    					}
						}
					}
				}
			}
		}
        else{
        	$user_id  = false;
        }

        return($user_id);
    } catch (Exception $e) {
        print($e->getFile() . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
    }
}

// http://thoughts.enseed.com/automatic-login-into-wordpress/
function plexuserGuestLogin($user_id, $user_login) {
    try{
		if (!is_user_logged_in()){
			wp_set_current_user($user_id, $user_login);

            wp_set_auth_cookie($user_id);

            do_action('wp_login', $user_login);
		}
	}
	catch(Exception $exec){
		PlexLog::addLog('Error =>(' . $customerId . ') Exception occure. Message : ' . $exec->getMessage() . "\n");			

		return '';
	}
}


add_action( 'woocommerce_email', 'unhook_woocommerce_order_related_emails' );
 
function unhook_woocommerce_order_related_emails( $email_class ) { 
	/**
	* Hooks for sending emails during store events
	**/
	remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
	remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
	remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );
	// New order emails
	remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	// Processing order emails
	remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
	// Completed order emails
	remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
	// Note emails
	remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
} 



/* customer invoice */
function sendCustomerInvoice($order_id){
	PlexLog::addLog(' Info =>@sendCustomerInvoice order_id='.$order_id);

	$wooCommerceOrderObject = new WC_Order( $order_id );
	$user_id = $wooCommerceOrderObject->get_user_id();
	$customerMailId = $wooCommerceOrderObject->billing_email;	

	$invoice_mail_content='';
	
	$invoice_mail_content.='
	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container">
		<tbody>
			<tr>
				<td align="center" valign="top">
					<font face="Arial" style="font-weight:bold;background-color:#8fd1c8;color:#202020;"></font>
					<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header" bgcolor="#8fd1c8">
						<tbody>
							<tr >
					            <td width="20" height="20"  bgcolor="#8fd1c8">&nbsp;</td>
					            <td bgcolor="#8fd1c8">&nbsp;</td>
					            <td width="20" bgcolor="#8fd1c8">&nbsp;</td>
					        </tr>
					        <tr >
					            <td width="20"  bgcolor="#8fd1c8">&nbsp;</td>
					            <td bgcolor="#8fd1c8" style="font-weight:bold;font-size:24px;color:#ffffff;" ><h2 style="margin:0;">Welcome to '.get_option( 'blogname' ).'! <br>Thank you for your order. We\'ll be in touch shortly with additional order and shipping information.</h2></td>
					            <td width="20" bgcolor="#8fd1c8">&nbsp;</td>
					        </tr>
					        <tr >
					            <td width="20" height="20"  bgcolor="#8fd1c8">&nbsp;</td>
					            <td bgcolor="#8fd1c8">&nbsp;</td>
					            <td width="20" bgcolor="#8fd1c8">&nbsp;</td>
					        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
            	<td align="center" valign="top">
            		<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
            			<tbody>
            				<tr>
            					<td valign="top">
            						<font style="background-color:#f9f9f9">
            							<table border="0" cellpadding="20" cellspacing="0" width="100%">
            								<tbody>
            									<tr>
	            									<td valign="top">
	            										<div>
	            											<font face="Arial" align="left" style="font-size:18px;color:#8a8a8a">
	            												<p>Your order #'.get_post_meta( $order_id, "plexOrderId", true ).' has been received and is now being processed. Your order details are shown below for your reference:</p>
																<table cellspacing="0" cellpadding="6" border="1" style="width:100%;">
																	<thead>
																		<tr>
																			<th scope="col">
																				<font align="left">Product</font>
																			</th>
																			<th scope="col">
																				<font align="left">Quantity</font>
																			</th>
																			<th scope="col">
																				<font align="left">Price</font>
																			</th>
																		</tr>
																	</thead>
																	<tbody>';

																		
																			$allItems = $wooCommerceOrderObject->get_items();
																			foreach ($allItems as $key => $value) {																				
																				$invoice_mail_content.='
																				<tr>
																					<td>
																						<font align="left">'. $value["name"].'<br><small></small></font>
																					</td>
																					<td>
																						<font align="left">'. $value["qty"].'</font>
																					</td>
																					<td>
																						<font align="left">
																							<span class="amount">$'. $value["line_subtotal"].'</span>
																						</font>
																					</td>
																				</tr>';
																				
																			}
																		$invoice_mail_content.='																		
																	</tbody>
																	<tfoot style="text-align: left;">
																		<tr>
																			<th scope="row" colspan="2">
																				<font align="left">Cart Subtotal:</font>
																			</th>
																			<td>
																				<font align="left">
																					<span class="amount">$'. sprintf('%0.2f', $wooCommerceOrderObject->get_subtotal() ).'</span>
																				</font>
																			</td>
																		</tr>';
																		if($wooCommerceOrderObject->get_total_discount() > 0.00 ){
																			$invoice_mail_content.='<tr>
																			<th scope="row" colspan="2">
																				<font align="left">Discount:</font>
																			</th>
																			<td>
																				<font align="left">
																					<span class="amount">$'. sprintf('%0.2f', $wooCommerceOrderObject->get_total_discount() ).'</span>
																				</font>
																			</td>
																		</tr>';

																		}

												$invoice_mail_content.='<tr>
																			<th scope="row" colspan="2">
																				<font align="left">Tax:</font>
																			</th>
																			<td>
																				<font align="left">
																					<span class="amount">$'. sprintf('%0.2f', ($wooCommerceOrderObject->get_cart_tax() + $wooCommerceOrderObject->get_shipping_tax() ) ).'</span>
																				</font>
																			</td>
																		</tr>
																		<tr>
																			<th scope="row" colspan="2">
																				<font align="left">Shipping:</font>
																			</th>
																			<td>
																				<font align="left">
																					<span class="amount">$'.$wooCommerceOrderObject->get_total_shipping().'</span>&nbsp;<small>via '. $wooCommerceOrderObject->get_shipping_method().'</small>
																				</font>
																			</td>
																		</tr>
																		<tr>
																			<th scope="row" colspan="2">
																				<font align="left">Payment Method:</font>
																			</th>
																			<td>
																				<font align="left">'. $wooCommerceOrderObject->payment_method_title.'</font>
																			</td>
																		</tr>
																		<tr>
																			<th scope="row" colspan="2">
																				<font align="left">Order Total:</font>
																			</th>
																			<td>
																				<font align="left">
																					<span class="amount">$'.sprintf('%0.2f', $wooCommerceOrderObject->get_total() ).'</span>
																				</font>
																			</td>
																		</tr>
																	</tfoot>
																</table>
																<h2>
																	<font face="Arial" align="left" style="font-weight:bold;font-size:30px;color:#6d6d6d">Customer details</font>
																</h2>
																<p><strong>Email:</strong> '. $customerMailId.'</p>
																<p><strong>Tel:</strong> '. $wooCommerceOrderObject->billing_phone.'</p>
																<table cellspacing="0" cellpadding="0" border="0">
																	<tbody>
																		<tr>
																			<td valign="top" width="50%">
																				<h3>
																					<font face="Arial" align="left" style="font-weight:bold;font-size:26px;color:#6d6d6d">Billing address</font>
																				</h3>
																				<p>'.$wooCommerceOrderObject->get_formatted_billing_address().'</p>

																			</td>
																			<td valign="top" width="50%">
																				<h3>
																					<font face="Arial" align="left" style="font-weight:bold;font-size:26px;color:#6d6d6d">Shipping address</font>
																				</h3>
																				<p>'. $wooCommerceOrderObject->get_formatted_shipping_address().'</p>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</font>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</font>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
						<tbody>
							<tr>
								<td valign="top">
	                                <table border="0" cellpadding="4" cellspacing="0" width="100%">
	                                	<tbody>
	                                		<tr>
	                                			<td colspan="2" valign="middle" id="credit" style="background-color: #777;">
	                                				<font face="Arial" align="center" style="font-size:12px;color:#bce3de">
	                                					<p>'.site_url().'</p>
	                                                </font>
	                                            </td>
	                                        </tr>
	                                    </tbody>
	                                </table>
	                            </td>
	                        </tr>
	                    </tbody>
	                </table>
	            </td>
	        </tr>
	    </tbody>
	</table>';

	
	$emailHeader = "Content-type: text/html";

	// To send HTML mail, the Content-type header must be set
	$emailHeader  = 'MIME-Version: 1.0' . "\r\n";
	$emailHeader .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// Additional emailHeader
	$emailHeader .= 'From: '.get_bloginfo( 'name' ).' <hello@plexuser.com>' . "\r\n";
	
	$emailSubject = "Your plexuser order receipt from ".date_i18n( wc_date_format(), strtotime( $wooCommerceOrderObject->order_date ) );
	wp_mail($customerMailId, $emailSubject, $invoice_mail_content, $emailHeader);

}

/* hook to update order status of current user */
add_action('plex_order_status_update', 'plex_order_status_update_callback_function',10,1);

function plex_order_status_update_callback_function($customer_orders){
	if(!$customer_orders){
		return false;
	}
	PlexLog::addLog(' Info =>going to update order status for user '.get_current_user_id());

	

	if(!is_array($customer_orders))
	{
		$customer_orders = array($customer_orders);
	}

	foreach ($customer_orders as $key => $woo_order) {
		$WC_OrderObj = new WC_Order($woo_order->ID);		 
		
		PlexLog::addLog(' Info =>going to update order status for orderid '.$WC_OrderObj->id);
		if( !('COMPLETED' == strtoupper ( $WC_OrderObj->get_status() ) || 'CANCELLED' == strtoupper ( $WC_OrderObj->get_status() ) ) ){
			$plexOrderId = get_post_meta( $WC_OrderObj->id, 'plexOrderId', true );
			if(''!=$plexOrderId){
				$orderStatus = getOrderStatusFromPlex($plexOrderId);
				PlexLog::addLog(' Info =>orderStatus for plex order id='.$plexOrderId.' is = '.$orderStatus);
				switch ($orderStatus) {
					case 'Closed':
						$WC_OrderObj->update_status('Completed');
						update_post_meta($WC_OrderObj->id, 'plexOrderId', $plexOrderId); 
						break;
					case 'Awaiting Approval':
					case 'Open':
					case 'Direct Open':
						$WC_OrderObj->update_status('processing');
						update_post_meta($WC_OrderObj->id, 'plexOrderId', $plexOrderId); 
						break;
					case 'Cancelled':
						$WC_OrderObj->update_status('Cancelled');
						update_post_meta($WC_OrderObj->id, 'plexOrderId', $plexOrderId); 
						break;
					case 'Consumer Service Hold':
					case 'Order Entry Hold':
					case 'Price Approval Hold':
					case 'Credit Hold':
					case 'Credit Card Hold':
					case 'Hold':
						$WC_OrderObj->update_status('on-hold');
						update_post_meta($WC_OrderObj->id, 'plexOrderId', $plexOrderId); 
						break;										
					default:						
						PlexLog::addLog(' Info =>order status unavailable');
						$WC_OrderObj->update_status('Cancelled');
						update_post_meta($WC_OrderObj->id, 'plexOrderId', $plexOrderId); 
				}		
			}
			else{
				PlexLog::addLog(' Info =>plexOrderId not found');
			}
		}
		else
		{
			PlexLog::addLog(' Info =>order status in not processing; it is :'.$WC_OrderObj->get_status());

		}

	}


}

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