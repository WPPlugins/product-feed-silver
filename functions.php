<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include_once( 'settings.php' );						 

class Product
	{
	public $id;				// <g:id></g:id>														v
	public $gtin;			// <g:gtin></g:gtin> EAN|JAN|UPC|ISBN									v
	public $title;			// <title></title>														v
	public $description;	// <description>up to 1000-1500 characters</description> 				v
	public $brand;			// <g:brand></g:brand>													v
	public $sku;			// <g:mpn>sku</g:mpn>													v
	public $price;			// <g:price></g:price>													v
	public $link;			// <link></link>   														v
	public $availability; 	// <g:availability></g:availability> [preorder|in stock|out of stock] 	v
	public $google_category;// <g:google_product_category> https://support.google.com/merchants/answer/1705911	v
	public $product_type;	// <g:product_type>main categoy > subcategory > specific category</g:product_type>	v
	public $image_link;		// <g:image_link>https://yourshop.com/images/image_1.jpg</g:image_link>				v
	public $condition;		// <g:condition>new|refurbished|used</g:condition>									v
	public $shipping_price;		// x
	public $shipping_name;		// x
	public $shipping_country;	// x


	function __construct ( $id, $gtin, $title, $description, $brand, $sku, $price, $link, $availability, $google_category, $product_type, $image_link, $condition, $shipping_price, $shipping_name, $shipping_country )
		{
			$this->id = $id;			
			$this->gtin = $gtin;		
			$this->title = $title;	
			$this->description = $description;
			$this->brand = $brand;
			$this->sku = $sku;					
			$this->price = $price;		
			$this->link = $link;
			$this->availability = $availability;
			$this->google_category = $google_category;
			$this->product_type = $product_type;
			$this->image_link = $image_link;
			$this->condition = $condition;
			$this->shipping_price = $shipping_price;
			$this->shipping_name = $shipping_name;
			$this->shipping_country = $shipping_country;
		}
	}


function get_products()
	{

		$count = 0;
		$products = array();
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1
			);
		
		$get_products = new WP_Query($args);
		
		while ( $get_products->have_posts() ) :
			$get_products->the_post();
			
			$id = get_the_id();
			$description =   get_the_excerpt();
			$title = get_the_title();
			
			unset($product_type);
			$getTags = get_the_terms( $id, 'product_cat' );
			$n = 1;
			foreach ($getTags as $tag)
			    {
			        $product_type .= $tag->name;
			        $n++;
			        if ($n > 0)
			            {
			                $product_type .= " > ";
			            }
			    }
			$product_type = substr($product_type, 0, -3);
			$wc_product = new WC_Product( $id );
			
			if ( get_option('pf_silver_gtin') == 'on')
			    {
			        $gtin = $wc_product->sku;
			        $sku = $wc_product->sku;
			    }
			else
			    {
			        $gtin = NULL;
					$sku = $wc_product->sku;
			    }
			$brand = get_the_terms( $id, 'product_cat' );
			$brand = esc_attr( $brand[0]->name );
			
			$price = $wc_product->get_display_price() . " " . get_option('pf_silver_currency');
			$link = get_permalink();
			if ( $wc_product->is_in_stock() == TRUE )
			    {
			        $availability = 'in stock';
			    }
			else
			    {
			        $availability = 'out of stock';
			    }  
			$google_category = get_option( 'pf_silver_google_product_category_id' );
			$image_link = wp_get_attachment_url( get_post_thumbnail_id( $id ));
			$condition = get_option( 'pf_silver_condition' );
		
			if ( get_option( 'pf_silver_enable_shipping' ) == 'on' )
				{
					$shipping_price = str_replace(",", ".", get_option( 'pf_silver_shipping_price' ));
					$shipping_name = get_option( 'pf_silver_shipping_name' );
					$shipping_country = get_option( 'pf_silver_shipping_country' );
				}
			else
			    {
			        $shipping_price = NULL;
					$shipping_name = NULL;
					$shipping_country = NULL;
				}
			$count++;

			$product = new Product(
				$id,
				$gtin,
				$title,
				$description,
				$brand,
				$sku,
				$price,
				$link,
				$availability,
				$google_category,
				$product_type,
				$image_link,
				$condition,
				$shipping_price,
				$shipping_name,
				$shipping_country
				);
			array_push($products, $product);
		endwhile;
		
		update_option('pf_silver_count_products', $count);
		return $products;
		wp_reset_postdata(); 
	}

