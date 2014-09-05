<?php

header("Content-type: application/json;charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/custom/util/config.php";

if (isset($_POST["user"]) && isset($_POST['sdate']) && isset($_POST['edate']) && isset($_POST['lang'])) {
    $plusQuery="";
    $user = $_POST["user"];
    $sdate =$_POST['sdate'];
    $edate=$_POST['edate'];
    
     if(isset($_POST['report'])){
         $report=$_POST['report'];
         $plusQuery=$plusQuery." AND A.SIMP_IMP_DCLR_NO LIKE '%$report%'";
     }
       if(isset($_POST['manifest'])){
         $manifest=$_POST['manifest'];
         $plusQuery= $plusQuery." AND A.CARGO_MGMT_NO  LIKE '%$manifest%'";
     }

    $lang=$_POST['lang'];
    $db = DataBase::getInstance();

    $query = "
SELECT
       simpImpDclrNo,
       prgsStatusFgCd,
       prgsStatusFgNm,
        CASE prgsStatusFgCd
         WHEN '10'  THEN '1'
         WHEN '20'  THEN '2'
         WHEN '50'  THEN ''
         WHEN '60'  THEN '4'
         WHEN '70'  THEN '5'         
		ELSE ''
        END AS rowcolor,       
       dclrDate,
       compNm,
       packingQty,
       packingQtyUnitCd,
       itemNm,
       totWgt,
       totWgtUnitCd,  
       totTaxAmt,
       commission,
       CASE taxType
       		WHEN '10' THEN 'Маягт №1'
       		WHEN '20' THEN 'Маягт №2'
            WHEN '30' THEN 'Маягт №3'
       	ELSE 'Маягт'
       	END AS taxType,
       	nvl(cargoMgmtNo,' ') as cargoMgmtNo ,
       	hardCd,
        nvl(pin,' ') as pin,
        taxType1
  FROM 
(
SELECT 
    A.SIMP_IMP_DCLR_NO AS simpImpDclrNo ,    
    a.prgs_status_fg_cd AS prgsStatusFgCd,
    SPCMM_GETCOMMONCODENAME_FU('CRN2021',a.prgs_status_fg_cd, '$lang') AS prgsStatusFgNm,	
	TO_CHAR(A.DCLR_DATE, 'YYYY-MM-DD') AS dclrDate,	
	A.COMP_NM AS compNm,
	A.PACKING_QTY AS packingQty,
	A.PACKING_QTY_UNIT_CD AS packingQtyUnitCd,
	(SELECT MAX(B.ITEM_NM)
          FROM TCRNSIMPIMPEXPDCLRITEM B
         WHERE B.ITEM_NO = 1
           AND A.SIMP_IMP_DCLR_NO = B.SIMP_IMP_DCLR_NO) AS itemNm,
	A.TOT_WGT AS totWgt,
	A.TOT_WGT_UNIT_CD AS totWgtUnitCd,
	A.TOT_TAX_AMT as totTaxAmt,
	A.COMMISSION AS commission,
	A.TAX_TYPE AS taxType,
	A.CARGO_MGMT_NO AS cargoMgmtNo,
	A.HARD_CD AS hardCd,
    A.pin AS pin,
    A.TAX_TYPE1 as taxType1
    from 
	( SELECT
           ROW_NUMBER () OVER (order by A.DCLR_DATE desc, A.SIMP_IMP_DCLR_NO desc) AS num,
           rowid AS rid	 
		FROM TCRNSIMPIMPDCLR A
	   WHERE DEL_YN='N'
	    AND PRGS_STATUS_FG_CD in('10','50','60','70')
		AND REG_USER_ID= '$user'
        AND dclr_Date >= to_date('$sdate', 'YYYYMMDD') 
 	    AND dclr_Date < to_date('$edate', 'YYYYMMDD')+1
                        	) tmp,
	TCRNSIMPIMPDCLR A
   WHERE tmp.rid = A.rowid $plusQuery
   	 order by num)";

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
        $result["lang"]=$lang;
        $result["data"]= $data;
    }
    
    

    oci_close($db);
} else {
    $result = Error_message::Error_number(1);
}
echo json_encode($result);


