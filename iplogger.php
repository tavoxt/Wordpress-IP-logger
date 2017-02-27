<?php
/*
Plugin Name: IP Logger
Plugin URI: 
Description: Logs IP adresses of visitors.
Author: 
Version: 1.0
Author URI: 
License: GPLv2
*/


require_once ABSPATH.'wp-config.php';
require ABSPATH.'/wp-content/plugins/Wordpress-IP-logger/createTables.php';
require ABSPATH.'/wp-content/plugins/Wordpress-IP-logger/getUserIP.php';
require ABSPATH.'/wp-content/plugins/Wordpress-IP-logger/insertOptionalIPs.php';
require ABSPATH.'/wp-content/plugins/Wordpress-IP-logger/drawForm.php';
require ABSPATH.'/wp-content/plugins/Wordpress-IP-logger/drawOptional.php';
require ABSPATH.'/wp-content/plugins/Wordpress-IP-logger/drawLogged.php';

add_action('admin_menu', 'admin_actions');
add_action('init', 'create_tables');
add_action('init', 'createOption');
add_action('wp_head', 'getUserIP');
add_action( 'admin_footer', 'ajaxCall' ); 

function admin_actions(){
  add_menu_page('IP logger', 'IP logger', 'manage_options', __FILE__, 'iplogger_admin');
}

function ajaxCall() { ?>
  <script type="text/javascript" >

  jQuery(document).ready(function($) {

    $('.deleteRow').on('click',function(e) {

      var tr = $(event.currentTarget).parent().parent().data('id');
      // fix the "Feature" by returning multiple values in ajax request
      var data = {
                  'action': 'deleteRow',
                  'ID': tr
                   };

      jQuery.post(ajaxurl, data, function(response) {
        $('#optional tr[data-id="'+tr+'"]').remove();
            });
      });

      var rowCount = $('#optional tbody tr').length;

    $("#addAddress").on("submit", function(e) {
      var IP = jQuery("#dname").val()
      e.preventDefault();
      var data = {
                  'action': 'insertIP',
                  'ROUTE': IP
                   };

      jQuery.post(ajaxurl, data, function(response) {
          $('#optional').append("<tr>" + "<td>" + (++rowCount) + "</td>" + "<td>" + response + "</td>" +  "<td><button class='button-secondary deleteRow' type='button'>Delete</button></td>" + "</tr>");
            });
      });

    $(".checkIPs").on("change", "input:radio", function(e){
  
      var which = $('input[name="logIP"]:checked').val();

      var data = {
                  'action': 'whichIpToLog',
                  'logIP': which
                   };

      jQuery.post(ajaxurl, data, function(response) {
        $("#logThis").text("Currently logging" + response+ " IP's");
            });
      });
  

});
  </script> <?php
}

function iplogger_admin(){

   echo '<div class="wrap">';

   drawForm();
   drawOptional();
   drawLogged();

   echo '</div>';

  }
?>
