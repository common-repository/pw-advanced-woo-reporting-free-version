<?php
	if(!class_exists('pw_rpt_datatable_generate'))
	{
		class pw_rpt_datatable_generate{
			
			public $results;
			public $search_form_fields='';
			public $table_cols;
			public $refund_status='refunddetails_status_refunded_main';
			var $order_meta= array();
			public $month_count=0;
			public $month_start=1;
			public $data_month='';
			public $data_country='';
			public $data_state='';
			public $data_variation='';
			public $pw_from_date_dashboard='';
			public $pw_to_date_dashboard='';			
						
			public function __construct(){
				
			}
			
			
			//////////////////////////////
			// GENERATE SQL
			//////////////////////////////
			public function fetch_sql($table_name,$search_fields=NULL){
				global $wpdb;
				
				$file_used="sql_table";
				switch($table_name){
					
					case 'top_5_products':
						require("fetch_data_dashboard_top_5_products.php");	
					break;
					
					case 'top_5_category':
						require("fetch_data_dashboard_top_5_category.php");	
					break;
					
					case 'product' :
						require("fetch_data_product.php");
					break;
					
					case 'category' :
						require("fetch_data_category.php");
					break;
					
				}
				
				if(isset($sql))
					return $wpdb->get_results($sql);
					
			}
			
			
			//////////////////////////////
			// GENERATE TABLE COLUMNS
			//////////////////////////////
			public function table_columns($table_name){
				switch($table_name){
					
					case 'dashboard_report':
						$table_column=array();
					break;
				
					case 'top_5_products':
						$table_column=array(
							array('lable'=>__('Item Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
							array('lable'=>__('Qty',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
							array('lable'=>__('Amount',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
					);
					break;
					
					case 'top_5_category':
						$table_column=array(
							array('lable'=>__('Category Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
							array('lable'=>__('Qty',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
							array('lable'=>__('Amount',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
					);
					break;
					case 'products':
						$table_column=array(
							__('Product ID',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							__('Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							__('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							__('Qty.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							__('Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							__('Price',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							__('Total Amount',__PW_REPORT_WCREPORT_TEXTDOMAIN__)
						);
					break;
					
					
					case 'product':
						$table_column=array(
							array('lable'=>__('Product SKU',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
							array('lable'=>__('Product Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
							array('lable'=>__('Categories',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),				
							array('lable'=>__('Sales Qty.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
							array('lable'=>__('Current Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
							array('lable'=>__('Amount',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
						);
					break;
							
					case 'category':
						$table_column=array(
							array('lable'=>__('Category Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
							array('lable'=>__('Quantity',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
							array('lable'=>__('Amount',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),				
						);
					break;
					
				
					
				}
				
				return $table_column;
			}
			
			
			//////////////////////////////
			// MAIN FUNCTION OF TABLE HTML
			//////////////////////////////
			public function table_html($table_name,$search_fields=NULL){
				
				$product_count=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_product_post_per_page',5);
				$order_count=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'recent_post_per_page',5);
				$category_count=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_category_post_per_page',5);
				$country_count=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_country_post_per_page',5);
				$state_count=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_state_post_per_page',5);
				$gateway_count=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_gateway_post_per_page',5);
				$coupon_count=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_coupon_post_per_page',5);
				$customer_count=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_customer_post_per_page',5);
				
				
				$page_titles=array(
					'dashboard_report'		=> "Summary",
					
					'top_5_products'		=> "Top $product_count Products",
					'top_5_category'		=> "Top $category_count Categroy",
					
					'product'				 => "Product",
					'category'				=> "Category",
					
				);
				
				
				$except_table=array("monthly_summary","order_summary","sale_order_status","top_5_products","top_5_category","top_5_country","top_5_state","top_5_customer","top_5_coupon","top_5_gateway","recent_5_order");
				
				$chart_table=array("order_summary","sale_order_status","top_5_products","top_5_category","top_5_country","top_5_state","top_5_customer","top_5_coupon","top_5_gateway");


				if($search_fields!=NULL || in_array($table_name,$except_table))
				{   $this->search_form_fields=$search_fields;
					$this->results =$this->fetch_sql($table_name,$search_fields);
				}
		
				/**************TABLE COLUMNS & CONTROLS************/
				
				
				///////////////////////////////
				// FETCH REPORT COLUMNS :
				// 1- There are two types of columns for refunddetails
				// 2- There are dynamic columns : The columns will be changed in code
				// 3- General mode
				
				$refund_type='';
				if($table_name=='refunddetails')
				{
					$refund_type=$this->refund_status;
					$this->table_cols =$this->table_columns($refund_type);
				}else if( in_array ( $table_name,array('details','prod_per_month','variation_per_month','prod_per_country','prod_per_state','country_per_month','payment_per_month','ord_status_per_month','summary_per_month','variation','stock_list','variation_stock','tax_reports') ) )
				{
					//$this->table_cols =$this->table_columns($table_name);
				}else{
					$this->table_cols = $this->table_columns($table_name);
				}
				
				//print_r($this->table_cols);
				
				$cols_controls='';
				$table_cols='';
				
				$i=0;
				if($search_fields!=NULL  || in_array($table_name,$except_table))
				{
					foreach($this->table_cols as $cols)
					{
						$checked='checked'; $display='';
						if ($cols['status']=='hide'){ $checked='';$display='display:none;';}
						$cols_controls.= '<label><input type="checkbox" '.$checked.'  data-column="'.$i++.'">'.$cols['lable'].'</label>';
						$table_cols.= '<th style="'.$display.'"><div>'.$cols['lable'].'</div></th>';
						
					}
				}
				//echo $table_cols;
				
				/**************TABLE FETCH DATAS OF TABLE************/
				if($search_fields!=NULL  || in_array($table_name,$except_table) || $table_name=='dashboard_report')
				{
					$pw_null_val = $this->price(0);
					$datatable_value='';
					
					
					$file_used="data_table";
					switch($table_name){
						
						case 'dashboard_report':
							require("fetch_data_dashboard_report.php");	
						break;	
						
						case 'top_5_products':
							require("fetch_data_dashboard_top_5_products.php");	
						break;
	
						case 'top_5_category':
							require("fetch_data_dashboard_top_5_category.php");	
						break;
						//ALL DETAILS
						case 'product':
							require("fetch_data_product.php");
						break;
						case 'category':
							require("fetch_data_category.php");
						break;
						
					}
				}
								
				/**************TABLE HTML************/
				if(($search_fields!=NULL && $table_name!='dashboard_report')   || (in_array($table_name,$except_table)))
				{
				?>
                <div class="awr-box">
                    <div class="awr-title">
                        <h3>
                            <i class="fa fa-filter"></i>
                            <?php 
                                //_e('Result',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
                                echo $page_titles[$table_name];
                            ?>
                        </h3>
                        
                        <?php
							if(in_array($table_name,$chart_table))
							{
						?>
                        <div class="awr-title-icons">
							<div class="awr-title-icon awr-title-icon-active" data-table="<?php echo $table_name; ?>" data-swap-type="awr-grid-chart"><i class="fa fa-table"></i></div>
							<div class="awr-title-icon" data-table="<?php echo $table_name; ?>" data-swap-type="awr-pie-chart"><i class="fa fa-pie-chart"></i></div>
							<div class="awr-title-icon" data-table="<?php echo $table_name; ?>" data-swap-type="awr-bar-chart"><i class="fa fa-bar-chart"></i></div>
						</div> 
                        <?php } ?>
                        
                    </div><!--awr-title -->
                    <div class="awr-box-content" id="awr-grid-chart-<?php echo $table_name; ?>">
    
                        <?php
							if(!in_array($table_name,$except_table))
							{
						?>
                        
                            <div class="awr-selcol">
                                    <a class="btn default" href="javascript:;" data-toggle="dropdown">
                                    Select Columns <i class="fa fa-angle-down"></i>
                                    </a>
                                    <div  class="dropdown-menu awr-opened">
                                    <?php
                                        echo $cols_controls;
                                    ?>    
                                    </div>
                            </div>
                        <?php } ?>
             
                       
                        <table class="display datatable <?php echo $table_name?>_datatable" cellspacing="0" width="100%">
                            <thead>
                                <tr>			
                                <?php
                                    echo $table_cols;
                                ?>    
                                </tr>
                            </thead>
                            
                            <?php
                            	if(!in_array($table_name,$except_table))
								{
                            ?>
                            <tfoot>
                                <tr>			
                                <?php
                                    echo $table_cols;
                                ?>    
                                </tr>
                            </tfoot>
                            <?php 
								}
							?>
                            
                            <tbody>
                                <?php 
                                echo $datatable_value;
                                ?>
                            </tbody>
                            
                        </table>
                        
                    
                        </div><!--awr-box-content -->
                	<div class="awr-box-content" id="awr-pie-chart-<?php echo $table_name; ?>"></div>
                    <div class="awr-box-content" id="awr-bar-chart-<?php echo $table_name; ?>"></div>   
                </div>

                <?php
				}else if(/*$search_fields!=NULL && */$table_name=='dashboard_report'){
				?>
                	
                   <!--HERE IS JUST FOR DASHBOARD SUMMARY BOX-->
                    
                <?php	
				}
			
			
			}//ENd Function
			
			
			
			//////////////////////////////
			// SEARCH FORM HTML
			//////////////////////////////
			public function search_form_html($table_name){
				//$this->results =$this->fetch_sql($table_name);
												
				$file_used="search_form";
				switch($table_name){
					
					case 'dashboard_report':
						require("fetch_data_dashboard_report.php");	
					break;
					
					case 'products':
						require("fetch_data_products.php");
					break;
					
					//ALL DETAILS
					case 'product':
						require("fetch_data_product.php");
					break;
					case 'category':
						require("fetch_data_category.php");
					break;
			
				}
			}
			
			
			
			function pw_get_prod_sku($order_item_id, $pw_product_id/*,$current_page='',$product_type='-1'*/){
				
				$pw_table_value = $this->pw_get_oiv_sku($order_item_id);
				$pw_table_value = strlen($pw_table_value) > 0 ? $pw_table_value : $this->pw_get_op_sku($pw_product_id);
				
				$pw_table_value = strlen($pw_table_value) > 0 ? $pw_table_value : 'Not Set';
				return $pw_table_value;
			}
			
			function pw_get_prod_stock_($order_item_id, $pw_product_id){
				$pw_table_value = $this->pw_get_oiv_stock($order_item_id);
				$pw_table_value = strlen($pw_table_value) > 0 ? $pw_table_value : $this->pw_op_stock($pw_product_id);
				$pw_table_value = strlen($pw_table_value) > 0 ? $pw_table_value : 'Not Set';
				return $pw_table_value;
			}
			
			function pw_get_oiv_stock($order_item_id = 0){
				global $wpdb;
				$sql = "
				SELECT 
				pw_postmeta_sku.meta_value as pw_variation_sku				
				FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items
				LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id = pw_woocommerce_order_items.order_item_id
				LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_sku ON pw_postmeta_sku.post_id = woocommerce_order_itemmeta.meta_value
				WHERE pw_woocommerce_order_items.order_item_id={$order_item_id}
				
				AND pw_woocommerce_order_items.order_item_type = 'line_item'
				AND woocommerce_order_itemmeta.meta_key = '_variation_id'
				AND pw_postmeta_sku.meta_key = '_stock'
				";
				return $orderitems = $wpdb->get_var($sql);
			}
			
			function pw_op_stock($pw_product_id = 0){
				global $wpdb;
				$sql = "SELECT pw_postmeta_stock.meta_value as pw_product_sku
				FROM {$wpdb->prefix}postmeta as pw_postmeta_stock			
				WHERE pw_postmeta_stock.meta_key = '_stock'";
				
				
				if(strlen($pw_product_id) >= 0 and  $pw_product_id > 0)
					$sql .= " and pw_postmeta_stock.post_id = {$pw_product_id}";
					
				if(strlen($pw_product_id) >= 0 and  $pw_product_id > 0){
					$orderitems = $wpdb->get_var($sql);
					if(strlen($wpdb->last_error) > 0){
						echo $wpdb->last_error;
					}
				}else
					$orderitems = '';
				
				
				return $orderitems;
				
				//return $orderitems = $wpdb->get_var($sql);
			}
			
			function pw_get_oiv_sku($order_item_id = 0){
				global $wpdb;
				$sql = "
				SELECT 
				pw_postmeta_sku.meta_value as pw_variation_sku				
				FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items
				LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id = pw_woocommerce_order_items.order_item_id
				LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_sku ON pw_postmeta_sku.post_id = woocommerce_order_itemmeta.meta_value
				WHERE pw_woocommerce_order_items.order_item_id={$order_item_id}
				
				AND pw_woocommerce_order_items.order_item_type = 'line_item'
				AND woocommerce_order_itemmeta.meta_key = '_variation_id'
				AND pw_postmeta_sku.meta_key = '_sku'
				";
				return $orderitems = $wpdb->get_var($sql);
			}
			
			function pw_get_op_sku($pw_product_id = 0){
				global $wpdb;
				$sql = "SELECT pw_postmeta_sku.meta_value as pw_product_sku
				FROM {$wpdb->prefix}postmeta as pw_postmeta_sku			
				WHERE pw_postmeta_sku.meta_key = '_sku'";
				
				
				if(strlen($pw_product_id) >= 0 and  $pw_product_id > 0)
					$sql .= " and pw_postmeta_sku.post_id = {$pw_product_id}";
					
				if(strlen($pw_product_id) >= 0 and  $pw_product_id > 0){
					$orderitems = $wpdb->get_var($sql);
					if(strlen($wpdb->last_error) > 0){
						echo $wpdb->last_error;
					}
				}else
					$orderitems = '';
				
				return $orderitems;
			}
			
			
			
			function price($vlaue, $args = array()){
			
				$currency        = isset( $args['currency'] ) ? $args['currency'] : '';
				
				if (!$currency ) {
					if(!isset($this->constants['woocommerce_currency'])){
						$this->constants['woocommerce_currency'] =  $currency = (function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : "USD");
					}else{
						$currency  = $this->constants['woocommerce_currency'];
					}
				}
				
				$args['currency'] 	= $currency;
				$vlaue 				= trim($vlaue);
				$withoutdecimal 	= str_replace(".","d",$vlaue);
							
				if(!isset($this->constants['price_format'][$currency][$withoutdecimal])){
					if(!function_exists('wc_price')){
						if(!isset($this->constants['currency_symbol'])){
							$this->constants['currency_symbol'] =  $currency_symbol 	= apply_filters( 'pw_woo_symbol_currency', '&#36;', 'USD');
						}else{
							$currency_symbol  = $this->constants['currency_symbol'];
						}					
						$vlaue				= strlen(trim($vlaue)) > 0 ? $vlaue : 0;
						$v 					= $currency_symbol."".number_format($vlaue, 2, '.', ' ');
						$v					= "<span class=\"amount\">{$v}</span>";
						
					}else{
						$v = wc_price($vlaue, $args);
					}
					$this->constants['price_format'][$currency][$withoutdecimal] = $v;
				}else{
					$v = $this->constants['price_format'][$currency][$withoutdecimal];				
				}
				
				
				return $v;
			}
			
			function woocommerce_currency(){
				if(!isset($this->constants['woocommerce_currency'])){
					$this->constants['woocommerce_currency'] =  $currency = (function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : "USD");
				}else{
					$currency  = $this->constants['woocommerce_currency'];
				}			
				return $currency;
			}
			
			var $terms_by = array();
			function pw_get_cn_product_id($id, $taxonomy = 'product_cat', $termkey = 'name'){
				$term_name ="";			
				if(!isset($this->terms_by[$taxonomy][$id])){
					$id			= (integer)$id;
					$terms		= get_the_terms($id, $taxonomy);
					$termlist	= array();
					if($terms and count($terms)>0){
						foreach ( $terms as $term ) {
								$termlist[] = $term->$termkey;
						}
						if(count($termlist)>0){
							$term_name =  implode( ', ', $termlist );
						}
					}
					$this->terms_by[$taxonomy][$id] = $term_name;				
				}else{				
					$term_name = $this->terms_by[$taxonomy][$id];
				}					
				return $term_name;
			}
			
		
			function pw_get_woo_countries(){
				return class_exists('WC_Countries') ? (new WC_Countries) : (object) array();
			}
		
		
			//GET REFUND PART AMMOUNT
			function pw_get_por_amount($order_id_string = array()){
				global $wpdb;
				
				$item_name = array();
				if(is_array($order_id_string)){
					$order_id_string = implode(",",$order_id_string);
				}
				
				if(strlen($order_id_string) > 0){
				
					
					
					$sql = "SELECT
						pw_posts.post_parent as order_id
						,SUM(postmeta.meta_value) 		as total_amount";
					
					$sql .= "
			
					FROM {$wpdb->prefix}posts as pw_posts
									
					LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id	=	pw_posts.ID";
					
					$sql .= " LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.ID	=	pw_posts.post_parent";
					
					$sql .= " WHERE pw_posts.post_type = 'shop_order_refund' AND  postmeta.meta_key='_refund_amount'";
					
					if(strlen($order_id_string) > 0){
						$sql .= "AND pw_posts.post_parent IN ({$order_id_string})";
					}
					
					$sql .= "AND shop_order.post_status NOT IN ('wc-refunded')";
					
					$sql .= " GROUP BY  pw_posts.post_parent";			
			
					$sql .= " ORDER BY pw_posts.post_parent DESC";
					
					$order_items = $this->get_results($sql);
					
					//$this->print_array($order_items);
					
					//$this->print_sql($sql);
					
					if(count($order_items) > 0){
						foreach($order_items as $key => $value){
							if(isset($item_name[$value->order_id]))
								$item_name[$value->order_id] = $item_name[$value->order_id] + $value->total_amount;
							else
								$item_name[$value->order_id] = $value->total_amount;
						}
					}
				}
				
				return $item_name;
		
			}
			
			
			function get_results($sql_query = ""){
				global $wpdb;
				$wpdb->query("SET SQL_BIG_SELECTS=1");
				$results = $wpdb->get_results($sql_query);				
				
				
				if($wpdb->last_error){
					echo $wpdb->last_error;
					//$this->print_sql($sql_query);
				}
				
				$wpdb->flush();
				return $results;
				
			}
			
	
			function pw_get_woo_orders_statuses(){
				if(!isset($this->constants['wc_order_statuses'])){
					if(function_exists('wc_get_order_statuses')){
						$pw_order_statuses = wc_get_order_statuses();						
					}else{
						$pw_order_statuses = array();
					}
					
					$pw_order_statuses['trash']	=	"Trash";
										
					$this->constants['wc_order_statuses'] = $pw_order_statuses;
				}else{
					$pw_order_statuses = $this->constants['wc_order_statuses'];
				}
				return $pw_order_statuses;
			}
			
			//GET PRODUCTS
			function pw_get_product_woo_data($pw_product_type = 'all'){
				
				global $wpdb;
				
				$category_id			= $this->pw_get_woo_requests('pw_category_id','-1');
				
				$taxonomy				= $this->pw_get_woo_requests_default('taxonomy','product_cat');
				
				$purchased_product_id	= $this->pw_get_woo_requests_default('purchased_product_id','-1');	
				
				$pw_hide_os 		= $this->pw_get_woo_requests_default('pw_hide_os','-1',true);
					
				$pw_publish_order			= 'no';
				
				$sql = "SELECT woocommerce_order_itemmeta.meta_value AS id, pw_woocommerce_order_items.order_item_name AS label 
				
				FROM `{$wpdb->prefix}woocommerce_order_items` as pw_woocommerce_order_items				
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id = pw_woocommerce_order_items.order_item_id";
				
				if($category_id != "-1" && $category_id >= 0){
					$sql .= " 
							LEFT JOIN {$wpdb->prefix}term_relationships		as pw_term_relationships		ON pw_term_relationships.object_id				= woocommerce_order_itemmeta.meta_value
							LEFT JOIN {$wpdb->prefix}term_taxonomy			AS term_taxonomy			ON term_taxonomy.term_taxonomy_id			= pw_term_relationships.term_taxonomy_id
							LEFT JOIN {$wpdb->prefix}terms					AS terms					ON pw_terms.term_id							= term_taxonomy.term_id";
				}
				if($pw_product_type == 1)
					$sql .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_variation_id_order_itemmeta ON pw_variation_id_order_itemmeta.order_item_id = pw_woocommerce_order_items.order_item_id";
				
				if($pw_product_type == 2 || ($pw_product_type == 'grouped' || $pw_product_type == 'external' || $pw_product_type == 'simple' || $pw_product_type == 'variable_')){
					$sql .= " 	
							LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships_product_type 	ON pw_term_relationships_product_type.object_id		=	woocommerce_order_itemmeta.meta_value 
							LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as pw_term_taxonomy_product_type 		ON pw_term_taxonomy_product_type.term_taxonomy_id		=	pw_term_relationships_product_type.term_taxonomy_id
							LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_product_type 				ON pw_terms_product_type.term_id						=	pw_term_taxonomy_product_type.term_id";
				}
				
				if(($pw_publish_order == "yes") || ($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'"))	$sql .= " LEFT JOIN {$wpdb->prefix}posts as pw_posts ON pw_posts.ID = pw_woocommerce_order_items.order_id";				
				
				$sql .= " WHERE woocommerce_order_itemmeta.meta_key = '_product_id'";
				
				if($category_id != "-1" && $category_id >= 0){
					$sql .= " AND term_taxonomy.taxonomy = 'product_cat'";
				}
				
				if($pw_product_type == 1)
					$sql .= " AND pw_variation_id_order_itemmeta.meta_key = '_variation_id' AND (pw_variation_id_order_itemmeta.meta_value IS NOT NULL AND pw_variation_id_order_itemmeta.meta_value > 0)";
				
				if($category_id != "-1" && $category_id >= 0)
					$sql .= " AND terms .term_id IN(".$category_id.")";
				
				if($pw_publish_order == 'yes')	$sql .= " AND pw_posts.post_status = 'publish'";
				
				if($pw_publish_order == 'publish' || $pw_publish_order == 'trash')	$sql .= " AND pw_posts.post_status = '".$pw_publish_order."'";
				
				if($pw_product_type == 'grouped' || $pw_product_type == 'external' || $pw_product_type == 'simple' || $pw_product_type == 'variable_'){
					$sql .= " AND pw_terms_product_type.name IN ('{$pw_product_type}')";
				}
				
				if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")$sql .= " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";
				
				$sql .= " GROUP BY woocommerce_order_itemmeta.meta_value ORDER BY pw_woocommerce_order_items.order_item_name ASC";
				
				//$this->print_sql($sql);
			
				$products = $wpdb->get_results($sql);
				
				//echo mysql_error();
			
				return $products;
			}
	
			
			function pw_get_woo_pli_category($categories = array(), $products = array(), $return_default = '-1' , $return_formate = 'string'){
				global $wpdb;
				
				$pw_cat_prod_id_string = $return_default;
				if(is_array($categories)){
					$categories = implode(",",$categories);
				}
				
				if(is_array($products)){
					$products = implode(",",$products);
				}
				
				if($categories  && $categories != "-1") {
				
					$sql  = " SELECT ";					
					$sql .= " woocommerce_order_itemmeta.meta_value		AS product_id";					
					
					$sql .= " FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items";
					$sql .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id";
					$sql .= " LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	woocommerce_order_itemmeta.meta_value ";
					$sql .= " LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";								
					$sql .= " WHERE 1*1 AND woocommerce_order_itemmeta.meta_key 	= '_product_id'";					
					$sql .= " AND term_taxonomy.term_id IN (".$categories .")";
										
					if($products  && $products != "-1") $sql .= " AND woocommerce_order_itemmeta.meta_value IN (".$products .")";
					
					$sql .= " GROUP BY  woocommerce_order_itemmeta.meta_value";
					
					$sql .= " ORDER BY product_id ASC";
					
					$order_items = $wpdb->get_results($sql);					
					$pw_product_id_list = array();
					if(count($order_items) > 0){
						foreach($order_items as $key => $order_item) $pw_product_id_list[] = $order_item->product_id;
						if($return_formate == 'string'){
							$pw_cat_prod_id_string = implode(",", $pw_product_id_list);
						}else{
							$pw_cat_prod_id_string = $pw_product_id_list;
						}
					}
				}
				
				return $pw_cat_prod_id_string;
				
			}
			
			//SOLD PRODUCT PARENT
			function pw_get_woo_sppc_data(){
				
				global $wpdb;
				
				//$pw_order_status	= $this->pw_get_woo_sm_requests('pw_orders_status',$pw_order_status, "-1");
				$pw_hide_os	= "-1";
				
				$sql ="";
				$sql .= " SELECT ";
				$sql .= " pw_term_taxonomy_product_id.parent AS id";
				$sql .= " ,pw_terms_parent_product_id.name AS label";
				
				$sql .= " FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items";
				
				
				$sql .= " LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.id=pw_woocommerce_order_items.order_id";
				
				$sql .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_product_id ON pw_woocommerce_order_itemmeta_product_id.order_item_id=pw_woocommerce_order_items.order_item_id";
				
				$sql .= " 	LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships_product_id 	ON pw_term_relationships_product_id.object_id		=	pw_woocommerce_order_itemmeta_product_id.meta_value 
							LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as pw_term_taxonomy_product_id 		ON pw_term_taxonomy_product_id.term_taxonomy_id	=	pw_term_relationships_product_id.term_taxonomy_id
							LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_product_id 				ON pw_terms_product_id.term_id						=	pw_term_taxonomy_product_id.term_id";
				
				$sql .= " 	LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_parent_product_id 				ON pw_terms_parent_product_id.term_id						=	pw_term_taxonomy_product_id.parent";
				
				$sql .= " WHERE 1*1 ";
				$sql .= " AND pw_woocommerce_order_items.order_item_type 	= 'line_item'";
				$sql .= " AND pw_woocommerce_order_itemmeta_product_id.meta_key 	= '_product_id'";
				$sql .= " AND pw_term_taxonomy_product_id.taxonomy 	= 'product_cat'";
				$sql .= " AND pw_term_taxonomy_product_id.parent > 0";
				
				
				$sql .= " AND pw_posts.post_type 											= 'shop_order'";				
				//if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")$sql .= " AND pw_posts.post_status IN (".$pw_order_status.")";
				if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")$sql .= " AND pw_posts.post_status NOT IN (".$pw_hide_os.")";
				
				$sql .= " GROUP BY pw_term_taxonomy_product_id.parent";
				
				$sql .= " ORDER BY pw_terms_parent_product_id.name ASC";
				
				$category_items = $wpdb->get_results($sql);
				
				if($wpdb->last_error){
					echo $wpdb->last_error;
				}
				return $category_items;
			}
			
			//GET REQUEST PARAMETERS
			public function pw_get_woo_requests($name,$default = NULL,$set = false){
				
				if(isset($this->search_form_fields[$name])){
					$newRequest = $this->search_form_fields[$name];
					
					if(is_array($newRequest)){
						$newRequest = implode(",", $newRequest);
					}else{
						$newRequest = trim($newRequest);
					}
					
					if($set) $this->search_form_fields[$name] = $newRequest;
					
					return $newRequest;
				}else{
					if($set) 	$this->search_form_fields[$name] = $default;
					return $default;
				}
			}
			
			public function pw_get_woo_requests_links($name,$default = NULL,$set = false){
				
				if(isset($_REQUEST[$name])){
					$newRequest = $_REQUEST[$name];
					
					if(is_array($newRequest)){
						$newRequest = implode(",", $newRequest);
					}else{
						$newRequest = trim($newRequest);
					}
					
					if($set) $_REQUEST[$name] = $newRequest;
					
					return $newRequest;
				}else{
					if($set) 	$_REQUEST[$name] = $default;
					return $default;
				}
			}	
			
			var $request_string = array();
			function pw_get_woo_sm_requests($id=1,$string, $default = NULL){
				
				if(isset($this->request_string[$id])){
					$string = $this->request_string[$id];
				}else{
					if($string == "'-1'" || $string == "\'-1\'"  || $string == "-1" ||$string == "''" || strlen($string) <= 0)$string = $default;
					if(strlen($string) > 0 and $string != $default){ $string  		= "'".str_replace(",","','",$string)."'";}
					$this->request_string[$id] = $string;			
				}
				
				return $string;
			}
			
			function pw_get_woo_requests_default($name, $default='', $set = false){
				if(isset($_REQUEST[$name])){
					$newRequest = trim($_REQUEST[$name]);
					return $newRequest;
				}else{
					if($set) $_REQUEST[$name] = $default;
					return $default;
				}
			}
			
			//DASHBOARD
			function dashboard_pw_get_por_amount($type = "today",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date){
				global $wpdb;
				
				$today_date 			= date("Y-m-d");
				$yesterday_date 		= date("Y-m-d",strtotime("-1 day",strtotime($today_date)));
				
				$sql = " SELECT SUM(postmeta.meta_value) 		as total_amount
						
				FROM {$wpdb->prefix}posts as pw_posts
								
				LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id	=	pw_posts.ID";
				
				$sql .= " LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.ID	=	pw_posts.post_parent";
				
				
				$sql .= " WHERE pw_posts.post_type = 'shop_order_refund' AND  postmeta.meta_key='_refund_amount'";
				
				$sql .= " AND shop_order.post_type = 'shop_order'";
						
				
				$sql .= " AND shop_order.post_status NOT IN ('wc-refunded')";
				
				if(count($pw_shop_order_status)>0){
					$pw_in_shop_os		= implode("', '",$pw_shop_order_status);
					$sql .= " AND  shop_order.post_status IN ('{$pw_in_shop_os}')";
				}
			
				
				if ($pw_from_date != NULL &&  $pw_to_date != NULL && $type == "total"){
					$sql .= " AND DATE(pw_posts.post_date) BETWEEN '{$pw_from_date}' AND '{$pw_to_date}'";
				}
				
				if($type == "today") $sql .= " AND DATE(pw_posts.post_date) = '{$today_date}'";
				
				if($type == "yesterday") 	$sql .= " AND DATE(pw_posts.post_date) = '{$yesterday_date}'";
				
				if(count($pw_hide_os)>0){
					$in_pw_hide_os		= implode("', '",$pw_hide_os);
					$sql .= " AND  shop_order.post_status NOT IN ('{$in_pw_hide_os}')";
				}
				
				$sql .= " LIMIT 1";
				
				//echo $sql;
				
				//$this->print_sql($sql);
			
				$wpdb->query("SET SQL_BIG_SELECTS=1");
				
				$order_items = $wpdb->get_var($sql);
				
				return $order_items;
				
			}
			
			function pw_get_dashboard_totals_coupons($type = "today",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date){
				global $wpdb,$options;
				$today_date 			= date("Y-m-d");
				$yesterday_date 		= date("Y-m-d",strtotime("-1 day",strtotime($today_date)));
				$sql = "
				SELECT				
				SUM(woocommerce_order_itemmeta.meta_value) As 'total_amount', 
				Count(*) AS 'total_count' 
				FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items 
				LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id
				LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id";
				
				$sql .= "
				WHERE 
				pw_woocommerce_order_items.order_item_type='coupon' 
				AND woocommerce_order_itemmeta.meta_key='discount_amount'
				AND pw_posts.post_type='shop_order'
				";
				
				if($type == "today") $sql .= " AND DATE(pw_posts.post_date) = '{$today_date}'";
				if($type == "yesterday") 	$sql .= " AND DATE(pw_posts.post_date) = '{$yesterday_date}'";
				
				if(count($pw_shop_order_status)>0){
					$pw_in_shop_os		= implode("', '",$pw_shop_order_status);
					$sql .= " AND  pw_posts.post_status IN ('{$pw_in_shop_os}')";
				}
				
				if ($pw_from_date != NULL &&  $pw_to_date != NULL && $type != "today"){
					$sql .= " AND DATE(pw_posts.post_date) BETWEEN '{$pw_from_date}' AND '{$pw_to_date}'";
				}
				
				if(count($pw_hide_os)>0){
					$in_pw_hide_os		= implode("', '",$pw_hide_os);
					$sql .= " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
				}
				
				//$this->print_sql($sql);
				return $order_items = $wpdb->get_row($sql); 
				
				///$this->print_array($order_items);
			}
			
			function pw_get_dashboard_value($data = NULL, $id, $default = ''){
				if($data){
					if($data->$id)
						return $data->$id;
				}
				return $default;
			}
			
		
			function pw_get_dashborad_totals_orders($type = "today", $meta_key="_order_tax",$order_item_type="tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date){
				global $wpdb;
				$today_date 			= date("Y-m-d");
				$yesterday_date 		= date("Y-m-d",strtotime("-1 day",strtotime($today_date)));
				
				$sql = "  SELECT";
				$sql .= " SUM(pw_postmeta1.meta_value) AS 'total_amount'";
				$sql .= " ,count(pw_woocommerce_order_items.order_id) AS 'total_count'";			
				$sql .= " FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items				
				LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_woocommerce_order_items.order_id";
				
				$sql .= " LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=	pw_woocommerce_order_items.order_id";
				
				
				$sql .= " WHERE pw_postmeta1.meta_key = '{$meta_key}' AND pw_woocommerce_order_items.order_item_type = '{$order_item_type}'";
				
				$sql .= " AND pw_posts.post_type='shop_order' ";
				
				if($type == "today") $sql .= " AND DATE(pw_posts.post_date) = '{$today_date}'";
				if($type == "yesterday") 	$sql .= " AND DATE(pw_posts.post_date) = '{$yesterday_date}'";
				
				if(count($pw_shop_order_status)>0){
					$pw_in_shop_os		= implode("', '",$pw_shop_order_status);
					$sql .= " AND  pw_posts.post_status IN ('{$pw_in_shop_os}')";
				}
				
				if ($pw_from_date != NULL &&  $pw_to_date != NULL && $type != "today"){
					$sql .= " AND DATE(pw_posts.post_date) BETWEEN '{$pw_from_date}' AND '{$pw_to_date}'";
				}
				
				if(count($pw_hide_os)>0){
					$in_pw_hide_os		= implode("', '",$pw_hide_os);
					$sql .= " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
				}
				
				return $order_items = $wpdb->get_row($sql);
				
				
			}
			
			public $firstorderdate=NULL;
			function pw_get_dashboard_first_orders_date($key = NULL){
				global $wpdb;
				if($this->firstorderdate){				
					return $this->firstorderdate;
				}else{
					$sql = "SELECT DATE_FORMAT(pw_posts.post_date, '%Y-%m-%d') AS 'OrderDate' FROM {$wpdb->prefix}posts  as pw_posts	WHERE pw_posts.post_type='shop_order' Order By pw_posts.post_date ASC LIMIT 1";
					return $this->firstorderdate = $wpdb->get_var($sql);
				}
			}
			
			function pw_get_dashboard_tsd($key = NULL){
				$now = time(); // or your date as well
				//$this->pw_get_dashboard_first_orders_date();
				$first_date = strtotime(($this->pw_get_dashboard_first_orders_date($key)));
				$datediff = $now - $first_date;
				$pw_total_shop_day = floor($datediff/(60*60*24));
				return $pw_total_shop_day;
			}
			
			function pw_get_dashboard_totals_orders($type = 'total',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date){
				global $wpdb;			
				
				$today_date 			= date("Y-m-d");
				$yesterday_date 		= date("Y-m-d",strtotime("-1 day",strtotime($today_date)));
				
				$sql = "
				SELECT 
				count(*) AS 'total_count'
				,SUM(pw_postmeta1.meta_value) AS 'total_amount'	
				,DATE(pw_posts.post_date) AS 'group_date'	
				FROM {$wpdb->prefix}posts as pw_posts ";
				$sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id = pw_posts.ID";
				$sql .= " WHERE  post_type='shop_order'";
				
				
				
				$sql .= " AND pw_postmeta1.meta_key='_order_total'";
				
				if($type == "today") 		$sql .= " AND DATE(pw_posts.post_date) = '{$today_date}'";
				if($type == "yesterday") 	$sql .= " AND DATE(pw_posts.post_date) = '{$yesterday_date}'";
				
				if($type == "today_yesterday"){
					$sql .= " AND (DATE(pw_posts.post_date) = '{$today_date}'";
					$sql .= " OR DATE(pw_posts.post_date) = '{$yesterday_date}')";
				}
						
				
				if(count($pw_shop_order_status)>0){
					$pw_in_shop_os		= implode("', '",$pw_shop_order_status);
					$sql .= " AND  pw_posts.post_status IN ('{$pw_in_shop_os}')";
				}
			
				
				if ($pw_from_date != NULL &&  $pw_to_date != NULL && $type != "today"){
					$sql .= " AND DATE(pw_posts.post_date) BETWEEN '{$pw_from_date}' AND '{$pw_to_date}'";
				}
				
				if(count($pw_hide_os)>0){
					$in_pw_hide_os		= implode("', '",$pw_hide_os);
					$sql .= " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
				}
				
				if($type == "today_yesterday"){
					$sql .= " GROUP BY group_date";
					$items =  $wpdb->get_results($sql);				
				}else{
					$items =  $wpdb->get_row($sql);
				}
				
				//$this->print_sql($sql);
				return $items;
			}
			
			
			function pw_get_dashboard_tbs($type = 'today',$status = 'refunded',$pw_hide_os,$pw_from_date,$pw_to_date)	{
				global $wpdb;
				$today_date 			= date("Y-m-d");
				$yesterday_date 		= date("Y-m-d",strtotime("-1 day",strtotime($today_date)));
				$sql = "SELECT";
				
				$sql .= " SUM( postmeta.meta_value) As 'total_amount', count( postmeta.post_id) AS 'total_count'";
				$sql .= "  FROM {$wpdb->prefix}posts as pw_posts";
				
				$status = "wc-".$status;
				$date_field = ($status == 'wc-refunded') ? "post_modified" : "post_date";
				
				$sql .= "
				LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=pw_posts.ID
				WHERE postmeta.meta_key = '_order_total' AND pw_posts.post_type='shop_order'";
				
				
							
				if($type == "today" || $type == "today") $sql .= " AND DATE(pw_posts.{$date_field}) = '".$today_date."'";
				if($type == "yesterday") 	$sql .=" AND DATE(pw_posts.{$date_field}) = '".$yesterday_date."'";
				
				
				if ($pw_from_date != NULL &&  $pw_to_date != NULL && $type != "today"){
					$sql .= " AND DATE(pw_posts.{$date_field}) BETWEEN '{$pw_from_date}' AND '{$pw_to_date}'";
				}
				
				if(strlen($status)>0){
					$sql .= " AND  pw_posts.post_status IN ('{$status}')";
				}
				
				if(count($pw_hide_os)>0){
					$in_pw_hide_os		= implode("', '",$pw_hide_os);
					$sql .= " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
				}
				
				$sql .= " Group BY pw_posts.post_status ORDER BY total_amount DESC";
				
				return $wpdb->get_row($sql);
			
			}
		
			function pw_get_dashboard_ttoc($type = 'total', $guest_user = false){
				global $wpdb;
				
				$today_date 			= date("Y-m-d");
				$yesterday_date 		= date("Y-m-d",strtotime("-1 day",strtotime($today_date)));
				
				$sql = "SELECT ";
				if(!$guest_user){
					$sql .= " users.ID, ";
				}
				$sql .= " pw_posts.post_date
				FROM {$wpdb->prefix}posts as pw_posts
				LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id = pw_posts.ID";
				
				if(!$guest_user){
					$sql .= " LEFT JOIN  {$wpdb->prefix}users as users ON users.ID = postmeta.meta_value";
				}
				
				$sql .= " WHERE  pw_posts.post_type = 'shop_order'";
				
				$sql .= " AND postmeta.meta_key = '_customer_user'";
				
				if($guest_user){
					$sql .= " AND postmeta.meta_value = 0";
					if($type == "today")		$sql .= " AND DATE(pw_posts.post_date) = '{$today_date}'";
					if($type == "yesterday")	$sql .= " AND DATE(pw_posts.post_date) = '{$yesterday_date}'";
				}else{
					$sql .= " AND postmeta.meta_value > 0";
					if($type == "today")		$sql .= " AND DATE(users.user_registered) = '{$today_date}'";
					if($type == "yesterday")	$sql .= " AND DATE(users.user_registered) = '{$yesterday_date}'";
				}
				
				if(!$guest_user){
					$sql .= " GROUP BY  postmeta.meta_value";
				}else{
					$sql .= " GROUP BY  pw_posts.ID";		
				}
				
				
				
				$sql .= " ORDER BY pw_posts.post_date desc";
				
				//echo $type;
				//$this->print_sql($sql);
				//
				$user =  $wpdb->get_results($sql);
				//$this->print_array($user);
				//echo "<br />";
				$count = count($user);
				//echo "<br />";
				//echo "<br />";
				return $count;
			}
			function pw_get_dashboard_time ($time, $current_time = NULL, $suffix = ''){
				if($time){
					if($current_time == NULL)
						$time = time() - $time; // to get the time since that moment
					else
						$time = $current_time - $time; // to get the time since that moment
				
					$tokens = array (
						31536000 => 'year',
						2592000 => 'month',
						604800 => 'week',
						86400 => 'day',
						3600 => 'hour',
						60 => 'minute',
						1 => 'second'
					);
				
					foreach ($tokens as $unit => $text) {
						if ($time < $unit) continue;
						$numberOfUnits = floor($time / $unit);
						return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'') .$suffix;
					}
				}else{
					return 0;
				}		
			}
			
		
			function pw_get_dashboard_boxes_generator($type, $color, $icon, $title, $amount, $amount_type, $count, $count_type, $progress_amount=NULL){
				
				$html='';
				
				if($amount_type=='price'){
					$amount=$this->price($amount);
				}
				
				if($count_type=='number' && trim($count)!=''){
					$count='#'.$count;
				}else if($count_type=='precent' && trim($count)!=''){
					$count=$count.'%';
				}
				
				if($type=='simple'){
					$html='
						<div class="col-xs-12 col-sm-6 col-md-4">
							<div class="awr-sum-item awr-sum-'.$color.' awr-'.$icon.'-ico">
								<div class="awr-txt">'.$title.'</div>
								<div class="awr-icon"></div>
								<div class="awr-sum-content">
									<div class="awr-num-big">'.$amount.'</div>
									<span>'.$count.'</span>	
								</div>
							</div><!--awr-sum-item -->
						</div>';	
				}else{
					$html='
						<div class="col-xs-12 col-sm-6 col-md-4">
							<div class="awr-sum-item awr-sum-item-progress awr-sum-'.$color.' awr-'.$icon.'-ico">
								<div class="awr-txt">'.$title.'</div>
								<div class="awr-icon"></div>
								<div class="awr-sum-content">
									<div class="awr-num-big">'.$amount.'</div>
									<span>'.$count.'</span>
								</div>
								<div class="awr-bottom-num">
									'.$progress_amount.'
								</div>
							</div><!--awr-sum-item -->
						</div>';
				}
				return $html;
			}
			
			
		}//end class
	}//end if exist
?>