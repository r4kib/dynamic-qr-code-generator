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
        add_action('init',array($this,"register_script"));
        add_action('add_meta_boxes',array($this,"add_admin_metabox"));
        add_action('admin_enqueue_scripts',array($this,"load_admin_script"));
        add_shortcode( 'dqr_code', array($this,"shortcode_dqr_code") );

    }
    public function register_script()
    {
        wp_register_script( 'rkb-dynamic-qr-code', plugins_url( $this->plugin_name .'/js/qrcode.min.js' , dirname(__FILE__) ), array('jquery'), '1.0', true);
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
        $html = '<div id="qrcode"><span>Shortcode: [dqr_code post_id="'.$post_id.'"]</span></div>';
        $html .= $this->get_qrcode_js($url,200,"qrcode");
        echo $html;
    }
    public function load_admin_script()
    {
        wp_enqueue_script('rkb-dynamic-qr-code');
    }
    public function get_qrcode_js($url, $size=null, $js_id=null,$color= null,$bgcolor= null)
    {
        if(null === $js_id){
            $js_id = "qrcode";
        }
        if(null === $size){
            $size = 100;
        }
        if(null === $color){
            $color = '#000000';
        }
        if(null === $bgcolor){
            $bgcolor = '#ffffff';
        }
        $script_format='<script type="text/javascript">
        jQuery( document ).ready(function() {
            var qrcode = new QRCode(document.getElementById("%s"), {
                width : %d,
                height : %d,
                colorDark: "%s",
                colorLight: "%s",
                correctLevel : QRCode.CorrectLevel.H
            });
            qrcode.makeCode("%s");
        });
        </script>';
        
       return sprintf($script_format,$js_id,$size,$size,$color,$bgcolor,$url);
    }
    public function shortcode_dqr_code($atts)
    {
        $args = shortcode_atts( array(
            'post_id' => '0',
            'url' => '',
            'size' =>'200',
            'color' =>'#000000',
            'bgcolor'=> '#ffffff'
        ), $atts );

        if($args['url']==''){
            if($args['post_id']=="0"){
                //no url or post id provided in shortcode return current page permalink
                $url= get_permalink();
                $js_id="qrcode-current";
            }else{
                $url= get_permalink($args['post_id']);
                $js_id="qrcode-".$args['post_id'];
                
            }
            
        }else{
            $url = $args['url'];
            $js_id="qrcode-".substr(md5($args['url']), 0, 8);
        }
        wp_enqueue_script('rkb-dynamic-qr-code');
        $html = '<span id="'.$js_id.'"></span>';
        $html .= $this->get_qrcode_js($url,$args['size'],$js_id,$args['color'],$args['bgcolor']);
        return $html;
    }
 }
 $rkb_dynamic_qr_code= new rkbDynamicQrCode();
 
?>