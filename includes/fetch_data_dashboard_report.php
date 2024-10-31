<?php
	if($file_used=="sql_table")
	{
	}elseif($file_used=="data_table"){
		
		
		$pw_from_date=$this->pw_from_date_dashboard;
		$pw_to_date=$this->pw_to_date_dashboard;
		
		$pw_hide_os='trash';
		$pw_shop_order_status="wc-completed,wc-on-hold,wc-processing";
		
		if(isset($_POST['pw_from_date']))
		{
			//parse_str($_REQUEST, $my_array_of_vars);
			$this->search_form_fields=$_POST;

			$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
			$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
			$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
			$pw_shop_order_status	= $this->pw_get_woo_requests('shop_order_status',$pw_shop_order_status,true);

		}
		
		$date_format		= get_option( 'date_format' );
		$pw_total_shop_day 		= $this->pw_get_dashboard_tsd();
		$datetime= date_i18n("Y-m-d H:i:s");
		
		//echo $pw_total_shop_day;
		$pw_hide_os=explode(',',$pw_hide_os);		
		if(strlen($pw_shop_order_status)>0 and $pw_shop_order_status != "-1") 
			$pw_shop_order_status = explode(",",$pw_shop_order_status); 
		else $pw_shop_order_status = array();
		
		//die($pw_shop_order_status.' - '.$pw_hide_os.' - '.$pw_from_date.' - '.$pw_to_date);
		
		$total_part_refund_amt	= $this->dashboard_pw_get_por_amount('total',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$_total_orders 			= $this->pw_get_dashboard_totals_orders('total',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$total_orders 			= $this->pw_get_dashboard_value($_total_orders,'total_count',0);
		$total_sales 			= $this->pw_get_dashboard_value($_total_orders,'total_amount',0);
		
		$total_sales			= $total_sales - $total_part_refund_amt;
		
		$total_refund 			= $this->pw_get_dashboard_tbs("total","refunded",$pw_hide_os,$pw_from_date,$pw_to_date);
	
		
		$total_refund_amount 	= $this->pw_get_dashboard_value($total_refund,'total_amount',0);
		$total_refund_count 	= $this->pw_get_dashboard_value($total_refund,'total_count',0);
		
		$total_refund_amount	= $total_refund_amount + $total_part_refund_amt;

		$total_coupon 			= $this->pw_get_dashboard_totals_coupons("total",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	
		$total_coupon_amount 	= $this->pw_get_dashboard_value($total_coupon,'total_amount',0);
		$total_coupon_count 	= $this->pw_get_dashboard_value($total_coupon,'total_count',0);
	
		$total_order_tax 		= $this->pw_get_dashborad_totals_orders("total","_order_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	
		$total_ord_tax_amount	= $this->pw_get_dashboard_value($total_order_tax,'total_amount',0);
		$total_ord_tax_count 	= $this->pw_get_dashboard_value($total_order_tax,'total_count',0);
		
		$total_ord_shipping_tax	= $this->pw_get_dashborad_totals_orders("total","_order_shipping_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
	
		$total_ordshp_tax_amount= $this->pw_get_dashboard_value($total_ord_shipping_tax,'total_amount',0);
		$total_ordshp_tax_count = $this->pw_get_dashboard_value($total_ord_shipping_tax,'total_count',0);

		
		$total_tax_amount		= $total_ordshp_tax_amount + $total_ord_tax_amount;
		$total_tax_count 		= '';
	
		$users_of_blog 			= count_users();			
		$total_customer 		= isset($users_of_blog['avail_roles']['customer']) ? $users_of_blog['avail_roles']['customer'] : 0;

		$total_reg_customer 	= $this->pw_get_dashboard_ttoc('total',false);
		$total_guest_customer 	= $this->pw_get_dashboard_ttoc('total',true);
	
		
		$type='simple';
		$total_summary ='';
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'red-1', 'chart', __('Total Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_sales, 'price', $total_orders, 'number');

		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'blue-1', 'category', __('Total Refund',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_refund_amount, 'price', $total_refund_count, 'number');
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'blue-2', 'piechart', __('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_tax_amount, 'price', $total_tax_count, 'precent');
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'brown-1', 'like', __('Total Coupons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_coupon_amount, 'price', $total_coupon_count, 'number');
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'red-2', 'category', __('Total Registered',__PW_REPORT_WCREPORT_TEXTDOMAIN__), "#".$total_customer, 'other', '', 'number');
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'green-1', 'piechart', __('Total Guest Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__), "#".$total_guest_customer, 'other', '', 'number');
		
		
		//echo '<div class="clearboth"></div><div class="awr-box-title">'.__('Other Summary',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div><div class="clearboth"></div>';
		$htmls='
		<div class="tabs tabsB tabs-style-underline"> 
			<nav>
				<ul class="tab-uls">
					<li><a href="#section-bar-1" > <div><i class="fa fa-cogs"></i>'.__('Total Summary ...',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div></a></li>
				</ul>
			</nav>
			<div class="content-wrap">
				
				<section id="section-bar-1">
					'.$total_summary.'
				</section>
			  
			</div>
		</div>
		';
		echo $htmls;
		
	}elseif($file_used=="search_form"){
		$pw_from_date=$this->pw_from_date_dashboard;
		$pw_to_date=$this->pw_to_date_dashboard;

		if(isset($_POST['pw_from_date']))
		{
			$this->search_form_fields=$_POST;
			$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
			$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		}
			
	?>
		<form class='alldetails search_form_reports' action='' method='post' id="dashboard_form">
            <input type='hidden' name='action' value='submit-form' />
            <input type='hidden' name="pw_from_date" id="pwr_from_date_dashboard" value="<?php echo $pw_from_date;?>"/>
            <input type='hidden' name="pw_to_date" id="pwr_to_date_dashboard"  value="<?php echo $pw_to_date;?>"/>
            
			<div class="page-toolbar">
				
				<input type="submit" value="Search" class="button-primary"/>	
				<div id="dashboard-report-range" class="pull-right tooltips  btn-fit-height grey-salt" data-placement="top" data-original-title="Change dashboard date range">
					<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
						<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
						<span></span> <b class="caret"></b>
					</div>
				</div>
				
				<?php
					$pw_hide_os='trash';
					$pw_publish_order='no';
					
					$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
				?>
				<input type="hidden" name="list_parent_category" value="">
				<input type="hidden" name="group_by_parent_cat" value="0">
				
				<input type="hidden" name="pw_hide_os" id="pw_hide_os" value="<?php echo $pw_hide_os;?>" />
				<input type="hidden" name="publish_order" id="publish_order" value="<?php echo $pw_publish_order;?>" />
			
				<input type="hidden" name="date_format" id="date_format" value="<?php echo $data_format;?>" />
			
				<input type="hidden" name="table_name" value="<?php echo $table_name;?>"/>
				<div class="fetch_form_loading dashbord-loading"></div>	  		
				
				
			</div>
            
                        
        </form>
    <?php
	}
	
?>