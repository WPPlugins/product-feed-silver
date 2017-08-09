<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
add_action( 'admin_action_pf_silver', 'pf_silver_admin_action' );

include_once( 'product-feed-silver.php' );				// building WP admin pages

function pf_silver_admin_action()
	{
		if ( isset($_POST['submit']) )
			{
				$cron_int = get_option('pf_silver_cron');
				$new_cron_int = $_POST['pf_silver_cron'];
				if ( $new_cron_int != $cron_int )
					{
					    if ( $new_cron_int != 'off' )
					        {
                                wp_clear_scheduled_hook( 'pf_silver_hook' );
                                wp_schedule_event( time(), $new_cron_int, 'pf_silver_hook' );
								build_xml();
                            }
                        else
                            {
                                wp_clear_scheduled_hook( 'pf_silver_hook' );
                            }
                    }
				foreach($_POST as $key => $val)
					{
					update_option($key, $val);
					}
			wp_redirect( $_SERVER['HTTP_REFERER'] );
		    exit();
			}
		
		if ( isset($_POST['build']) )
			{
				if ( build_xml() )
					{
						update_option('pf_silver_create_xml', 'success');
					}
				else
					{
						update_option('pf_silver_create_xml', 'failed');
					}
			wp_redirect( $_SERVER['HTTP_REFERER'] );
		    exit();	
			}		
	}


