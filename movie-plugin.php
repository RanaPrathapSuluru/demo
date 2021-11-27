<?php
/**
 * @package moviePlugin
 */
/*
Plugin Name: movie Plugin
Plugin URI: https://movieplugin.com/
Description: “these plugin used for the total movie information and movie updates and realse dates all are availble.” To get started: activate the movie Plugin plugin and then go to your moviePlugin Settings page to set up your API key.
Version: 4.1.9
Author: venkey
Author URI: https://venkey.com/wordpress-plugins/
License: GPLv2 or later
Text Domain: venkey-plugin
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 venkey, Inc.
*/


defined( 'ABSPATH' ) or die( 'Hey, you can access this file' );

if( ! class_exists( 'venkey' ) ) {
    class venkey {
             function __construct() {
                
                // add Shortcode

                add_shortcode( 'contact-form', array( $this, 'load_shortcode' ) );

                // load Scripts

                add_action( 'wp_footer', array( $this, 'load_scripts' ) );

                // Register REST API
                add_action( 'rest_api_init', array( $this, 'register_rest_api' ) ); 

            }

            function register() {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
           
            }

            function enqueue() {
            //enqueue all our scripts

            wp_enqueue_style( 'mypluginstyle', plugins_url( '/assets/mystyle.css', __FILE__ ) );
            wp_enqueue_style( 'myplugin_bootstrap', plugins_url( '/assets/css/bootstrap.min.css', __FILE__ ) );
            wp_enqueue_style( 'bootstrap_grid', plugins_url( '/assets/css/bootstrap-grid.css', __FILE__ ) );
            wp_enqueue_script( 'myplugin_script', plugins_url( '/assets/myscript.js',__FILE__ ) );
            wp_enqueue_script( 'myplugin_bootstrap_script', plugins_url( '/assets/js/bootstrap.min.js', __FILE__ ) );
            wp_enqueue_script( 'myplugin_bundle_script', plugins_url( '/assets/js/bootstrap.bundle.js', __FILE__ ) );
         }


         public function load_shortcode()
         { ?>

            <div class="container simple_contact_form">
            <h1>Form Details</h1>

            <form id="simple_contact_form__form">
              <div class="col-md-12">
                <div class="form-group">
                    <label for="Name"><b>Movie</b></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                
                </div>
                <div class="form-group">
                    <label for="Email"><b>Hero</b></label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                
                </div>
                    <div class="form-group">
                    <label for="phone"><b>Director</b></label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number">
                
                </div>

               
                <div class="form-group">
                    <label for="Address"><b>Address</b></label>
                    <textarea id=""  class="form-control" id="address" name="address"></textarea>
                
                </div>

                <button type="submit" class="btn btn-success btn-block">Button</button>

            </div>
        </form>
            
        </div>


        <?php } 

        public function load_scripts()
         { ?>


    
            <script>

                var nonce = '<?php echo wp_create_nonce('wp_rest');?>';

                (function($){
                
                $( '#simple_contact_form__form' ).submit( function(event) {

                    event.preventDefault();
                    
                    var name  = $("movie").val();
                    var email = $("hero").val();
                    var phone = $("director").val();
                    var address = $("address").val();
                    var form  = $(this).serialize();

                    console.log(form);

                    $.ajax({

                        method: 'post',
                        url: '<?php echo get_rest_url( null, 'simple-contact-form/v1/send-email' );?>',
                        headers: { 'X-WP-Nonce': nonce },
                        data: form
                    })

                });

            })(jQuery)

            </script>


        <?php } 

        public function register_rest_api()
        {
            register_rest_route( 'simple-contact-form/v1', 'send-email', array(

                'methods' => 'POST',
                'callback' => array( $this, 'handle_contact_form' )

            ) );
        }

        public function handle_contact_form($data)
        {   
            global $wpdb;

            $headers = $data->get_headers();
            $params  = $data->get_params();
            $nonce   = $headers['x_wp_nonce'][0];
           // $nonce = 1234567878;

            $name  = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];


            if(!wp_verify_nonce( $nonce, 'wp_rest' ))
            {
                return new WP_REST_Response( 'Message not sent', 422 );
            }

            $wpdb->query("insert into `wp_sample_contact_form` values('$name','$email','$phone','$address');");
    
            die();

           

        }

    }

    $myclassvar = new venkey();

    $myclassvar->register();

    //activation

    require_once plugin_dir_path( __FILE__ ) . 'inc/movie-plugin-activate.php';

    register_activation_hook( __FILE__, array( 'movieActivate', 'activate' ) );

    //deactivation

    require_once plugin_dir_path( __FILE__ ) . 'inc/movie-plugin-deactivate.php';

    register_deactivation_hook( __FILE__, array( 'movieDeactivate', 'deactivate' ) );

}