<?php

/**
 * Plugin Name: WP-Contact-Form
 * Description: This things will be written in letter
 * Author: Sifat
 * Version: 1.0.0
 */
   add_action('wp_enqueue_scripts', 'enqueue_plugin_styles');

   function enqueue_plugin_styles() {
    wp_enqueue_style('my-plugin-style', plugins_url('/plugin.css', __FILE__), false, "1.0.0");

    // Start with an empty string to store dynamic CSS
    $dynamic_css = '';

    $background_color = get_option( 'wpcf_background' );
    $border_color = get_option('wpcf_border_color');
    // $border_width = get_option('wpcf_border_width');
    $border_radius = get_option('wpcf_border_radius');
    $container_border_radius = get_option( 'wpcf_container_border_radius' );
    $btn_bg = get_option( 'wpcf_btn_bg' );
    $btn_border_radius = get_option( 'wpcf_btn_border_radius' );
    $title_color = get_option( 'wpcf_title_color' );
    $btn_width = get_option( 'wpcf_btn_width' );
    $btn_text_color = get_option( 'wpcf_btn_text_color' );
    $text_color = get_option( 'wpcf_text_color' );

        $dynamic_css .= "
            .form-box {
                background-color: $background_color;
                border-radius: {$container_border_radius}px;
            }
            .title{
              color: {$title_color};
            }
            .form-box >  .box{
              border-color: $border_color;
              border-radius: {$border_radius}px;
              color: {$text_color};
            }
            .submit_btn{
              background-color: $btn_bg;
              border-radius: {$btn_border_radius}px;
              width: {$btn_width}%;
              color: {$btn_text_color};
        ";

    // Add the dynamic CSS to the main stylesheet
    if ( ! empty( $dynamic_css ) ) {
        wp_add_inline_style( 'my-plugin-style', $dynamic_css );
    }
}

 function wpcf_contents(){

    $content = '';
    $content .= '<form class="form-box" method="post" action="options.php">';
     $content .= '<h2 class="title">' . get_option( 'wpcf_title' ) . '</h2>';
      $content .= '<input type="text" class="box" name="full_name" placeholder="Full Name" />';
      $content .= '<br>';
      $content .= '<input class="box" type="text" name="email_address" placeholder="Email Address" />';
      $content .= '<br>';
      $content .= '<input class="box" type="text" name="phone_number" placeholder="Phone Number" />';
      $content .= '<br>';
      $content .= '<textarea class="box" name="comments" placeholder="Give us your comments"></textarea>';
      $content .= '<br/>';
      $content .= '<input class="submit_btn" type="submit" name="wpcf_submit_form" value="'.get_option( 'wpcf_btn_text' ).'" />';
      $content .= '<br>';
    $content .= '</form>'; 
    return $content;
   }
   add_shortcode('wpcf_shortcode', 'wpcf_contents');//The shortcode of the plugin
  
   function set_html_content_type(){
    return 'text/html';
   }

   function wpcf_capture(){
    global $post,$wpdb;
    if(array_key_exists('wpcf_submit_form', $_POST)){
      $to = print get_option('wpcf_author_email');
      $subject = "Ideapr example site form submission";
      $body = '';
      $body .= 'Name: '.$_POST['full_name']. '<br/>';
      $body .= 'Email: '.$_POST['email_address']. '<br/>';
      $body .= 'Phone: '.$_POST['phone_number']. '<br/>';
      $body .= 'Comments: '.$_POST['comments']. '<br/>';
  
      add_filter('wp_mail_content_type', 'set_html_content_type');
      wp_mail($to, $subject, $body);
      remove_filter('wp_mail_content_type','set_html_content_type');
  
      //Insert the information into a comment
      $time = current_time('mysql');
  
      $data = array(
          'comment_post_ID' => $post->ID,
          'comment_content' => $body,
          'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
          'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
          'comment_date' => $time,
          'comment_approved' => 1,
      );
      
      wp_insert_comment($data);

      //Inserting form data in database
      // $insertData = $wpdb->get_results("INSERT INTO ".$wpdb->prefix."form_data (data) VALUES ('".$body."') ");
  
  
    }
   }
   add_action('wp_head','wpcf_capture');

  

function wpcf_admin_page(){
  add_menu_page( 'wpcf-admin-page', 'WP Contact Form', 'manage_options', 'wpcf-admin-menu', 'wpcf_create_admin_page', 'dashicons-forms', 110 );
}
add_action( 'admin_menu', 'wpcf_admin_page');

function wpcf_admin_enqueue_style(){
  wp_enqueue_style('wpcf_admin_style', plugins_url( '/admin-style.css', __FILE__), false, "1.0.0");
}
add_action( 'admin_enqueue_scripts', 'wpcf_admin_enqueue_style' );

// Add a function to initialize the plugin settings
function wpcf_plugin_settings_init() {
  // Register a setting for each option you want to save
  register_setting('wpcf_plugin_options', 'wpcf_title');
  register_setting('wpcf_plugin_options', 'wpcf_title_color');
  register_setting('wpcf_plugin_options', 'wpcf_background');
  register_setting('wpcf_plugin_options', 'wpcf_container_border_radius');
  register_setting('wpcf_plugin_options', 'wpcf_border_color');
  register_setting('wpcf_plugin_options', 'wpcf_border_radius');
  register_setting('wpcf_plugin_options', 'wpcf_btn_text');
  register_setting('wpcf_plugin_options', 'wpcf_btn_text_color');
  register_setting('wpcf_plugin_options', 'wpcf_btn_bg');
  register_setting('wpcf_plugin_options', 'wpcf_btn_border_radius');
  register_setting('wpcf_plugin_options', 'wpcf_btn_width');
  register_setting('wpcf_plugin_options', 'wpcf_author_email');
  register_setting('wpcf_plugin_options', 'wpcf_text_color');
}
add_action('admin_init', 'wpcf_plugin_settings_init');


function wpcf_create_admin_page(){
  ?>
  <div class="wpcf_admin_wrapper">

    <div class="wpcf_admin_main_area">

      <h1>WP Contact Form</h1>

      <form action="options.php" method="post">
      <?php settings_fields('wpcf_plugin_options'); ?>

      <div class="form-container">
        <h3>Style your form container</h3>
        <label for="wpcf_title" name="wpcf_title">Title</label>
        <input type="text" name="wpcf_title" placeholder="form header title" value="<?php print get_option('wpcf_title') ?>">

        <label for="wpcf_title_color" name="wpcf_title_color">Title Color</label>
        <input type="color" name="wpcf_title_color" value="<?php print get_option('wpcf_title_color') ?>">

        <label for="wpcf_background" name="wpcf_background">Background Color</label>
        <input type="color" name="wpcf_background" value="<?php print get_option('wpcf_background') ?>">

        <label for="wpcf_container_border_radius" name="wpcf_container_border_radius">Border Radius</label>
        <input type="number" name="wpcf_container_border_radius" value="<?php print get_option('wpcf_container_border_radius') ?>">
      </div>


      <div class="form-contents">
        <h3>Style your input fields</h3>

        <label for="wpcf_border_color" name="wpcf_border_color">Border Color</label>
        <input type="color" name="wpcf_border_color" value="<?php print get_option('wpcf_border_color') ?>">

        <label for="wpcf_border_radius" name="wpcf_border_radius">Border Radius</label>
        <input type="number" name="wpcf_border_radius" value="<?php print get_option('wpcf_border_radius') ?>">

        <label for="wpcf_text_color" name="wpcf_text_color">Text Color</label>
        <input type="color" name="wpcf_text_color" value="<?php print get_option('wpcf_text_color') ?>">

      </div>

      <div class="wpcf_button">
        <h3>Style your button</h3>

        <label for="wpcf_btn_text" name="wpcf_btn_text">Text</label>
        <input type="input" name="wpcf_btn_text" value="<?php print get_option('wpcf_btn_text') ?>">

        <label for="wpcf_btn_text_color" name="wpcf_btn_text_color">Text</label>
        <input type="color" name="wpcf_btn_text_color" value="<?php print get_option('wpcf_btn_text_color') ?>">


        <label for="wpcf_btn_bg" name="wpcf_btn_bg">Background</label>
        <input type="color" name="wpcf_btn_bg" value="<?php print get_option('wpcf_btn_bg') ?>">
        
        <label for="wpcf_btn_border_radius" name="wpcf_btn_border_radius">Border Radius</label>
        <input type="number" name="wpcf_btn_border_radius" value="<?php print get_option('wpcf_btn_border_radius') ?>">

        <label for="wpcf_btn_width" name="wpcf_btn_width">Width</label>
        <input type="number" name="wpcf_btn_width" value="<?php print get_option('wpcf_btn_width') ?>">

      </div>      

      <label for="wpcf_author_email" name="wpcf_author_email">Author Email</label>
      <input type="email" name="wpcf_author_email" value="<?php print get_option('wpcf_author_email') ?>">

      <?php submit_button(); ?>
      </form>
      
    </div>

    <div class="wpcf_admin_sidebar">
      <h4>For more information visit our website</h4>
    </div>

  </div>

  <?php
}




  /*
  * Plugin Redirect Feature
  */
  register_activation_hook( __FILE__, 'wpcf_plugin_activation' );
  function wpcf_plugin_activation(){
    add_option('wpcf_plugin_do_activation_redirect', true);
  }

  add_action( 'admin_init', 'wpcf_plugin_redirect');
  function wpcf_plugin_redirect(){
    if(get_option('wpcf_plugin_do_activation_redirect', false)){
      delete_option('wpcf_plugin_do_activation_redirect');
      if(!isset($_GET['active-multi'])){
        wp_safe_redirect(admin_url( 'admin.php?page=wpcf-admin-menu' ));
        exit;
      }
    }
  }



?>