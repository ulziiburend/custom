<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";

if (isset($_POST["user"]) && isset($_POST['pass'])) {

    $user = $_POST["user"];
    $pass = $_POST['pass'];
    $db = DataBase::getInstance();

    $query = "SELECT
       user_id AS userid,
       user_nm AS userNm,
       user_snm AS regUserSnm,
       user_pin AS userPin,
       user_posi_cd AS userPosiCd,
       default_lang_fg_cd AS defaultLangFgCd,
       cstm_cd AS cstmCd,
       user_cell_phone_no AS userCellPhoneNo,
       user_tel_no AS userTelNo,
       user_addr AS userAddr,
       user_email AS userEmail,
       user_ip AS userIp,
       default_lang_fg_cd AS hl,
       page_size AS userPageCount
       FROM TCMMUSER
       WHERE use_yn = 'Y'
       AND user_id = '$user' 
       AND user_passwd = cais.cryptit.gethash('$pass')";

    $check = oci_parse($db, $query);
    oci_execute($check);
   $data=oci_fetch_assoc($check);
    if (!$data) {
        $result = Error_message::Error_number(1001);
    } else {
        $result = Error_message::Error_number(1000);
        $result["data"] = $data;
        $userId=$data['USERID'];
        $cstmcd=$data['CSTMCD'];
        $ip=$_POST['ip'];
        $logQuery="INSERT INTO CAIS.TCMMUSERLOGINHISTORY (
       logged_date,
       user_id,
       cstm_cd,
       user_ip
     ) VALUES (
       sysdate,
       '$userId',
       '$cstmcd',
       '$ip'
     )";
         $insertLog = oci_parse($db, $logQuery);
        oci_execute($insertLog);
        oci_commit($db);
    }

    oci_close($db);
} else {
    $result = Error_message::Error_number(1);
}
echo json_encode($result);


