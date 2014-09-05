<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";

if (isset($_POST["code"]) ) {
    $plusQuery = null;
    $codeNo = $_POST['code'];
  
  
    $db = DataBase::getInstance();

    $query = "
INSERT INTO TCRNSIMPIMPDCLR(
       simp_imp_dclr_no,
       prgs_status_fg_cd,
       dclr_date,       
       cargo_mgmt_no,
       comp_cd,
       comp_nm,
       pin,
       comp_tel_no,
       comp_addr,
       packing_qty,
       packing_qty_unit_cd,
       tot_wgt,
       tot_wgt_unit_cd,       
       tot_tax_amt,
       REG_USER_ID,
       reg_date,
       del_yn,
       cstm_cd,
       DOC_CHARGER_ID,
       DCLR_TYPE_CD,
	   IMP_EXP_FG_CD,
	   TRADE_TYPE_CD,
	   IMP_EXP_COUNTRY_CD,
	   DOC_CHARGER_BADGE_NO,
	   DELIVER_COND,
	   BORDR_CROS_MGMT_NO,
	   COMMISSION,
	   TAX_TYPE,
	   HARD_CD,
       PAY_NO,
       EXPT_RSL_NM,
       TAX_TYPE1
     ) VALUES (
       ?,
       10,
       to_date(?,'YYYY-MM-DD'),
	   ?,
       ?,
       ?,
       ?,
       ?,
       ?,       
       ?,
       ?,       
       ?,
       ?,       
       ?,
	   ?,
	    sysdate,  
	   'N',
	   ?,
	   ?,
	   ?,
	   ?,
	   ?,
	   ?,
	   SPCMM_GETBADGENO_FU(?),
	   ?,
	   replace(?, '-', ''),
	   ?,
	   ?,
	   ?,
       ?,
       ?,
       ?
     )";
 
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


