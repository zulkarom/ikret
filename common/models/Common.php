<?php

namespace common\models;

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
	
	private static function dateFormat($date){
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
		return [1 => 'Beneficiary', 2 => 'Supplier'];
	}
	
	public static function expertType(){
		return [10 => 'Industry Expert', 20 => 'Academician', 30 => 'Mentor'];
	}

	public static function reportStatus(){
		return [0 => 'Not Submit', 10 => 'Draft', 20 => 'Submit', 30 => 'Approved'];
	}

	public static function statusModule(){
	    return [10 => 'Submit', 20 => 'Paid', 30 => 'Complete', 40 => 'Reject'];
	}

	public static function anjuran(){
        return [1 => 'UMK', 2 => 'Bukan UMK'];
    }
}
