<?php
/*
 * Title   : WooRed
 * Author  : Amir JM
 * Url     : http://web2webs.com/woored-pasarela-plugin-pago-gratis-wordpress-woocommerce/
 */
?>

<h3>Pago con ServiRed / RedSys</h3>

<table class="form-table">
    <?php $this->generate_settings_html(); ?>
    <tr valign="top">
    	<th class="titledesc" scope="row">
        	<label for="woocommerce_servired_modena">Moneda</label>
        </th>
        <td class="forminp">
        	<p style="margin-top:0" class="description">"<?php echo get_woocommerce_currency(); ?>" se establece a trav&eacute;s de la configuraci&oacute;n general de WooCommerce, TPV s&oacute;lo funciona con Euros, D&oacute;lares, Libras y Yenes</p>
        </td>
    </tr>
</table>

<div class="sp-footer" style="margin-top:15px;">
<a target="_blank" href="http://web2webs.com/woored-pasarela-plugin-pago-gratis-wordpress-woocommerce/">Soporte</a>
|
<a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/woored?filter=5#postform">Valorar 5 estrella</a>
|
<a target="_blank" href="http://web2webs.com/">Web2webs Plugins</a>
| WooRed Version: <?php echo woored_get_version(); ?>
</div>
