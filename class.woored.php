<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Title   : WooRed
 * Author  : Amir JM
 * Url     : http://web2webs.com/woored-pasarela-plugin-pago-gratis-wordpress-woocommerce/
 */

class WC_ServiRed extends WC_Payment_Gateway 
{
    protected $order                    = null;
	protected $entorno                  = '';
	protected $fuc                      = '';
	protected $clave                    = '';
    protected $terminal                 = '';
    protected $moneda                   = '';
    protected $idioma                   = '';
	    
    public function __construct() 
    { 
		global $woocommerce;
		
        $this->id            = 'servired';
		$this->icon          = plugins_url( 'img/modulo-servired.jpg' , __FILE__ );
		$this->method_title  = 'ServiRed / RedSys';
        $this->has_fields    = false;        
        $this->init_form_fields();
        $this->init_settings();
        $this->entorno       = $this->settings['entorno' ];
        $this->title         = $this->settings['title'   ];
        $this->fuc           = $this->settings['fuc'     ];
        $this->clave         = $this->settings['clave'   ];
        $this->terminal      = $this->settings['terminal'];
		$this->idioma        = $this->settings['idioma'  ];
		$this->description   = (!$this->settings['description']) ? 'Pagar a trav&eacute;s de ServiRed. usted puede pagar con su tarjeta de cr&eacute;dito/d&eacute;bito a trav&eacute;s de la plataforma segura de ServiRed' : $this->settings['description'];
		
		if (get_woocommerce_currency()=="GBP") $this->moneda = '826';
		elseif (get_woocommerce_currency()=="EUR") $this->moneda = '978';
		elseif (get_woocommerce_currency()=="USD") $this->moneda = '840';
		elseif (get_woocommerce_currency()=="JPY") $this->moneda = '392';
		if ( !$this->is_valid_for_use() ) $this->enabled = false;
		
		if ( 'yes' == $this->debug ) $this->log = $woocommerce->logger();	  
		
		add_action('init', array(&$this, 'check_servired_response'));
		add_action('woocommerce_receipt_servired', array(&$this, 'receipt_page'));
      	add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options'));		
    }
	
	function is_valid_for_use() 
	{
        if ( ! in_array( get_woocommerce_currency(), array( 'USD', 'EUR', 'JPY', 'GBP' ) ) ) return false;
        return true;
    }
    