function pf_silver_menu_options() {
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	$pf_silver_activate = get_option('pf_silver_activate');
	$pf_silver_cron = get_option('pf_silver_cron');
	$pf_silver_create_xml = get_option('pf_silver_create_xml');
	$pf_silver_count_products = get_option('pf_silver_count_products');
	$pf_silver_currency = get_option('pf_silver_currency');	
	$pf_silver_sale_prices = get_option('pf_silver_sale_prices');	
	$pf_silver_gtin = get_option('pf_silver_gtin');		
	$pf_silver_condition = get_option('pf_silver_condition');
	$pf_silver_google_product_category_id = get_option( 'pf_silver_google_product_category_id' );
	$pf_silver_shipping_country = get_option( 'pf_silver_shipping_country' );
	$pf_silver_shipping_price = get_option( 'pf_silver_shipping_price' );
	$pf_silver_shipping_name = get_option( 'pf_silver_shipping_name' );
	$pf_silver_enable_shipping = get_option( 'pf_silver_enable_shipping' );
	$dir = plugin_dir_url( __FILE__ );
	$pf_silver_feed_loc = $dir . 'product_feed_silver.xml';
	$test = wp_next_scheduled( 'pf_silver_hook' );
	$eta = $test-time();

	echo "<div class='wrap'>
			<img src='".$dir."images/silver_32px.png' class='alignleft' style='margin: -5px 0 0 0;' />
	        <h2>Murphsy.com Product Feed Silver</h2>";
	echo "
	<ol>
	    <li>Select an update interval, it's 'off' by default</li>
	    <li>Input / change settings to your liking</li>
	    <li>Input the correct Google Product Category Id (required!)</li>
	    <li>Submit your settings and your feed will be created!</li>
	    <li>Please <a href='https://wordpress.org/plugins/product-feed-silver/' target=_blank>rate the plugin at the WordPress Plugin Directory ></a></li>
	</ol>
	<h3>How is it going so far?</h3>
	<table>
	    <form action='" . admin_url( 'admin.php' ) . "' method='post'>
		<tr>
			<th>Job to do</th>
			<th>Progress</th>
			<th></th>
		</tr>
		<tr>
			<td>Create xml-file</td>
			<td>";
			if ( $pf_silver_create_xml == "fail" )
				{
					echo "<div style='padding: 4px; border: 3px solid #4F0004; color: #4F0004; background-color: #B55F5D;'>failed</div></td><td><p>No product feed yet!</p>";
				}
			elseif ( $pf_silver_create_xml == "success")
				{
					echo "<div style='padding: 4px; color: #0D4707; border: 3px solid #0D4707; background-color: #69A263;'>succeeded</div></td><td>";
				}
			else
			    {
			        echo "<div style='padding: 4px; color: #CB7A00; border: 3px solid #CB7A00; background-color: #F9C77C;'>not enabled</div>";
			    }
			echo "</td>
		</tr>
		<tr>
		    <td>Next feed update</td>
		    <td><div style='padding: 4px; color: #000; border: 1px solid #ccc; background-color: transparant;'>";
		if ( $eta + time() == 0 )
		    {
		        echo "Product Feed Silver is switched off";
		    }
		elseif ( $eta < 0 )
		    {
		        echo "Now!";
		    }
		else
		    {
		        echo $eta . " seconds";
		    }
		echo "</div></td>
		    <td>
		    </td>
		</tr>
		<tr>
			<td>How many products have we counted?</td>
			<td>";
			if ( $pf_silver_count_products <= 500)
				{
				echo "<div style='padding: 4px; color: #000; border: 1px solid #ccc; background-color: transparant;'>$pf_silver_count_products</div>";
				}
			else
				{
				echo "<div style='padding: 4px; border: 1px solid #4F0004; color: #4F0004; background-color: #B55F5D;'>$pf_silver_count_products</div>";
				}
		echo "</td><td></td>
		</tr>
		<tr>
		    <td colspan='3'></td>
		</tr>
	</table>";
    echo "<p><i>Product Feed Silver has been tested up to 500 products, if you are above that we cannot guarantee that all products will be added. Or if the feed will work at all. Check your Google Merchant Center or the Product Feed to see how many products got through.</i></p>
    <p>The actual product feed: ";
    if ( $pf_silver_create_xml == "fail" )
        {
			echo "<span style='color: #4F0004;'>No product feed yet!</span></p>";
		}
	elseif ( $pf_silver_create_xml == "success")
		{
			echo "<span><a href='$pf_silver_feed_loc' target=_blank>$pf_silver_feed_loc</a></span></p>";
		}
	else
	    {
	        echo "<span style='color: #CB7A00;'>not enabled</span></p>";
	    }
	echo "
	<div style='border: 1px solid #222; padding: 6px 15px 10px 15px; background: #eaeaea;'>
	<h2>Product Feed settings</h2>";
	echo "<p>Google forces us to jump through quite a lot of hoops before they'll accept your product feed for use with Google Shopping / Merchant Center. We can't create the perfect feed without some more input.</p>
	<h4>General settings</h4>
	";
	echo "<table>";		
	echo "<tr>
			<td>Update interval</td>
			<td>
				<select name='pf_silver_cron' id='pf_silver_cron'>
					<option value='off'"; if ( $pf_silver_cron == "off" ) { echo "selected='selected'"; } echo ">off</option>";
					echo "
					<option value='hourly'"; if ( $pf_silver_cron == "hourly" ) { echo "selected='selected'"; } echo ">hourly</option>
					<option value='twicedaily'"; if ( $pf_silver_cron == "twicedaily" ) { echo "selected='selected'"; } echo ">twice per day</option>
					<option value='daily'"; if ( $pf_silver_cron == "daily" ) { echo "selected='selected'"; } echo ">daily</option>
				</select>
			</td>
			<td>
				<p>Changing the update interval will cause Product Feed Silver to update the feed immediately and schedule the next update for your chosen interval.</p>
			</td>
		</tr>
		<tr>
			<td>Currency <a href='https://en.wikipedia.org/wiki/ISO_4217' target=_blank>follow ISO 4217 rules</a></td>
			<td>
				<input type='text' name='pf_silver_currency' id='pf_silver_currency' value='$pf_silver_currency' />
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td>Use gtin <a href='https://support.google.com/merchants/answer/160161' target=_blank>read more</a></td>
			<td>
				<select name='pf_silver_gtin' id='pf_silver_gtin'>
					<option value='on'"; if ( $pf_silver_gtin == "on" ) { echo "selected='selected'"; } echo ">active</option>
					<option value='off'"; if ( $pf_silver_gtin == "off" ) { echo "selected='selected'"; } echo ">inactive</option>
				</select>
			</td>
			<td>
			    <p>If you select 'active' we'll fill the gtin field in your XML feed with the value you entered into 'General > SKU' on the product page. This value is also used for mpn.</p>
			</td>
		</tr>
		<tr>
			<td>Are you selling new products?</td>
			<td>
				<select name='pf_silver_condition' id='pf_silver_condition'>
					<option value='new'"; if ( $pf_silver_condition == "new" ) { echo "selected='selected'"; } echo ">new</option>
					<option value='used'"; if ( $pf_silver_condition == "used" ) { echo "selected='selected'"; } echo ">used</option>
					<option value='used'"; if ( $pf_silver_condition == "refurbished" ) { echo "selected='selected'"; } echo ">refurbished</option>
				</select>
			</td>
			<td>
			</td>
		</tr>
		<tr>
		    <td>Google Product Category ID <a href='https://support.google.com/merchants/answer/1705911' target=_blank>look it up here</a></td>
		    <td><input type='text' value='".$pf_silver_google_product_category_id."' name='pf_silver_google_product_category_id' id='pf_silver_google_product_category_id' /></td>
		    <td>
		        <p>We currently only support 1 Google Product Category for the whole feed. We're working on expanding this functionality. <a href='http://goo.gl/forms/Re9kyVogcp' target=_blank>Please fill in our survey if you really need this option.</a></p>
			</td>
		</tr>
		<tr>
			<td colspan='3'>
				<h4>Shipping settings (all optional)</h4>
				<p>We <b>strongly</b> advise you to set your shipping settings via the Google Merchant Center account settings.</p>
			</td>
		</tr>
		<tr>
		    <td>Use shipping settings (overrides Google Merchant Center settings!)</td>
		    <td>
		    <select name='pf_silver_enable_shipping' id='pf_silver_enable_shipping'>
					<option value='on'"; if ( $pf_silver_enable_shipping == "on" ) { echo "selected='selected'"; } echo ">on</option>
					<option value='off'"; if ( $pf_silver_enable_shipping == "off" ) { echo "selected='selected'"; } echo ">off (recommended)</option>
			</select>
		    </td>
		    <td></td>
		</tr>
			<tr class='shipping_options'>
				<td>Shipping name (optional)</td>
				<td><input type='text' value='".$pf_silver_shipping_name."' name='pf_silver_shipping_name' id='pf_silver_shipping_name' /></td>
				<td>
					<p>Name for shipping method. <b>Will be added to all products and overrides Google Merchant Center account settings!</b></p>
				</td>
			</tr>
			<tr class='shipping_options'>
				<td>Shipping country (optional) <a href='https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2' target=_blank>Use ISO 3166-1 standard</a></td>
				<td><input type='text' value='".$pf_silver_shipping_country."' name='pf_silver_shipping_country' id='pf_silver_shipping_country' /></td>
				<td>
					<p>Country where you will ship to. <b>Will be added to all products and overrides Google Merchant Center account settings!</b></p>
				</td>
			</tr>
			<tr class='shipping_options'>
				<td>Shipping price (optional)</td>
				<td><input type='text' value='".$pf_silver_shipping_price."' name='pf_silver_shipping_price' id='pf_silver_shipping_price' /></td>
				<td>
					<p>How much does shipping cost? <b>Will be added to all products and overrides Google Merchant Center account settings!</b></p>
				</td>
			</tr>
		<tr>
			<td colspan='3'>
				<input type='hidden' name='action' value='pf_silver' />
				<input type='submit' name='submit' id='submit' style='-webkit-appearance: none; padding: 5px;' />
			</td>
		</tr>";
	echo "
		
		</table>
		</div>
		<div style='margin: 12px 0 12px 0;'>
	    	<input type='submit' id='build' name='build' value='I&#8217;m desperate, build the XML manually' style='-webkit-appearance: none; padding: 5px;' />
		    </form>
		</div>
		";
	echo "
		<h2>Enjoying Murphsy.com Product Feed Silver?</h2>
		<p>Product Feed Silver is completely free. Are you using it so sell tons of stuff? Or simply using (snippets of) our code to build something better? We would love a small donation to help us pay the bills. If you can't spare a dime, no worries! Instead you could provide us with some feedback to make this plugin even better: <a href='http://goo.gl/forms/mP1a2O01gh' target='_blank'>Take me to the 3 minute mini-survey ></a></p>	
			<div class=''>
				<form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_top'>
					<input type='hidden' name='cmd' value='_s-xclick'>
					<input type='hidden' name='hosted_button_id' value='DBZU2G47W9Y8A'>
					<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
					<img alt='' border='0' src='https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif' width='1' height='1'>
			    </form>
			</div>";
	echo "</div>"; // END CLASS='WRAP'
}

