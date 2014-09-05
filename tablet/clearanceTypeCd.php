<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";

if (isset($_POST["typeCode"])) {

    $typeCode = $_POST["typeCode"];


    $db = DataBase::getInstance();

    $query = "
select a.DCLR_TYPE_CD,a.DCLR_TYPE_NM,a.INOUT_TYPE
from tcrndclrtype a where del_yn='N' and a.DCLR_TYPE_CD like '$typeCode%' order by a.DCLR_TYPE_CD asc";

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
} else {
    $result = Error_message::Error_number(1);
}
echo json_encode($result);