    function init_form_fields() 
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => 'Habilitar/Deshabilitar', 
                'type' => 'checkbox', 
                'label' => 'Habilitar pasarela de pago ServiRed / RedSys', 
                'default' => 'yes'
            ), 
            'entorno' => array(
                'title' => 'Entorno', 
                'type' => 'select', 
				'default' => 'https://sis.redsys.es/sis/realizarPago',
				'options' => array(
					'https://sis.redsys.es/sis/realizarPago' => 'Real',			   
					'https://sis-t.redsys.es:25443/sis/realizarPago' => 'Pruebas'
				)
            ), 
            'title' => array(
                'title' => 'Titulo', 
                'type' => 'text', 
                'description' => 'controla el t&iacute;tulo que el usuario ve durante pago y env&iacute;o.', 
                'default' => 'Pasarela de pago ServiRed'
            ),
			'description' => array(
                'title' => 'Descripci&oacute;n', 
                'type' => 'textarea', 
                'description' => 'la descripci&oacute;n que los usuarios ver&aacute;n durante la finalizaci&oacute;n de la compra.'
            ),
            'fuc' => array(
                'title' => 'Numero de comercio', 
                'type' => 'text'
            ),
            'clave' => array(
                'title' => 'Clave', 
                'type' => 'text'
            ),
            'terminal' => array(
                'title' => 'Terminal', 
                'type' => 'text'
            ),
            'idioma' => array(
                'title' => 'Idioma', 
                'type' => 'select', 
                'default' => '1',
				'description' => 'se establece el idioma en la p&aacute;gina web de TPV.',
				'options' => array(
					'1'   => 'Castellano',			   
					'2'   => 'Ingl&eacute;s',
					'3'   => 'Catal&aacute;n',			   
					'4'   => 'Franc&eacute;s',
					'5'   => 'Alem&aacute;n',			   
					'7'   => 'Italiano',
					'9'   => 'Portugu&eacute;s',			   
					'13'  => 'Euskera',			   
					'643' => 'Ruso'
				)
            )
       );
    }
    
    public function admin_options() 
    {
        include_once('form.admin.php');
    }
	
	function receipt_page( $order ) 
	{
		echo '<p>Gracias por su compra, por favor haga clic en el bot&oacute;n de continuar para terminar el pago.</p>';
		echo $this->generate_servired_form( $order );	
	}
    
    function process_payment($order_id) 
    {
    	$order = new WC_Order( $order_id );
        return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay' ))))
		);
		
    }
	
	function check_servired_response()
	{
		if(isset($_REQUEST['Ds_Signature']) && isset($_REQUEST['Ds_Order']) && isset($_REQUEST['Ds_Merchant_Order'])) {
            $order_id = explode('D', $_REQUEST['Ds_Merchant_Order']);
            $order_id = (int)$order_id[1];
			//$woo_order_id = $_REQUEST['order'];
			$order = new WC_Order( $order_id );
			$digest = strtoupper(sha1($_REQUEST['Ds_Amount'].$_REQUEST['Ds_Order'].$this->fuc.$_REQUEST['Ds_Currency'].$_REQUEST['Ds_Response'].$this->clave));
			if ($digest == $_REQUEST['Ds_Signature']) $order -> payment_complete();
		}
    }
			
	function generate_servired_form( $order_id ) 
	{
		$order = new WC_Order( $order_id );
		$total = $order->get_total();
		
		$data = array();
		$clave = $this->clave;
		$servired_url = $this->entorno;
		
		$data['Ds_Merchant_Currency'] = $this->moneda;
		$data['Ds_Merchant_Order'] = substr((substr(md5(uniqid(rand())),0,4).'D'.$order_id),0,12); 
		$data['Ds_Merchant_MerchantCode'] = $this->fuc;
		$data['Ds_Merchant_MerchantURL'] = $this->get_return_url( $order );
		$data['Ds_Merchant_UrlOK'] = $this->get_return_url( $order );
		$data['Ds_Merchant_UrlKO'] = $order->get_cancel_order_url();
		$data['Ds_Merchant_ConsumerLanguage'] = $this->idioma;
		$data['Ds_Merchant_Terminal'] = $this->terminal;
		$data['Ds_Merchant_TransactionType'] = 0;
		$data['Ds_Merchant_Amount'] = ($this->moneda == '978') ? intval($total * 100) : intval($total);
		$data['Ds_Merchant_MerchantSignature'] = strtoupper(sha1($data['Ds_Merchant_Amount'].$data['Ds_Merchant_Order'].$data['Ds_Merchant_MerchantCode'].$data['Ds_Merchant_Currency'].$data['Ds_Merchant_TransactionType'].$data['Ds_Merchant_MerchantURL'].$clave));
		
		$submit_tag = 'Validar el Pago';
			
		// Go Cyberpac
		$output = "<form id=\"servired_form\" name=\"servired_form\" method=\"post\" action=\"$servired_url\">\n";
		foreach($data as $n=>$v) {	$output .= "<input type=\"hidden\" name=\"$n\" value=\"$v\" />\n";	}
		$output .= "<noscript><input type=\"submit\" value=\"$submit_tag\" /></noscript></form>";
		$output .= "<script language=\"javascript\" type=\"text/javascript\">document.getElementById('servired_form').submit();</script>";

		return $output;

	}    

}

function add_woored_gateway($methods) 
{
    array_push($methods, 'WC_ServiRed'); 
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_woored_gateway');