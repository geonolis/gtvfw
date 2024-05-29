<?php

	/**
	* The core functionality of the plugin.
	*
	* @link       https://github.com/geonolis
	* @since      1.0.0
	*
	* @package    	
	* @subpackage Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3/admin
	*/

	/**
	* The core functionality of the plugin.
	*
	* Connects to GT Web Services and Creates the Voucher.
	*
	* @package    Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3
	* @subpackage Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3/admin
	* @author     Γεώργιος Παπαμανώλης <geonolis@hotmail.com>
	*/

class GTVFW {
	private $gt_api_object;

	public function __construct( GT_API $gt_api_object ){
		$this->gt_api_object=$gt_api_object;
	}		


	public function is_method( $order_id) {
		$order=wc_get_order( $order_id );
		$ship_methods=$order->get_shipping_methods() ;
		if (! reset($ship_methods)) return false;
		$ship_method= reset( $ship_methods )->get_method_id();
		if( in_array(  $ship_method, $this->gt_api_object->get_methods() )){
			return true ;
		}else{
			return false;
		}
	}

	// Βασική διαδικασία που θα καλείται όταν ΟΛΟΚΛΗΡΩΝΕΤΑΙ η παραγγελία:
	function gtvfw_create_voucher( $order_id ) {
		//get order
		$order =  wc_get_order($order_id);
		//get payment method
		$payment_method_name= $order->get_payment_method();
		//get order weight - volume	
		$courier_wvolume = 0 ;
		foreach ( $order->get_items() as $item_id => $item ) {
			// TODO get_the_category_by_ID($item->get_category_ids());
			$courier_wvolume += $item->get_quantity() * array_product($item->get_product()->get_dimensions(false))/5000;
		}
		$courier_wvolume=round($courier_wvolume);
		//  αν δεν έχει συμπληρωθεί βάρος, βάλε -2- 
		$courier_wvolume=max($courier_wvolume,2);
		$order->update_meta_data( 'courier_wvolume', $courier_wvolume );
//		$courier_wvolume = get_post_meta( $order_id, 'courier_wvolume', true ) ; 

		// Αρχικοποίηση ΥΠΗΡΕΣΙΩΝ και ΠΟΣΟΥ ΑΝΤΙΚΑΤΑΒΟΛΗΣ

		$services ="";
		$CodAmount =0;
		//check if cash on delivery
		if(strcmp($payment_method_name,'cod')==0)
		{
		$services = 'αμ';
		$CodAmount=$order->get_total();
		}

		$last_name=$order->get_shipping_last_name();
		$first_name=$order->get_shipping_first_name();
		$name = $last_name.' '.$first_name;
		$address = $order->get_shipping_address_1() . ', '. $order->get_shipping_address_2();
		$city = $order->get_shipping_city();
		$phone = $order->get_shipping_phone() ?? $order->get_billing_phone();
		$weight =  $courier_wvolume ; 
		$pieces = 1;
		$zip= $order->get_shipping_postcode();	
		$message =$order->get_customer_note();
		$ReceivedDate= date("Y-m-d");  
		//create voucher data
		$oVoucher = array(	'OrderId' => '#' . $order_id,
							'Name' => $name,
							'Address' => $address,
							'City' => $city,
							'Telephone' => $phone,
							'Zip' => $zip,
							'Destination' => "",
							'Courier' => "",
							'Pieces' => $pieces,
							'Weight' => $weight,
							'Comments' => $message,
							'Services' => $services ,
							'CodAmount' => $CodAmount,
							'InsAmount' => 0,
							'VoucherNumber' => "",
							'SubCode' => "",
							'BelongsTo' => "",
							'DeliverTo' => "",
							'ReceivedDate' => $ReceivedDate	);
		// κλήση του API
		$voucher=$this->gt_api_object->get_gt_voucher($oVoucher);

		if (is_numeric($voucher)) {
					// αποθήκευσε τον αριθμό αποστολής σε meta-data
			$order->update_meta_data( 'courier_voucher', $voucher );
					// αποθήκευσε την παραγγελία για να κρατηθεί ο αριθμός αποστολής
			$order->save();	
					// πρόσθεσε σημείωση με τον αριθμό αποστολής και με link για την εκτύπωση του voucher (PDF)
			$order->add_order_note(__('Job was sent successfully to Gen. Taxydromiki, Voucher number is '.$voucher .' </br><a target="_blank" href="'. $this->gt_api_object->get_voucher_url($voucher) . '">Print</a>', ''));
		} else {
			$order->add_order_note(__('Order not sent to Geniki Taxydromiki due to authentication failure 1', ''));
		} 		
		
	} // τέλος function woocommerce_create_gt_voucher() 

} //class