function build_xml()
	{	
		$products = get_products();
		$xmlDoc = new DOMDocument();
		$xmlns = [
			'atom' => 'http://www.w3.org/2005/Atom',
			'g' => 'http://base.google.com/ns/1.0',
			'c' => 'http://base.google.com/cns/1.0'
			];

		$base = $xmlDoc->appendChild( $xmlDoc->createElement( "rss" ) );
		$base->setAttribute("version" , '2.0');
		$base->setAttribute("xmlns:g" , $xmlns['g']);
		$base->appendChild(
			$xmlDoc->createElement("title", get_bloginfo() ));
		$base->appendChild(
			$xmlDoc->createElement("link", get_bloginfo( 'description' ) ));
		$base->appendChild(
			$xmlDoc->createElement("description", get_bloginfo( 'url' ) ));
	
		$root = $base->appendChild ( $xmlDoc->createElement( "channel" ));
		
		// LOOP THROUGH PRODUCTS ARRAY
		foreach ($products as $product)
			{

				$productTag = $root->appendChild(
					$xmlDoc->createElement("product"));

				$productTag->appendChild(
					$xmlDoc->createElement("g:id", $product->id)
				);
				
				if ( $product->gtin != NULL )
					{
					$productTag->appendChild(
						$xmlDoc->createElement("g:gtin", $product->gtin)
					);
					}
				$title = $productTag->appendChild(
					$xmlDoc->createElement( "title" ));
						$title->appendChild($xmlDoc->createCDATASection( $product->title )); 
				
				$description = $productTag->appendChild(
					$xmlDoc->createElement( "description" ));
						$description->appendChild($xmlDoc->createCDATASection( $product->description )); 
				
				$brand = $productTag->appendChild(
					$xmlDoc->createElement( "g:brand" ));
						$brand->appendChild($xmlDoc->createCDATASection( $product->brand )); 
						
				$productTag->appendChild(
					$xmlDoc->createElement("g:mpn", $product->sku)
				);
				
				$productTag->appendChild(
					$xmlDoc->createElement("g:price", $product->price)
				);
				
				$link = $productTag->appendChild(
					$xmlDoc->createElement( "link" ));
						$link->appendChild($xmlDoc->createCDATASection( $product->link ));
				
				$availability = $productTag->appendChild(
					$xmlDoc->createElement( "g:availability" ));
						$availability->appendChild($xmlDoc->createCDATASection( $product->availability ));
						
				$productTag->appendChild(
					$xmlDoc->createElement("g:google_product_category", $product->google_category)
				);
				
				$productType = $productTag->appendChild(
					$xmlDoc->createElement( "g:product_type" ));
						$productType->appendChild($xmlDoc->createCDATASection( $product->product_type ));
						
				$imageLink = $productTag->appendChild(
					$xmlDoc->createElement( "g:image_link" ));
						$imageLink->appendChild($xmlDoc->createCDATASection( $product->image_link ));
				
				$productTag->appendChild(
					$xmlDoc->createElement("g:condition", $product->condition)
				);
				
				if ( $product->shipping_name != NULL )
					{
					$shipping = $productTag->appendChild(
						$xmlDoc->createElement("g:shippping", '')
					);
					$shipping_country = $shipping->appendChild(
					    $xmlDoc->createElement("g:country", $product->shipping_country)
					);
					$shipping_service = $shipping->appendChild(
					    $xmlDoc->createElement("g:service", $product->shipping_name)
					);
					$shipping_price = $shipping->appendChild(
					    $xmlDoc->createElement("g:price", $product->shipping_price)
					);
					} // END SHIPPING
			}
		$xmlDoc->preserveWhiteSpace = false;
		$xmlDoc->formatOutput = true;
		$dir = plugin_dir_path( __FILE__ );
	    if ( $xmlDoc->save($dir . 'product_feed_silver.xml') > 100 )
	        {
	            update_option('pf_silver_create_xml', 'success');
				return TRUE;
	        }
	    else
	        {
	            update_option('pf_silver_count_products', 'fail');
				return FALSE;
			}
	}
?>