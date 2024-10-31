<?php
/*
Plugin Name: PW Advanced Woo Reporting - Free Version
Plugin URI: http://proword.net/Advanced_Reporting/
Description: WooCommerce Advance Reporting plugin is a comprehensive and the most complete reporting system.
Version: 2.1
Author: Proword
Author URI: http://proword.net/
Text Domain: pw_report_wcreport_textdomain
Domain Path: /languages/
*/

if(!class_exists('pw_report_wcreport_class')){

	//USE IN INCLUDE
	define( '__PW_REPORT_WCREPORT_ROOT_DIR__', dirname(__FILE__));
	
	//USE IN ENQUEUE AND IMAGE
	define( '__PW_REPORT_WCREPORT_CSS_URL__', plugins_url('assets/css/',__FILE__));
	define( '__PW_REPORT_WCREPORT_JS_URL__', plugins_url('assets/js/',__FILE__));
	define ('__PW_REPORT_WCREPORT_URL__',plugins_url('', __FILE__));
	
	//PERFIX
	define ('__PW_REPORT_WCREPORT_FIELDS_PERFIX__', 'custom_report_' );
	
	//TEXT DOMAIN FOR MULTI LANGUAGE
	define ('__PW_REPORT_WCREPORT_TEXTDOMAIN__', 'pw_report_wcreport_textdomain' );
	
	include('includes/datatable_generator.php');
	
	//CLASS FOR ENQUEUE SCRIPTS AND STYLES
	class pw_report_wcreport_class extends pw_rpt_datatable_generate{
		
		public $pw_plugin_status='';
				
		function __construct(){
			include('includes/actions.php');
			
			add_action('admin_head',array($this,'pw_report_backend_enqueue'));
			add_action( 'plugins_loaded', array( $this, 'loadTextDomain' ) );
			add_action('admin_menu', array( $this,'pw_report_setup_menus'));
			
			$this->pw_plugin_status="";
			
		} 

		
		function pw_report_backend_enqueue(){
			if(isset($_GET['parent']))
			{
				include ("includes/admin-embed.php");
			}
		}	
		
		function loadTextDomain() {
			load_plugin_textdomain( 'pw_report_wcreport_textdomain' , false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
		}
		
		function pw_report_setup_menus() {
			
			global $submenu;
			add_menu_page(__('Woo Reporting',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Woo Reporting',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_dashboard&parent=dashboard',  array($this,'wcx_plugin_dashboard'),'dashicons-chart-pie' );
			
			add_submenu_page(null, __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_dashboard',  array($this,'wcx_plugin_dashboard' ));
			
			add_submenu_page(null, __('My Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('My Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_plugin_menu_my_dashboard',  array($this,'wcx_plugin_menu_my_dashboard' ));
			
			//ALL DETAILS
			add_submenu_page(null, __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_product',   array($this,'wcx_plugin_menu_product' ) );		
			add_submenu_page(null, __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_category',   array($this,'wcx_plugin_menu_category' ) );
			//////////////////////////////////////////////
			//////////////////////
			//////////////////////////////////////////////
			
			/////////////////////////////
			//SETTINGS
			/////////////////////////////////
			
			add_submenu_page(null, __('Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Report Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_addons_report',   array($this,'wcx_plugin_menu_addons_report' ) );
			
			add_submenu_page(null, __('Proword',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Other Useful Plugins',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_proword_report',   array($this,'wcx_plugin_menu_proword_report' ) );	
			
		}
		
		function wcx_plugin_dashboard($display="all"){
			$this->pages_fetch("dashboard_report.php",$display);
		}
		
		//////////////////////ALL DETAILS//////////////////////
		
		function pages_fetch($page,$display="all"){
			
			$visible_menu=array(
				
				array(
					"parent" => "main",
					"childs" => array(
						array(
							"label" => __('All Menus',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "all_menu",
							"link" => "#",
							"icon" => "fa-bars",
						),
						array(
							"label" => __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "dashboard",
							"link" => "admin.php?page=wcx_wcreport_plugin_dashboard&parent=dashboard",
							"icon" => "fa-bookmark",
						),
						array(
							"label" => __('All Orders',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "all_orders",
							"link" => "#",
							"icon" => "fa-file-text",
						),
						array(
							"label" => __('More Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "more_reports",
							"link" => "#",
							"icon" => "fa-files-o",
							"submenu_id" => "more_reports",
						),
						//CROSSTAB
						//VARIATION
						array(
							"label" => __('Stock List',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "stock_list",
							"link" => "#",
							"icon" => "fa-cart-arrow-down",
						),
						//VARIATION STOCK 
						array(
							"label" => __('Target Sale vs Actual Sale',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "proj_actual_sale",
							"link" => "#",
							"icon" => "fa-calendar-check-o",
						),
						array(
							"label" => __('Tax Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "tax_reports",
							"link" => "#",
							"icon" => "fa-pie-chart",
						),
						array(
							"label" => __('Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "setting",
							"link" => "#",
							"icon" => "fa-cogs",
						),
						array(
							"label" => __('Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "addons",
							"link" => "admin.php?page=wcx_wcreport_plugin_addons_report&parent=addons",
							"icon" => "fa-plug",
						),
						array(
							"label" => __('Proword',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "proword",
							"link" => "admin.php?page=wcx_wcreport_plugin_proword_report&parent=proword",
							"icon" => "fa-product-hunt",
						),
					)
 				),
				array(
					"parent" => "more_reports",
					"childs" => array(
						array(
							"label" => __("Product" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "product",
							"link" => "admin.php?page=wcx_wcreport_plugin_product&parent=more_reports&product",
							"icon" => "fa-cog",
						),
						array(
							"label" => __("Category" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "category",
							"link" => "admin.php?page=wcx_wcreport_plugin_category&parent=more_reports&category",
							"icon" => "fa-tags",
						),
						array(
							"label" => __("Customer" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "customer",
							"link" => "#",
							"icon" => "fa-user",
						),
						array(
							"label" => __("Billing Country" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "billing_country",
							"link" => "#",
							"icon" => "fa-globe",
						),
						array(
							"label" => __("Billing State" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "billing_state",
							"link" => "#",
							"icon" => "fa-map",
						),
						array(
							"label" => __("Payment Gateway" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "payment_gateway",
							"link" => "#",
							"icon" => "fa-credit-card",
						),
						array(
							"label" => __("Order Status" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "order_status",
							"link" => "#",
							"icon" => "fa-check",
						),
						array(
							"label" => __("Recent Order" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "recent_order",
							"link" => "#",
							"icon" => "fa-shopping-cart",
						),
						array(
							"label" => __("Tax Report" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "tax_report",
							"link" => "#",
							"icon" => "fa-pie-chart",
						),
						array(
							"label" => __("Customer Buy Product" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "customer_buy_prod",
							"link" => "#",
							"icon" => "fa-users",
						),
						array(
							"label" => __("Refund Detail" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "product",
							"link" => "#",
							"icon" => "fa-eye-slash",
						),
						array(
							"label" => __("Coupon" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "cupon",
							"link" => "#",
							"icon" => "fa-hashtag",
						),
					)
 				),
			);
						
			include("class/pages_fetch_dashboards.php");
		}
		
		//1-PRODUCTS
		function wcx_plugin_menu_product(){
			$this->pages_fetch("product.php");
		}
		//2-CATEGORY
		function wcx_plugin_menu_category(){
			$this->pages_fetch("category.php");
		}
		
		//////////////////////CROSS TABS//////////////////////
		
		//ADD-ONS
		function wcx_plugin_menu_addons_report(){
			$this->pages_fetch("addons_report.php");
		}
		
		//ADD-ONS
		function wcx_plugin_menu_proword_report(){
			$this->pages_fetch("advertise_other_plugins.php");
		}
		
	}
	
	$GLOBALS['pw_rpt_main_class'] = new pw_report_wcreport_class;
	
	
	//THE PLUGIN PAGES IS CREATED IN THIS FILE
	//include('class/custommenu.php');
}
?>