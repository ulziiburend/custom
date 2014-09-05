<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";

if (isset($_POST["code"]) ) {
    $plusQuery = null;

        $codeNo = $_POST['code'];
        $plusQuery = $plusQuery." AND (a.COMP_CD LIKE '%$codeNo%' OR a.COMP_REG_NO LIKE '%$codeNo%' OR a.COMP_NM LIKE '$codeNo%' OR a.COMP_TEL_NO LIKE '$codeNo%' )";
 
    if (isset($_POST['regId'])) {
        $regId = $_POST['regId'];
        $plusQuery = $plusQuery." AND  a.COMP_REG_NO LIKE '%$regId%'";
    }
       if (isset($_POST['phone'])) {
        $phone = $_POST['phone'];
        $plusQuery = $plusQuery." AND  a.COMP_TEL_NO LIKE '%$phone%'";
    }
    $db = DataBase::getInstance();

    $query = "
select a.COMP_CD,a.COMP_NM,a.COMP_REG_NO,a.COMP_TEL_NO,a.COMP_ADDR from
tcmmcomp a 
where ROWNUM <= 50 AND a.comp_type_fg_cd='1' $plusQuery order by a.COMP_CD desc 
";

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
} else {
    $result = Error_message::Error_number(1);
}
echo json_encode($result);


