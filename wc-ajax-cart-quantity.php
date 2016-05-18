<?php
/*
Plugin Name: WooCommerce AJAX Cart Quantity
Plugin URI: http://www.bryanheadrick.com
Description: Page Caching safe method to display current cart quantity on a menu item
Version: 1.1
Author: Bryan Headrick (Edits: Matt Miller/Miller Media)
Author URI: http://www.bryanheadrick.com
*/
/*

Changelog:
v1.0: Initial release
v1.1: Code Cleanup / Adjusted for latest version of WooCommerce

*/
/*
Credits: 
	This template is based on the template at http://pressography.com/plugins/wordpress-plugin-template/ 
	My changes are documented at http://soderlind.no/archives/2010/03/04/wordpress-plugin-template/
*/

if (!class_exists('wc_ajax_cart_quantity')) {
    class wc_ajax_cart_quantity
    {
        /**
         * @var string The options string name for this plugin
         */
        var $optionsName = 'wc_ajax_cart_quantity_options';

        /**
         * @var array $options Stores the options for this plugin
         */
        var $options = array();
        /**
         * @var string $localizationDomain Domain used for localization
         */
        var $localizationDomain = "wc_ajax_cart_quantity";

        /**
         * @var string $url The url to this plugin
         */
        var $url = '';
        /**
         * @var string $urlpath The path to this plugin
         */
        var $urlpath = '';

        //Class Functions
        /**
         * PHP 4 Compatible Constructor
         */
        function wc_ajax_cart_quantity()
        {
            $this->__construct();
        }

        /**
         * PHP 5 Constructor
         */
        function __construct()
        {
            //Language Setup
            $locale = get_locale();
            $mo = plugin_dir_path(__FILE__) . 'languages/' . $this->localizationDomain . '-' . $locale . '.mo';
            load_textdomain($this->localizationDomain, $mo);

            //"Constants" setup
            $this->url = plugins_url(basename(__FILE__), __FILE__);
            $this->urlpath = plugins_url('', __FILE__);

            //Actions
            add_action('wp_enqueue_scripts', array(&$this, 'wcajaxcartqty_addscripts'));
            add_action('wp_ajax_nopriv_get_ajax_cart_qty', array(&$this, 'get_ajax_cart_qty'));
            add_action('wp_ajax_get_ajax_cart_qty', array(&$this, 'get_ajax_cart_qty'));
        }

        function wcajaxcartqty_addscripts()
        {
            global $woocommerce;
            $cart_url = $woocommerce->cart->get_cart_url();

            if (!is_admin()):
                wp_enqueue_script('wcajaxcartqty_script', plugins_url('', __FILE__) . '/ajax.js', array('jquery'));
                wp_localize_script('wcajaxcartqty_script', 'cart_page', array('cart_url' => $cart_url, 'wpajaxurl' => admin_url('admin-ajax.php')));
            endif;
        }

        function wc_ajax_cart_quantity_script()
        {
            wp_enqueue_script('jquery'); // other scripts included with Wordpress: http://tinyurl.com/y875age
            wp_enqueue_script('jquery-validate', 'http://ajax.microsoft.com/ajax/jquery.validate/1.6/jquery.validate.min.js', array('jquery')); // other/new versions: http://www.asp.net/ajaxlibrary/cdn.ashx
            wp_enqueue_script('wc_ajax_cart_quantity_script', $this->url . '?wc_ajax_cart_quantity_javascript'); // embed javascript, see end of this file
            wp_localize_script('wc_ajax_cart_quantity_script', 'wc_ajax_cart_quantity_lang', array(
                'required' => __('Please enter a number.', $this->localizationDomain),
                'number' => __('Please enter a number.', $this->localizationDomain),
                'min' => __('Please enter a value greater than or equal to 1.', $this->localizationDomain),
            ));
        }

        /**
         * @desc Adds the Settings link to the plugin activate/deactivate page
         */
        function filter_plugin_actions($links, $file)
        {
            $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
            array_unshift($links, $settings_link); // before other links

            return $links;
        }

        function get_ajax_cart_qty()
        {
            global $woocommerce;
            print $woocommerce->cart->cart_contents_count;

        }
    } //End Class
} //End if class exists statement


if (isset($_GET['wc_ajax_cart_quantity_javascript'])) {
    //embed javascript
    Header("content-type: application/x-javascript");
    echo <<<ENDJS
/**
* @desc WooCommerce AJAX Cart Quantity
* @author Bryan Headrick - http://www.bryanheadrick.com
*/

jQuery(document).ready(function(){
	// add your jquery code here


	//validate plugin option form
  	jQuery("#wc_ajax_cart_quantity_options").validate({
		rules: {
			wc_ajax_cart_quantity_option1: {
				required: true,
				number: true,
				min: 1
			}
		},
		messages: {
			wc_ajax_cart_quantity_option1: {
				// the wc_ajax_cart_quantity_lang object is define using wp_localize_script() in function wc_ajax_cart_quantity_script() 
				required: wc_ajax_cart_quantity_lang.required,
				number: wc_ajax_cart_quantity_lang.number,
				min: wc_ajax_cart_quantity_lang.min
			}
		}
	});
});

ENDJS;

} else {
    if (class_exists('wc_ajax_cart_quantity')) {
        $wc_ajax_cart_quantity_var = new wc_ajax_cart_quantity();
    }
}
?>