function pf_silver_structure_page(){
    if (!current_user_can('manage_options'))
        {
        wp_die( __('You do not have sufficient permissions to access this page.') );
        }
    echo "
        <div class='wrap'>
        <h2>Where does the data for the feed come from?</h2>
	    <p>Below you'll find an overview of every field in the feed and how we've filled it for you.</p>
	    <table>
	        <tr>
	            <th>XML-feed field</th>
	            <th>How we filled it</th>
	        </tr>
	        <tr>
	            <td>g:id</td>
	            <td>post id / product id (same thing)</td>
	        </tr>
	        <tr>
	            <td>g:gtin (optional)</td>
	            <td>Woocommerce product SKU</td>
	        </tr>
	        <tr>
	            <td>title</td>
	            <td>product title</td>
	        </tr>
	        <tr>
	            <td>description</td>
	            <td>excerpt (short description)</td>
	        </tr>
	        <tr>
	            <td>g:brand</td>
	            <td>category (highest parent category of the product)</td>
	        </tr>
	        <tr>
	            <td>g:mpn</td>
	            <td>Woocommerce SKU</td>
	        </tr>
	        <tr>
	            <td>g:price</td>
	            <td>price displayed to customer on site (takes into account Woocommerce settings!)</td>
	        </tr>
	        <tr>
	            <td>link</td>
	            <td>permalink</td>
	        </tr>
	        <tr>
	            <td>g:availability</td>
	            <td>Woocommerce stock</td>
	        </tr>
	        <tr>
	            <td>g:google_product_category</td>
	            <td>inserted via Settings</td>
	        </tr>
	        <tr>
	            <td>g:product_type</td>
	            <td>Woocommerce Product Category (full nested hierarchy)</td>
	        </tr>
	        <tr>
	            <td>g:image_link</td>
	            <td>product image</td>
	        </tr>
	        <tr>
	            <td>g:condition</td>
	            <td>inserted via Settings</td>
	        </tr>
	        <tr>
	            <td>g:country</td>
	            <td>shipping country, entered via Shipping Settings</td>
	        </tr>
	        <tr>
	            <td>g:service</td>
	            <td>shipping name, entered via Shipping Settings</td>
	        </tr>
	        <tr>
	            <td>g:price</td>
	            <td>shipping price, entered via Shipping Settings</td>
	        </tr>
	    </table>
        </div>
        ";
    }

