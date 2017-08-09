$j=jQuery.noConflict();

$j(document).ready(function(){

var $shippingOptions = $j('#pf_silver_enable_shipping').val();

if ( $shippingOptions == 'off' )
	{
		$j( ".shipping_options" ).hide();
	}

$j( "#pf_silver_enable_shipping" ).change( 
	function(){
			$shippingOptions = this.value;
			if ( $shippingOptions == "on" )
				{
					$j( ".shipping_options" ).show();
				}
			else
				{
					$j( ".shipping_options" ).hide();
				}
		});

$j( "#pf_silver_google_product_category_id" ).change(
    function(){
        var $googleProductCategory = this.value;
        if (!$j.isNumeric($googleProductCategory))
            {
                $j( "#pf_silver_google_product_category_id" ).css({ 'border' : '1px solid red', 'color' : 'red' });
                alert( "The Google Product Category ID should be numeric (like '1223' or '56', your input: '" + $googleProductCategory + "' contains other characters than numbers." );
            }
        if ($j.isNumeric($googleProductCategory))
            {
                $j( "#pf_silver_google_product_category_id" ).css({ 'border' : '1px solid ', 'border-color' : '#dddddd', 'color' : '#32373c' });
            }
    });

$j( "#pf_silver_currency" ).change(
    function(){
        // alert("works!");
        var currency = this.value;
        var checkCurrency = /^([a-zA-Z]{3})$/;
        
        if(!checkCurrency.test(currency)) 
            {
                $j( "#pf_silver_currency" ).css({ 'border' : '1px solid red', 'color' : 'red' });
                alert( "Use the 3 letter currency code from ISO 4217 spec (like 'EUR', 'USD' or 'GBP', your input: '" + currency + "' is not 3 letters." );
            }
        if(checkCurrency.test(currency)) 
            {
                $j( "#pf_silver_currency" ).css({ 'border' : '1px solid ', 'border-color' : '#dddddd', 'color' : '#32373c' });
            }
    });

});