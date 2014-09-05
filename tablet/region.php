<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";

$db = DataBase::getInstance();
$plusQuery="";
if(isset($_POST['value']))
    $val=$_POST['value'];
    $plusQuery="AND (a.common_detail_cd like upper('$val') OR a.common_detail_cd_nm like '%$val%' )";

        $query = "select a.common_detail_cd, a.common_detail_cd_nm from tcmmcddetail a
where a.common_class_cd='CMM1001' $plusQuery order by  a.common_detail_cd asc ";



$dbData = oci_parse($db, $query);
oci_execute($dbData);

$data =oci_fetch_assoc($dbData);

if (!$data ) {
    $result = Error_message::Error_number(1001);
} else {
    $result = Error_message::Error_number(1000);

    $result["data"] = $data;
}

oci_close($db);

echo json_encode($result);


