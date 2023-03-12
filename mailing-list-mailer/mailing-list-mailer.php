<?php
/**
 * Plugin Name: mailing-list-mailer
 * Description: Mail from subscribers in database
 * Author: Amar Patel
 * Author URI: ""
 * Version: 1.0.0
 * Text Domain: mailing-list-mailer
 */

 if (!defined('ABSPATH')){
    exit;
 }

 define("MailingFunctions_dir", plugin_dir_path(__FILE__) . "MailingForm.php"); 
 define("MetaBoxes_dir", plugin_dir_path(__FILE__) . "metaBoxes.php");


 require_once(MailingFunctions_dir);
 require_once(MetaBoxes_dir);
 

 class MailingList {

    #Initializes the code
    public function __construct() {
        #Creates email menu bar
        add_action('init', array($this, "create_custom_post_type"));
        #Creates send email menu bar
        add_action( "init", array($this,'mysite_admin_menu'));
        #Loads ajax script to get information from the form
        add_action( "admin_footer", array($this, "load_scripts"));
        #Registers rest api that is called when form is submitted
        add_action("rest_api_init", array($this, "register_rest_api"));
        #Creates metaboxes to save email information in emails menu bar
        add_action( "add_meta_boxes", array($this, "create_meta_box"));
    }


    public function mysite_admin_menu() {

        add_menu_page(
            "Send Emails", 
            "Send Emails", 
            "manage_options", 
            "emails", 
            array($this, "create_form"), 
            "dashicons-email" , 
            6);
    }

    public function create_custom_post_type() {
        $args = array(
            'public' => true,
            'has_archive' => true,
            "supports" => array("title"),
            "exclude_from_search" => true,
            "publicly_queryable" => false,
            "capability" => "manage_options",
            "capability_type" => "post",
            "labels" => array(
                "name" => "Emails",
                "singular_name" => "Email"
            ),
            "capabilities" => array(
                "create_posts" => false,
                "publish_post" => false
            ),
            "map_meta_cap" => true, 
            "menu_icon" => "dashicons-email",
            "menu_position" => 5,
            "supports" => false,
        );

        register_post_type("mailing_list", $args);

    }

    public function create_form() {
        createForm();
    }

    public function load_scripts() 
    {?>
        <script>              
            var nonce = "<?php echo wp_create_nonce( "wp_rest" );?>";

            (function($){
                $("#Information").submit(function(event) {
                    var mysave = $('#textBox').html();
                    $('#hiddeninput').val(mysave);
                    event.preventDefault();
                    var form = $(this);
                    form.fadeOut();
                    $.ajax({
                        method : "post",
                        url : "<?php echo get_rest_url( null, "mailing-list-mailer/v1/send-email"  );?>",
                        headers : {"X-WP-NONCE" : nonce},
                        data : form.serialize(),
                        success : function() {
                            form.hide();
                            $("#formSuccess").html("Email Sent").fadeIn();
                        },
                        error : function(jqXHR, XMLHttpRequest, textStatus, errorThrown) {
                            $("#formError").html("Error sending Email").fadeIn();
                           form.fadeIn();
                        }
                    })
            });

            })(jQuery)
        </script>
    <?php }

    public function register_rest_api() {
        register_rest_route( "mailing-list-mailer/v1", "send-email", array(
            "methods" => "POST",
            "callback" => array ($this, "handle_email")
        ) );
    }

    public function handle_email($data) { 
        $params = $data -> get_params();
        $headers = $data -> get_headers();
        $nonce = $headers["x_wp_nonce"][0];

        if (!wp_verify_nonce( $nonce, "wp_rest" )){
            return new WP_REST_Response("Message not Sent", 422);
        }

        add_filter("wp_mail_content_type", function($content_type) {
            return "text/html";
        });

        
        unset($params["_wpnonce"]);
        unset($params["_wp_http_referer"]);

        $post = [
            "post_title" => $params["Subject"],
            "post_type" => "mailing_list",
        ];
        
        $post_id =  wp_insert_post($post);

        add_post_meta( $post_id, "Contents", $params["Contents"] );
        add_post_meta( $post_id, "Subject", $params["Subject"] );


        $emailList = array();

        #Removes any repeat emails that may be in the list
        foreach ($params["Email_Group"] as $value) {
            $encodedJSON = json_decode($value, true);
            foreach ($encodedJSON["Info"] as $JSONtwo) {
                $email = $JSONtwo;
                $formattedOutput = array("Email" => $email["Email"], "Name" => $email["Name"]);
                if (in_array($formattedOutput, $emailList) == false) {
                    array_push($emailList, $formattedOutput);
                }
            }
        }
        #Puts email into html and sends it out for each email in the list
        foreach ($emailList as $email) {
                $trackingCode = trim(md5(rand()));
        
                $email_message = "<html> 
                                    <head> <meta http-equiv='Content-Type' content='text/html'; charset=utf-8 /> </head>
                                    <body>  
                                        <img src='" .  WP_CONTENT_URL . "/uploads/2023/02/emailBanner.png' . alt='Email'>"
                                        . $params["Contents"] .
                                        "<img src='" . WP_PLUGIN_URL. "/mailing-list-mailer/tracker.php?tracking=" . urlencode($trackingCode) . "&id=" . urlencode($post_id) . "' width='1px' height='1px' alt='' >" .
                                    "</body>
                                </html>";

                $emailAddress = str_replace("'", "", $email["Email"]);
                wp_mail( $emailAddress, $params["Subject"], $email_message);

               add_post_meta( $post_id, $trackingCode, array("Email" => $emailAddress, "Name" => $email["Name"], "Status" => "Unread"));
        }

        if ($post_id) {
            return new WP_REST_Response("Sent", 200);
        }
        
    }

    public function create_meta_box() {
        createMetaBox();
    }
 }

 new MailingList;