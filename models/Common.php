<?php

namespace app\models;

use Yii;

class Common {

    public static function months()
    {
        return [
			1 => 'Januari',
			2 => 'Februari',
			3 => 'Mac',
			4 => 'April',
			5 => 'Mei',
			6 => 'Jun',
			7 => 'Julai',
			8 => 'Ogos',
			9 => 'September',
			10 => 'Oktober',
			11 => 'November',
			12 => 'Disember',
		];
    }
	
	public static function months_short()
    {
        return [
			1 => 'Jan',
			2 => 'Feb',
			3 => 'Mac',
			4 => 'Apr',
			5 => 'Mei',
			6 => 'Jun',
			7 => 'Jul',
			8 => 'Ogos',
			9 => 'Sep',
			10 => 'Okt',
			11 => 'Nov',
			12 => 'Dis',
		];
    }

	public static function get_bulan_short($b){
		$bln_short = null;
		$bln = self::months_short();
        if(array_key_exists($b,$bln)){
            $bln_short = $bln[$b];
        }
		return $bln_short;
	}
	
	public static function getHari($number){
		$arr = self::hari_list();
		return $arr[$number];
	}
	
	public static function getTarikhHari($date){
		$tarikh = self::dateFormat($date);
		$num_hari = date('N', strtotime($date));
		$str_hari = self::getHari($num_hari);
		return $tarikh . ' ('.$str_hari.')';
	}
	
	public static function dateFormat($date){
		$day = date('j', strtotime($date));
		$month_num = date('n', strtotime($date));
		$month_bm = self::months();
		$month_str = $month_bm[$month_num];
		$year = date('Y', strtotime($date));
		return $day . ' ' . $month_str . ' ' . $year;
	}
	
	public static function hari_list()
    {
        return [
			7 => 'Ahad',
			1 => 'Isnin',
			2 => 'Selasa',
			3 => 'Rabu',
			4 => 'Khamis',
			5 => 'Jumaat',
			6 => 'Sabtu',
		];
    }
	
	public static function getMonth($str){
		$list = self::months_short();
		foreach($list as $key=>$val){
			$m = strtolower($val);
			$str = strtolower($str);
			if($m == $str){
				return $key;
			}
		}
		return 0;
	}
	
	public static function date_malay($str){
		$day = date('d', strtotime($str));
		$month = date('m', strtotime($str)) + 0;
		$month_malay = self::months()[$month];
		$year = date('Y', strtotime($str));
		return $day . ' ' . $month_malay . ' ' . $year;
	}
	
	public static function date_malay_short($str){
		$day = date('d', strtotime($str));
		$month = date('m', strtotime($str)) + 0;
		$month_malay = self::months_short()[$month];
		$year = date('Y', strtotime($str));
		return $day . ' ' . $month_malay . ' ' . $year;
	}
	
	
	public static function days(){
		return [1 => "Ahad", 2 => "Isnin", 3 => "Selasa", 4 => "Rabu", 5 =>"Khamis", 6 => "Jumaat", 7 => "Sabtu"];
	}
	
	public static function years()
    {
		$curr = date('Y') + 0;
		$last = $curr - 1;
        return [
			$curr => $curr,
			$last => $last,
		];
    }
	
	public static function gender(){
		return [1 => 'Lelaki', 0 => 'Perempuan'];
	}
	
	public static function marital(){
		return [1 => 'Berkahwin', 2 => 'Tidak Berkahwin'];
	}
	
	public static function citizen(){
		return ['Malaysia' => 'Malaysia', 'Bukan Malaysia' => 'Bukan Malaysia'];
	}
	
	public static function yesNo(){
		return [1 => 'Ya', 0 => 'Tidak'];
	}

	public static function role(){
		return [1 => 'KADET', 2 => 'PEGAWAI', 3 => 'JURU/GURULATIH', 4 => 'ANAK GAYONG', 5 => 'IBUBAPA/PENJAGA', 6 => 'PENASIHAT', 10 => 'GUEST'];
	}

	public static function roleCard(){
		return [1 => 'KADET', 2 => 'PEGAWAI', 3 => 'JURU/GURULATIH', 4 => 'ANAK GAYONG'];
	}

	public static function role2(){
	    return [4 => 'ANAK GAYONG', 3 => 'JURU/GURULATIH', 6 => 'PENASIHAT'];
	    //5 => 'GURULATIH'
	}

