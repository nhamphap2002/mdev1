<?PHP

Class Tools {

    private static $_seed01 = "MYSEED";
    private static $_seed02 = "SEED";

    static function clear($val) {
        return htmlentities(trim($val), ENT_QUOTES, 'UTF-8');
    }

//end function

    static function clearAry($ary) {
        foreach ($ary as $key => $val) {
            $ary[$key] = self::clear($val);
        }
        return $ary;
    }

//end function

    static function getRequest($val) {
        if (!empty($_REQUEST[$val])) {
            if (is_array($_REQUEST[$val])) {
                $anAry = $_REQUEST[$val];
                foreach ($anAry as &$ary) {
                    $ary = self::clear($ary);
                }
                return $anAry;
            } else {
                $_REQUEST[$val] = self::clear($_REQUEST[$val]);
                return isset($_REQUEST[$val]) ? $_REQUEST[$val] : "";
            }
        } else {
            return "";
        }
    }

//end function

    function printJSON($ary) {

        echo '{';
        foreach ($ary as $key => $val) {
            echo '"' . $key . '" : "' . $val . '",';
        }
        echo ' "lastData" : "lastData" ';
        echo '}';
    }

//end function

    function printArrayJSON($ary) {

        echo '[';
        foreach ($ary as $key => $val) {
            echo '{';
            foreach ($val as $kval => $vval) {
                echo '"' . $kval . '" : "' . $vval . '",';
            }
            echo ' "" : "" ';
            echo '},';
        }
        //echo ' "lastData" : "lastData" ';
        echo '{}]';
    }

//end function

    function valEncode($kVal) {
        $kVal = base64_encode($kVal);
        $kVal = str_rot13($kVal);
        $kVal = self::$_seed01 . $kVal;
        $kVal = base64_encode($kVal);
        $kVal = str_rot13($kVal);
        $kVal = htmlentities($kVal);
        $kVal = urlencode($kVal);
        //echo $kVal." encode <BR>";
        //self::valDecode($kVal);
        return $kVal;
    }

//end function

    function valDecode($kVal) {
        $kVal = urldecode($kVal);
        $kVal = html_entity_decode($kVal);
        $kVal = str_rot13($kVal);
        $kVal = base64_decode($kVal);
        $kVal = ltrim($kVal, self::$_seed01);
        $kVal = str_rot13($kVal);
        $kVal = base64_decode($kVal);
        //echo $kVal." decode <BR>";
        return $kVal;
    }

    function varEncode($val) {
        $kVal = md5(self::$_seed01 . $val . self::$_seed02);
        $kVal = substr($kVal, 2, 10);
        return $kVal;
    }

    function varDecode($val) {
        
    }

    function replaceBR($content) {
        $order = array("\r\n", "\n", "\r");
        $replace = '<br />';
        $content = str_replace($order, $replace, $content);
        return $content;
    }

//end function

    function arrayToString($theArray) {
        $toString = implode(";", $theArray);
        return $toString;
    }

//end function

    function stringToArray($theString) {
        $toArray = explode(";", $theString);
        return $toArray;
    }

//end function

    function random($length, $string) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        for ($i = 1; $i <= $length; $i++) {
            $thisRanChar = $characters[mt_rand(0, strlen($characters))];
            if ($thisRanChar == "") {
                $thisRanChar = "0";
            }
            $string .= $thisRanChar;
        }
        return strtoupper($string);
    }

