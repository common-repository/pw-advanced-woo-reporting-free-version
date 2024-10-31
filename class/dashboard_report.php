<?php
	global $pw_rpt_main_class;
	
	global $wpdb;
	
	$first_date= date("Y-m-d");
		$this->pw_from_date_dashboard=$first_date;
		$first_date=substr($first_date,0,4);
	
	$pw_to_date= date("Y-m-d");
	$this->pw_to_date_dashboard=$pw_to_date;
	$pw_to_date=substr($pw_to_date,0,4);
?>

<div class="wrap">
    <div class="row">
        <div class="col-xs-12">
            <div class="awr-addons-cnt awr-addones-active" style="background:#fff">
          	  <div class="awr-descicon"><i class="fa fa-shopping-cart "></i></div>
                <div class="awr-desc-content">	
                	<h3 class="awr-addones-title" style="color:#333;border-bottom:1px solid #ccc;padding-bottom:5px">Buy Pro Version</h3>
              	  <div class="awr-addnoes-desc">If you need access to all reports, you should buy the Pro Version by clicking on <strong>"Buy Pro Version"</strong>  button.</div>
                  <br />
        <a class="awr-addons-btn" href="http://codecanyon.net/item/advanced-woocommerce-reporting-/12042129?ref=proword" target="_blank" style="background: #eee;"><i class="fa fa-shopping-cart "></i>Buy Pro Version</a>
    </div>
    <div class="awr-clearboth"></div>
</div>

<div class="wrap">
	<div class="row">
		<div class="awr-box-content-tab">
			<?php
				$table_name='dashboard_report';
				$pw_rpt_main_class->search_form_html($table_name);
			?>	 
			
			<div id="target">
				<?php
					$table_name='dashboard_report';
					$pw_rpt_main_class->table_html($table_name);
				?>	
			</div>
		</div>
		
        <div class="col-md-12">
			<?php
				$table_name='top_5_products';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
		
		<div class="col-md-12">
			<?php
				$table_name='top_5_category';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
        
        
		
     </div><!--row --> 
</div>

    

<script type="text/javascript">

	jQuery( document ).ready(function( $ ) {
		
		[].slice.call( document.querySelectorAll( ".tabsA" ) ).forEach( function( el ) {
			new CBPFWTabs( el );
		});
		
		[].slice.call( document.querySelectorAll( ".tabsB" ) ).forEach( function( el ) {
			new CBPFWTabs( el );
		})
	});
</script>