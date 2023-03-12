<?php

require_once( '../../../wp-load.php' );

$codeValue = htmlspecialchars($_GET["tracking"]);
$postIDValue = htmlspecialchars($_GET["id"]);


$email = "";
$name = "";
$data = get_post_meta($postIDValue);
foreach ($data as $key => $value) {
    if ($key == $codeValue) {
        $userInfo = get_post_meta( $postIDValue, $key, false);
        $email = $userInfo[0]["Email"];
        $name = $userInfo[0]["Name"];
        update_post_meta($postIDValue, $codeValue, array("Email" => $email, "Name" => $name, "Status" => "Read"));
    }
}
?>