//end function

    function getSerialCode($groupName) {
        $groupName1 = $groupName[0];
        $groupName2 = $groupName[1];
        $groupName3 = $groupName[2];

        $rand3 = "";
        $rand3 = self::random(3, $rand3);
        $code01 = $groupName1 . $rand3;

        $rand2 = "";
        $rand2 = self::random(2, $rand2);
        $code02 = $groupName2 . $groupName3 . $rand2;

        //===START=======code03============
        $year = date("Y");
        $yearNum = $year[3];
        if ($yearNum == 1) {
            $yearTrans = "M";
        } elseif ($yearNum == 2) {
            $yearTrans = "N";
        } elseif ($yearNum == 3) {
            $yearTrans = "O";
        } elseif ($yearNum == 4) {
            $yearTrans = "P";
        } elseif ($yearNum == 5) {
            $yearTrans = "Q";
        } else {
            $yearTrans = "Z";
        }

        $month = strtoupper(date("M"));
        if ($month == "JAN") {
            $monthTrans = "1";
        } elseif ($month == "FEB") {
            $monthTrans = "2";
        } elseif ($month == "MAR") {
            $monthTrans = "3";
        } elseif ($month == "APR") {
            $monthTrans = "4";
        } elseif ($month == "MAY") {
            $monthTrans = "5";
        } elseif ($month == "JUN") {
            $monthTrans = "6";
        } elseif ($month == "JUL") {
            $monthTrans = "7";
        } elseif ($month == "AUG") {
            $monthTrans = "8";
        } elseif ($month == "SEP") {
            $monthTrans = "9";
        } elseif ($month == "OCT") {
            $monthTrans = "0";
        } elseif ($month == "NOV") {
            $monthTrans = "A";
        } elseif ($month == "DEC") {
            $monthTrans = "B";
        } else {
            $monthTrans = "Z";
        }

        $rand2 = "";
        $rand2 = self::random(2, $rand2);
        $code03 = $rand2 . $yearTrans . $monthTrans;

        //===END=======code03============

        $rand4 = "";
        $code04 = self::random(4, $rand4);

        $serial_code = $code01 . "-" . $code02 . "-" . $code03 . "-" . $code04;
        $serial_code = strtoupper($serial_code);

        return $serial_code;
    }

//end function


    /*     * ===START===get serial code life tour======================================================================================== * */

    function getSerialCodeLifeTour() {

        $randCode = "";
        $randCode01 = self::random(5, $randCode);

        //===START=======code02============
        $year = date("Y");
        $yearNum = $year[3];
        if ($yearNum == 1) {
            $yearTrans = "M";
        } elseif ($yearNum == 2) {
            $yearTrans = "N";
        } elseif ($yearNum == 3) {
            $yearTrans = "O";
        } elseif ($yearNum == 4) {
            $yearTrans = "P";
        } elseif ($yearNum == 5) {
            $yearTrans = "Q";
        } else {
            $yearTrans = "Z";
        }

        $month = strtoupper(date("M"));
        if ($month == "JAN") {
            $monthTrans = "1";
        } elseif ($month == "FEB") {
            $monthTrans = "2";
        } elseif ($month == "MAR") {
            $monthTrans = "3";
        } elseif ($month == "APR") {
            $monthTrans = "4";
        } elseif ($month == "MAY") {
            $monthTrans = "5";
        } elseif ($month == "JUN") {
            $monthTrans = "6";
        } elseif ($month == "JUL") {
            $monthTrans = "7";
        } elseif ($month == "AUG") {
            $monthTrans = "8";
        } elseif ($month == "SEP") {
            $monthTrans = "9";
        } elseif ($month == "OCT") {
            $monthTrans = "0";
        } elseif ($month == "NOV") {
            $monthTrans = "A";
        } elseif ($month == "DEC") {
            $monthTrans = "B";
        } else {
            $monthTrans = "Z";
        }

        $rand2 = "";
        $rand2 = self::random(3, $rand2);
        $randCode02 = $rand2 . $yearTrans . $monthTrans;
        //$randCode02 = self::random(5, $randCode);
        //===END=======code02============

        $randCode03 = self::random(5, $randCode);

        $randCode = $randCode01 . "-" . $randCode02 . "-" . $randCode03;

        $serial_code = strtoupper($randCode);

        return $serial_code;
    }

//end function
    /*     * ===END===get serial code life tour======================================================================================== * */
}

//end class
?>