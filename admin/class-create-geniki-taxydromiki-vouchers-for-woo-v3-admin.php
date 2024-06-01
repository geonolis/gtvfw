<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/geonolis
 * @since      1.0.0
 *
 * @package    	
 * @subpackage Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3
 * @subpackage Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3/admin
 * @author     Γεώργιος Παπαμανώλης <geonolis@hotmail.com>
 */
class Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private string $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private string $version;

	/**
	 * The GT connection API object
	 * It is referenced as class private property, to be created once and accessed from all class methods
	 * @since    1.0.0
	 * @access   private
	 * @var      GT_API    $gt_api    The API connection object.
	 */
	private GT_API $gt_api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/create-geniki-taxydromiki-vouchers-for-woo-v3-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Create_Geniki_Taxydromiki_Vouchers_For_Woo_V3_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/create-geniki-taxydromiki-vouchers-for-woo-v3-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add a Settings menu
	 *
	 * @since  1.0.0
	 */
	public function addPluginAdminMenu() {
//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page(  $this->plugin_name, 'Γεν. Ταχυδρομική', 'administrator', 'gtvfw_settings', array( $this, 'displayPluginAdminSettings' ), 
			plugin_dir_url( __FILE__ ) . 'images/gt-icon.png', 26 );
	}

	public function displayPluginAdminSettings() {
         // set this var to be used in the settings-display view
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
		if(isset($_GET['error_message'])){
			add_action('admin_notices', array($this,'pluginNameSettingsMessages'));
			do_action( 'admin_notices', $_GET['error_message'] );
		}
		require_once 'partials/'.$this->plugin_name.'-admin-display.php';
	}

	public function pluginNameSettingsMessages($error_message){
		switch ($error_message) {
			case '1':
			$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 
			$err_code = esc_attr( 'plugin_name_example_setting' );                 
			$setting_field = 'plugin_name_example_setting';                 
			break;
		}
		$type = 'error';
		add_settings_error(
			$setting_field,
			$err_code,
			$message,
			$type
		);
	}

	public function registerAndBuildFields() {
         /**
        * First, we add_settings_section. This is necessary since all future settings must belong to one.
        * Second, add_settings_field
        * Third, register_setting
        */     
    // Add the section to reading settings so we can add our
 	// fields to it
    // add_settings_section(a string $id, string $title, callable $callback, string $page, array $args = array() )
         add_settings_section(
         	'gtvfw_settings_section',
         	'GT connection settings section',
         	array( $this, 'gtvfw_settings_section_callback_function'),
         	'gtvfw_settings'
         );

 	// 1.option: Geniki Taxydromiki Web Services User Name

 	// ορίζω το array που θα περάσει ως παράμετρος στην callback, που θα σχηματίσει το input:
         unset($args);
         $args = array (
         	'type'      => 'input',
         	'subtype'   => 'text',
         	'id'    => 'gt_username',
         	'name'      => 'gtvfw_settings',
         	'required' => 'true',
         	'get_options_list' => '',
         	'value_type'=>'normal',
         	'wp_data' => 'option',
         	'default'=> 'name'
         );
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	// add_settings_field( string $id, string $title, callable $callback, string $page, string $section = ‘default’, array $args = array() )
         add_settings_field(
         	'gt_username',
         	'ΓΤ UserName',
         	array( $this, 'gtvfw_setting_callback_render_function'),
         	'gtvfw_settings',
         	'gtvfw_settings_section',
         	$args
         ); 

// 2.option: Geniki Taxydromiki Web Services Password
         unset($args);
         $args = array (
         	'type'      => 'input',
         	'subtype'   => 'text',
         	'id'    => 'gt_password',
         	'name'      => 'gtvfw_settings',
         	'required' => 'true',
         	'get_options_list' => '',
         	'value_type'=>'normal',
         	'wp_data' => 'option',
         	'default'=> 'Password'
         );

         add_settings_field(
         	'gt_password',
         	'ΓΤ Password',
         	array( $this, 'gtvfw_setting_callback_render_function'),
         	'gtvfw_settings',
         	'gtvfw_settings_section',
         	$args
         );

 // 3.option: Geniki Taxydromiki Web Services App Key
         unset($args);
         $args = array (
         	'type'      => 'input',
         	'subtype'   => 'text',
         	'id'    => 'gt_appkey',
         	'name'      => 'gtvfw_settings',
         	'required' => 'true',
         	'get_options_list' => '',
         	'value_type'=>'normal',
         	'wp_data' => 'option',
         	'default'=> 'key'
         );

         add_settings_field(
         	'gt_appkey',
         	'ΓΤ App Key',
         	array( $this, 'gtvfw_setting_callback_render_function'),
         	'gtvfw_settings',
         	'gtvfw_settings_section',
         	$args
         );

// 4.option: Test or Production CheckBox 
         unset($args);
         $args = array (
         	'type'      => 'input',
         	'subtype'   => 'checkbox',
         	'id'    => 'gt_testmode',
         	'name'      => 'gtvfw_settings',
         	'required' => 'true',
         	'get_options_list' => '',
         	'value_type'=>'normal',
         	'wp_data' => 'option',
         	'default'=> '0'
         );

         add_settings_field(
         	'gt_testmode',
         	'ΓΤ Test Mode',
         	array( $this, 'gtvfw_setting_callback_render_function'),
         	'gtvfw_settings',
         	'gtvfw_settings_section',
         	$args
         );

// 5.option: Shipping options Multi Select List
         unset($args);
//         global $woocommerce;
//       $woocommerce->shipping->load_shipping_methods();
         $shipping_methods=array();
         foreach (WC()->shipping->get_shipping_methods() as $method){
         	$shipping_methods[]=$method->id;
//         		echo $method->id; $method->title
         }

         
         $args = array (
         	'type'      => 'input',
         	'subtype'   => 'multiselect',
         	'id'    => 'gt_methods',
         	'name'      => 'gtvfw_settings',
         	'required' => 'true',
         	'get_options_list' => $shipping_methods,
         	'value_type'=>'normal',
         	'wp_data' => 'option',
         	'default'=> array()
         );

		add_settings_field(
			'gt_methods',
			'ΓΤ Μέθοδοι Αποστολής',
			array( $this, 'gtvfw_setting_callback_render_function'),
			'gtvfw_settings',
			'gtvfw_settings_section',
			$args
		);

 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	// register_setting( string $option_group, string $option_name, array $args = array() )
		register_setting( 'gtvfw_settings', 'gtvfw_settings');

	}

