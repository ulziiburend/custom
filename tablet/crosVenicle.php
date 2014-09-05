<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";

if (isset($_POST["user"])) {
    $plusQuery="";
    $user = $_POST["user"];

     if(isset($_POST['venicleNo'])){
         $Ven=$_POST['venicleNo'];
         $plusQuery=" AND  vehcl_no  LIKE '$Ven%'";
     }
       

    $db = DataBase::getInstance();

    $query = "SELECT 
       bordr_cros_mgmt_no AS bordrCrosMgmtNo,
       vehcl_no AS vehclNo,
       nationlty_cd AS nationltyCd,      
       bordr_cros_cstm AS bordrCrosCstm,
       TO_CHAR(bordr_cros_date,'YYYYMMDDHH24MISS') AS bordrCrosDate,
       trnsp_permit_expire_ymd AS trnspPermitExpireYmd,
       vin    AS vin,
       vehcl_model_no AS vehclModelNo,
       engine_no AS engineNo,
       vehcl_owner_nm AS vehclOwnerNm,
       vehcl_fg_cd AS vehclFgCd,
       SPCMM_GETCOMMONCODENAME_FU('CRG4113',vehcl_fg_cd, 'mn') AS vehclFgNm,
       SPCMM_GETCOMMONCODENAME_FU('CMM1001',nationlty_cd, 'mn') AS nationltyNm,
       vehcl_owner_addr AS vehclOwnerAddr,
       TRNSP_METH_FG_CD AS trnspMethFgCd,
       SPCMM_GETCOMMONCODENAME_FU('CRG4104',trnsp_meth_fg_cd, 'mn') AS trnspMethFgNm,
       EMPTY_VEHICLE_WGT AS emptyVehicleWgt,
       MAX_LOAD_WGT AS  maxLoadWgt,
       dest_cd AS destCd,
       model_nm,
       (SELECT NVL((CASE LOWER( 'mn')
						WHEN 'ko' THEN country_loc_cd_kor_nm
						WHEN 'en' THEN country_loc_cd_eng_nm
						WHEN 'ru' THEN country_loc_cd_rus_nm
						WHEN 'mn' THEN country_loc_cd_nm
						ELSE country_loc_cd_nm
						END), NVL(country_loc_cd_eng_nm, '')) from tcmmcountryloccd WHERE country_loc_cd = dest_cd )AS destNm,
       departure_cd AS departureCd,
       (SELECT NVL((CASE LOWER( 'mn')
						WHEN 'ko' THEN country_loc_cd_kor_nm
						WHEN 'en' THEN country_loc_cd_eng_nm
						WHEN 'ru' THEN country_loc_cd_rus_nm
						WHEN 'mn' THEN country_loc_cd_nm
						ELSE country_loc_cd_nm
						END), NVL(country_loc_cd_eng_nm, '')) from tcmmcountryloccd WHERE country_loc_cd = departure_cd )AS departureNm                  
  	FROM tcrgvehclbordrcros 
  	WHERE 1 = 1
  	AND del_yn = 'N' 
    AND bordr_cros_fg_cd = 'I'
	AND vehcl_fg_cd in ('C', 'B', 'T','Q','A')
	AND prgs_status_cd in ('A', 'W','P','AX')
    AND substr(bordr_cros_cstm,1,2)= substr('$user',1,2)
        $plusQuery
   ORDER BY bordr_cros_date DESC, bordr_cros_mgmt_no DESC";

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

  
        $result["data"]= $data;
    }
    

    oci_close($db);
} else {
    $result = Error_message::Error_number(1);
}
echo json_encode($result);