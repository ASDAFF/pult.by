<?
header("Content-Type: application/x-javascript");
$config = array("appmap" => array("main"=>"/site_zf/eshop_app/", "left"=>"/site_zf/eshop_app/left.php"));
echo json_encode($config);
?>