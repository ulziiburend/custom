<?php

class Error_message{
    public static function Error_number($number){
        $number = (int)$number;
        switch($number){
            case 1000:
                $result["error_description"] = "Амжилттай боллоо";
                $result["error_number"] = 1000;
              
                break;
            case 1001:
                $result["error_description"] = "Одоогоор өгөгдөл байхгүй байна";
                $result["error_number"] = 1001;
            
                break;
            case 1003:
                $result["error_description"] = "Амжилтгүй боллоо";
                $result["error_number"] = 2000;
          
                break;
        
            default :
                $result["error_description"] = "Unknown Error";
                $result["error_number"] = 0;
           
                break;
        }
        return $result;
    }
}