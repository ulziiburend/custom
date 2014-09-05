<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";

$db = DataBase::getInstance();
$query = null;
if (isset($_POST['typeId'])) {
    $id = $_POST['typeId'];
    if ($id == 3)
        $query = "select a.common_detail_cd, a.common_detail_cd_nm from tcmmcddetail a
where a.common_class_cd='CRN2070' order by a.common_detail_cd asc ";
} else
    $query = "select a.common_detail_cd, a.common_detail_cd_nm from tcmmcddetail a
where a.common_class_cd='CRN2063'";


$dbData = oci_parse($db, $query);
oci_execute($dbData);

$data = array();

while ($row = oci_fetch_assoc($dbData)) {
    $data[] = $row;
}
if (count($data) < 1) {
    $result = Error_message::Error_number(1001);
} else {
    $result = Error_message::Error_number(1000);

    $result["data"] = $data;
}


oci_close($db);

echo json_encode($result);