/**
	Function to validate - needs improvement
     function validateTxt($input) {
	// Check option field contains no HTML tags - if so strip them out
	$input['text_string'] =  wp_filter_nohtml_kses($input['text_string']);	
	return $input; // return validated input
}
*/

	public function gtvfw_settings_section_callback_function() {
	echo '<p>Συμπληρώστε τα στοιχεία που σας έχει δώσει η Γενική Ταχυδρομική για πρόσβαση στα Web Services.</p>';
} 

	public function gtvfw_setting_callback_render_function( $args ) {
 /* EXAMPLE INPUT
								'type'      => 'input',
								'subtype'   => '',
								'id'    => $this->plugin_name.'_example_setting',
								'name'      => $this->plugin_name.'_example_setting',
								'required' => 'required="required"',
								'get_option_list' => "",
									'value_type' = serialized OR normal,
			'wp_data'=>(option or post_meta),
			'post_id' =>
			*/     
		// για να αποθηκευτεί κάθε field ως μέρος array, και όχι σε χωριστή εγγραφή στη βάση, ορίζω το $array_element όπου το name είναι το record name στον πίνακα wp_options της βάσης και id είναι ο δείκτης στο array.
			$array_element=$args['name'].'['.$args['id'].']';
			if($args['wp_data'] == 'option')
			{
//				$wp_data_value =get_option($args['name'])[$args['id']] ;
				$wp_data_value = is_array(get_option($args['name'])) ? get_option($args['name'])[$args['id']] : '';


			} elseif($args['wp_data'] == 'post_meta'){
				$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
			}

			switch ($args['type']) {

				case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if($args['subtype'] != 'checkbox' && $args['subtype'] != 'multiselect') {
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
					$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
					$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
					if(isset($args['disabled'])){
									// hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$array_element.'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$array_element.'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					} else {
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$array_element.'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/

				} elseif ($args['subtype']=='checkbox') {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$array_element.'" size="40" value="1" '.$checked.' />';
				} else {  //subtype==multiselect
					$wp_data_value = is_array($wp_data_value) ? $wp_data_value : array();

					echo '<select style="width:280px" id="'. $args['id'] .'" name="'. $array_element .'[]" multiple>';
					foreach ($args['get_options_list'] as $shipping_method) 
					{
						$selected = false;
						if( in_array(  $shipping_method, $wp_data_value )	) 
						{
							$selected = true;			
						} 

						echo "<option value='".$shipping_method."' " . selected( $selected, true, false ) . ">". $shipping_method."</option>";
					}
					echo '</select>';
				}
				break;
				default:
					# code...
				break;
			}
	}

	// Βασική διαδικασία που θα καλείται όταν ΟΛΟΚΛΗΡΩΝΕΤΑΙ η παραγγελία:
	// Έχει προστεθεί hook στην private function define_admin_hooks() στο includes\class-create-geniki-taxydromiki-vouchers-for-woo-v3.php
	public function woocommerce_create_gt_voucher( $order_id )
	{
		// ενεργοποίηση της διασύνδεσης (API) με ΓΤ Web Services
		if ( ! @isset($this->gt_api) ) $this->gt_api = new GT_API();

		// Νέο αντικείμενο που δημιουργεί Voucher
		// χρησιμοποιώντας το Αpplication Interface που ενεργοποιήσαμε
		$gtvfw=new GTVFW($this->gt_api);

		$order=wc_get_order( $order_id );

		if ( $gtvfw->is_method($order_id)) {
			// Δημιουργία voucher για την παραγγελία
			$gtvfw->gtvfw_create_voucher($order_id);
		}else{ // Αν η μέθοδος αποστολής της παραγγελίας δεν είναι
		// επιλεγμένη για τη ΓΤ τότε μην φτιάξεις voucher
			$order->add_order_note('Order send by other method');		
		}
		return;
	} // function woocommerce_create_gt_voucher()
	
	// ***********************************************************************************************
	// Εμφάνιση στις στήλες με τς παραγγελίες:
 
	public function gt_add_new_order_admin_list_column( $columns ) {
 	   $columns['gt_track'] = 'Αποστολή ΓΤ';
    return $columns;
	}

	function gt_add_new_order_admin_list_column_content( $column, $order_or_postid) {
  	 	if ( 'gt_track' === $column ) {
  	 		// check if arg is order object (HPOS) or post id (CPT)
		    $order = ( $order_or_postid instanceof WP_Post )
			    ? wc_get_order( $order_or_postid->ID )
			    : $order_or_postid;
	 		echo $this->gt_shipping_status( $order );
    	}	
	}
	
	
	// Εμφάνιση shipping status παραγγελίας
	private function gt_shipping_status( $order ): string {
		$courier_voucher=$order->get_meta( 'courier_voucher');
		// αν δεν είναι συμπληρωμένος αριθμός voucher, τότε ABORD
		if ( $courier_voucher == '')	{
			return 'not available';;
		}
		if ( ! @isset($this->gt_api) ) $this->gt_api = new GT_API();
		return 	$this->gt_api->get_status($courier_voucher);
	}

} //class