	public static function roleLoginEgayong(){
	    return [4 => 'ANAK GAYONG', 3 => 'JURU/GURULATIH', 5 => 'IBUBAPA/PENJAGA', 6 => 'PENASIHAT'];
	}

	public static function role3(){
	    return [4 => 'ANAK GAYONG', 5 => 'IBUBAPA/PENJAGA'];
	    //5 => 'GURULATIH'
	}
	
	public static function status(){
		return [10 => 'Submit', 20 => 'Approved', 30 => 'Disapproved'];
	}

	public static function regStatus(){
		return [0 => 'Submit', 20 => 'Approved', 30 => 'Disapproved'];
	}

	public static function statusColor(){
	    return [10 => 'warning', 20 => 'primary', 30 => 'danger'];
	}
	
	public static function statusBm(){
	    return [10 => 'Mohon', 20 => 'Aktif', 30 => 'Tidak Lulus'];
	}

	public static function statusBmCapital(){
	    return [10 => 'MOHON', 20 => 'AKTIF', 30 => 'TIDAK LULUS'];
	}

	public static function reportStatus(){
		return [0 => 'Not Submit', 10 => 'Draft', 20 => 'Submit', 30 => 'Approved'];
	}

	public static function statusKursus(){
	    return [10 => 'Submit', 20 => 'Paid', 30 => 'Complete', 40 => 'Reject'];
	}

	public static function statusPermohonan(){
		return "Permohonan anda dalam proses kelulusan untuk dimasukan dalam DIREKTORI KOMUNITI 1 JUTA ANAK GAYONG. Sila pastikan maklumat peribadi anda lengkap untuk memastikan proses kelulusan berjaya bagi tujuan paparan dalam DIREKTORI eGayong.";
	}

	public static function jenisGayong(){
	    return [1 => 'PSSGM', 2 => 'PSSPGM', 3 => 'PSSGWSM', 4 => 'PSGSM', 5 => 'PSSGMK', 6 => 'PSSGN', 7 => 'PSSCSM'];
	}

	public static function jenisPekerjaan(){
	    return [1 => 'Kerajaan', 2 => 'Swasta', 3 => 'Berniaga', 6 => 'Bekerja Sendiri', 8 => 'Pelajar', 7 => 'Pesara', 4 => 'Tidak Berkerja', 5 => 'Lain-lain'];
	}

	public static function paymentMedium(){
	    return [
	        1 =>'Online Banking',
	        2 =>'Cheque',
	        3 => 'Payment Gateway',
	        4 => 'eWallet',
	    ];
	}
	
	public static function dateStartEndFormat($date1, $date2, $long = false){
		
	    $day1 = date('j', strtotime($date1));
	    if($long){
	        $month_str1 = date('F', strtotime($date1));
	    }else{
	        $month_str1 = date('M', strtotime($date1));
	    }
	    $year1 = date('Y', strtotime($date1));
	    
	    $day2 = date('j', strtotime($date2));
	    if($long){
	        $month_str2 = date('F', strtotime($date2));
	    }else{
	        $month_str2 = date('M', strtotime($date2));
	    }
	    $year2 = date('Y', strtotime($date2));
	    
		if($date1 == $date2){
			return $day1 . ' '  . $month_str1 . ' ' . $year1;
		}else if($month_str1 == $month_str2){
	        if($year1 == $year2){
	            return $day1 . ' - '.$day2.' ' . $month_str1 . ' ' . $year1;
	        }else{
	            return $day1 . ' ' . $month_str1 . ' ' . $year1 . ' - '. $day2 . ' ' . $month_str2 . ' ' . $year2 ;
	        }
	        
	    }else{
	        if($year1 == $year2){
	            return $day1 . ' ' . $month_str1 . ' - '.$day2.' ' . $month_str2 . ' ' . $year1;
	        }else{
	            return $day1 . ' ' . $month_str1 . ' ' . $year1 . ' - '. $day2 . ' ' . $month_str2 . ' ' . $year2 ;
	        }
	    }
	}
	
	public static function array2Str($array){
	    $str = '';
	    if($array){
	        $i = 1;
	        foreach($array as $a){
	            $comma = $i == 1 ? '':', ';
	            $str .=$comma.$a;
	        $i++;
	        }
	    }
	    
	    return $str;
	}
	
}
