<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";


$db = DataBase::getInstance();

$query = "
select a.common_detail_cd, a.common_detail_cd_nm from tcmmcddetail a
where a.common_class_cd='CMM1012' order by a.COMMON_DETAIL_CD asc";

$check = oci_parse($db, $query);
oci_execute($check);

$data = array();

while ($row = oci_fetch_assoc($check)) {
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


