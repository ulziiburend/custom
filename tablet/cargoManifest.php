<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";

if (isset($_POST["user"]) && isset($_POST['sdate']) && isset($_POST['edate'])) {
    $plusQuery="";
    $user = $_POST["user"];
    $sdate =$_POST['sdate'];
    $edate=$_POST['edate'];
    
     if(isset($_POST['billNo'])){
         $billNo=$_POST['billNo'];
         $plusQuery=$plusQuery." AND  c.bl_no LIKE '$billNo%'";
     }
       if(isset($_POST['manifest'])){
         $manifest=$_POST['manifest'];
         $plusQuery= $plusQuery." AND (A.CARGO_MGMT_NO  LIKE '%$manifest%' OR c.bl_no LIKE '$manifest%' OR  b.wagon_no like '$manifest%') ";
     }
  if(isset($_POST['wagonNo'])){
         $wagonNo=$_POST['wagonNo'];
         $plusQuery= $plusQuery." AND b.wagon_no  LIKE '$wagonNo%'";
     }


    $db = DataBase::getInstance();

    $query = "SELECT 
       a.cargo_mgmt_no AS cargoMgmtNo,
       a.mf_mgmt_no AS mfMgmtNo,
       a.house_seq AS houseSeq,
       a.shipper_nm AS shipperNm,
       a.consignee_nm AS consigneeNm,
       a.consignee_addr AS consigneeaddr,
       a.consignee_tel_no AS consigneetelno,
       c.qty AS qty,
       c.wgt AS wgt,
       c.qty_unit AS qtyUnit,
       
       c.wgt_unit AS wgtUnit ,
       c.bl_no AS blNo,
       to_char(b.ent_border_date,'YYYYMMDD') AS entBorderDate,
       c.submt_cstm_cd AS submtCstmCd,
       b.load_place_cd AS loadPlaceCd,
       b.uload_place_cd AS uloadPlaceCd,
       b.wagon_no  AS wagonNo,   
       b.wagon_seal_no AS wagonSealNo,
       c.goods_nm as goodsnm
  FROM tcrg a, tcrgmf b, tcrginout c
 WHERE 1 = 1
   AND a.mf_mgmt_no = b.mf_mgmt_no
   AND a.del_yn != 'Y'
   AND b.cargo_imp_exp_shape = 'I'
   AND a.mf_mgmt_no = c.mf_mgmt_no
   AND a.house_seq = c.house_seq
   AND c.carry_in_out_cd = 'I'
   AND c.inv_yn = 'N'
   and a.house_seq != decode(b.mf_type, 'C', '000',decode(b.dest_cstm_cd1,'12201','000',' '))
   AND c.submt_cstm_cd LIKE CONCAT('$user', '%')  -- param
   AND b.doc_func in ('4', '7', '5', '9', '99')
   AND b.ent_border_date >= TO_DATE('$sdate', 'YYYY-MM-DD') 
   AND b.ent_border_date < TO_DATE('$edate', 'YYYY-MM-DD') + 1   
  $plusQuery
ORDER BY a.mf_mgmt_no DESC, a.house_seq asc";

    $check = oci_parse($db, $query);
    oci_execute($check);
  
   $data=array();

        while( $row=oci_fetch_assoc($check)){
            $data[]=$row;
         
    }
       if (count($data)<1) {
       $result = Error_message::Error_number(1001);}
    else {
        $result = Error_message::Error_number(1000);
        $result["startData"]=$sdate;
        $result["endData"]=$edate;
        $result["lenght"]=  count($data);
        $result["data"]= $data;
    }

    oci_close($db);
} else {
    $result = Error_message::Error_number(1);
}
echo json_encode($result);