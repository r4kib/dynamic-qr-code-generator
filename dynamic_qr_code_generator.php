<?php
/**
 * Plugin Name: Dyanomic QR Code Generator WordPress
 * Plugin URI: https://rakibh.com/
 * Description: Generate QR code for every page, post or whole Wordpress site. Share Smartly.
 * Version: 0.0.1
 * Author: Rakib Hasan
 * Author URI: https://rakibh.com/
 * Text Domain: dynamic_qr_code_generator
 * License: GPL2
 */

 class rkbDynamicQrCode 
 {
public $plugin_name ="dynamic_qr_code_generator";
public $txt_domain ="dynamic_qr_code_generator";
    public function  __construct()
    {
        add_action('add_meta_boxes',array($this,"add_admin_metabox"));
        add_action('admin_enqueue_scripts',array($this,"load_admin_script"));

    } 
    public function add_admin_metabox()
    {
        $screens=array('post','page');
        foreach($screens as $screen){
            add_meta_box('cm_qrcode', __('QR code for the permalink',$this->txt_domain), array($this,'admin_metabox_html'), $screen,'side','high');
        }
    }

    public function admin_metabox_html()
    {
        $post_id = $_REQUEST['post'];
        $url=get_permalink();
        $html = '<div id="qrcode"><span>Shortcode: [dqr_code id="'.$post_id.'"]</span></div>';
        $html .= $this->get_qrcode_js($url,200,"qrcode");
        echo $html;
    }
    public function load_admin_script()
    {
        wp_enqueue_script( 'rkb-dynamic-qr-code', plugins_url( $this->plugin_name .'/js/qrcode.min.js' , dirname(__FILE__) ) );
    }
    public function get_qrcode_js($url, $size=null, $js_id=null)
    {
        if(null === $js_id){
            $js_id = "qrcode";
        }
        if(null === $size){
            $size = 100;
        }
        $script_format='<script type="text/javascript">
        var qrcode = new QRCode(document.getElementById("%s"), {
            width : %d,
            height : %d
        });
        qrcode.makeCode("%s");
        </script>';
        
       return sprintf($script_format,$js_id,$size,$size,$url);
    }
 }
 $rkb_dynamic_qr_code= new rkbDynamicQrCode();
 
?>