function pf_silver_upgrade_page() {
	echo "
		<div class='wrap'>
		<h2>Thank you for using Woocommerce Product Feed Silver</h2>
		<p>We know, it's pretty basic. But it gets the core job done, doesn't it? We called it 'Silver' on purpose, it's not gold nor platinum. However if enough people actually use the plugin, we will build an upgraded version.</p>
		<p>What kind of functionality would we add to Product Feed Gold?</p>
		<ul style='text-indent: 15px; list-style: square inside;'>
			<li>Filter products based on business rules</li>
			<li>Exclude specific products via the product page</li>
			<li>Change the XML-structure to support more external tools</li>
			<li>Create multiple feeds (different ones, ofcourse)</li>
		</ul>
		<p>Does that sound like something you need? <a href='https://wordpress.org/plugins/product-feed-silver/' target=_blank>Rate us 5-stars at the Wordpress plugin page</a>. Or even better:</p>
		<h2>Take 3 minutes to answer a few questions about Product Feeds for us!</h2>
		<p><a href='http://goo.gl/forms/mP1a2O01gh' target='_blank'>Take me to the mini-survey ></a></p>
		<h2>Want to help us even more?</h2>
		<p>We've created this plugin with the goal of enabling even the smallest of Woocommerce shops to run a Google Shopping campaign. This should create a more level playing field. We like that. Do you want us to create more free plugins so everyone can enjoy the same functionalities as the big shops? A donation would help us pay the bills. Anything you can spare would be greatly appreciated!</p>
			<div class='alignleft'>
				<form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_top'>
					<input type='hidden' name='cmd' value='_s-xclick'>
					<input type='hidden' name='hosted_button_id' value='DBZU2G47W9Y8A'>
					<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
					<img alt='' border='0' src='https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif' width='1' height='1'>
				</form>
			</div>
		</div>		
		";
}
?>