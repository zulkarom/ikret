<?php
namespace app\models;
use Yii;
use app\models\Common;

class LetterPdf
{
	public $model;
	public $pdf;
	public $tuan = 'Tuan';
	public $template;
	public $fontSize = 11;
	public $en = false;
	public $margin_left = 37;
	public $store = false;
	public $multiple = false;
	public $modelMultiple;
	public $ref_left = 390;
	public $date = '09 May 2024';
	
	public function generatePdf(){
		
		$this->pdf = new LetterStart(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$this->writeHeaderFooter();
		$this->startPage();
		
		$this->writeRef();
		$this->writeTitle();


		/*
		$this->writeTable();
		
		// $this->pdf->AddPage("P");
		$this->writeEnding();
		// $this->writeSlogan();

		$this->writeSigniture();
		$this->writeSignitureImg(); */
		
		// $this->writeSk();
		
		// $this->pdf->AddPage("P");
		// $this->writeTask();

		$file_name = str_replace(['/', "\\", "'"],'', 'Appointment_Letter');
		
		$this->pdf->Output($file_name . '.pdf', 'I'); 
        
	}
	
	public function writeHeaderFooter(){
		    $this->pdf->margin_top = 38;
		    $this->margin_left = 21;
		    $this->ref_left = 490;
		
		$this->pdf->footer_first_page_only = true;
		//$this->pdf->footer_html ='<img src="images/letterfoot.jpg" />';
	}
	
	public function writeRef(){

		
		$ref = 'UMK.A01.100-12/3/2 JLD 9 ( 518  )';
		
		$html = '<br /><br />
        <div style="line-height:24px;">&nbsp;</div>
		<table cellpadding="1" border="0">
		<tr>
			<td width="'. $this->ref_left .'"></td>
			<td width="300" align="left">'.$ref . '</td>
		</tr>
		<tr>
			<td></td>
			<td align="left">'. $this->date .'</td>
		</tr>
		</table>
		<br /><br />';
   
		
		$this->pdf->SetMargins($this->margin_left, 10, 35);
		
		$this->pdf->SetFont('arial','', $this->fontSize);
		$tbl = <<<EOD
		$html
EOD;
		
		$this->pdf->writeHTML($tbl, true, false, false, false, '');
	}

	
	public function writeTitle(){

		/* $html = $this->model->user->fullname . '
		<br />Faculty of Entrepreneurship and Business
		<br />Universiti Malaysia Kelantan <br/><br/>'; */
		
		$html = $this->model->user->fullname;
		$l = '';
		if($this->model->committee->is_jawatankuasa == 1){
			$l = 'AHLI ';
			if($this->model->is_leader == 1){
				$l = 'KETUA ';
			}
		}
		$html .= '<br />' . $l.$this->model->committee->com_name;
		$html .= '<br /><br />';

		$html .= 'Dear Prof./Assoc. Prof. /Dr./ Sir/ Madam,<br /><br />
		    
		<b>APPOINTMENT AS A COMMITTEE MEMBER FOR THE INTERNATIONAL CONVENTION ON RESOURCEFUL ENTREPRENEURS ACHIEVING TOMORROW’S EXCELLENCE (I-CREATE 2024), FACULTY OF ENTREPRENEURSHIP AND BUSINESS (FEB), UNIVERSITI MALAYSIA KELANTAN (UMK)</b>
		<br /><br />
		    
		The above matter is cordially referred.
		<br /><br />
		 <table ><tr><td>
		<span style="text-align:justify;">2. &nbsp;&nbsp;&nbsp;</span><span style="text-align:justify;">We are honoured to inform you that the Faculty of Entrepreneurship and Business (FEB), Universiti Malaysia Kelantan (UMK) has appointed you as a committee member for the <b>INTERNATIONAL CONVENTION ON RESOURCEFUL ENTREPRENEURS ACHIEVING TOMORROW’S EXCELLENCE (I-CREATE 2024)</b> effective March 17th, 2024 until the completion of tasks related to the program.</span>
</td></tr>
</table>
<br /><br />

		<table><tr><td>
		<span style="text-align:justify;">3. &nbsp;&nbsp;&nbsp;</span><span style="text-align:justify;">As a member of the committee, you are expected to:</span>
</td></tr>
</table>


<br /><br />';
$w = 710;
$w1=90;
$w2=30;
$w3=$w-$w1-$w2;
$html .='<table border="0">
	<tr><td width="'.$w1.'"></td><td width="'.$w2.'">i.</td>
		<td width="'.$w3.'"><span style="text-align:justify;">Assist in the planning, organizing strategies, and execution of the event.</span></td>
	</tr>
	<tr><td width="'.$w1.'"></td><td width="'.$w2.'">ii.</td>
		<td width="'.$w3.'"><span style="text-align:justify;">Attend coordination meetings and other related discussions.</span></td>
	</tr>
	<tr><td width="'.$w1.'"></td><td width="'.$w2.'">iii.</td>
		<td width="'.$w3.'"><span style="text-align:justify;">Contribute to other related activities for the success of the event.</span></td>
	</tr>
</table>

<br /><br />
		 <table ><tr><td>
		<span style="text-align:justify;">4. &nbsp;&nbsp;&nbsp;</span><span style="text-align:justify;">Your insight and contribution would be of great value towards the success of the event. We thank you for your kind cooperation and remain grateful for your continuous support for the success of I-CREATE 2024.</span>
</td></tr>
</table>
<br /><br />
We sincerely thank you for your contribution and great support for this event.
<br /><br />
<b>"ISLAM MEMIMPIN, RAJA MENAUNGI, NEGERI BERKAT"</b><br />
<b>"MALAYSIA MADANI"</b><br />
<b>"BERKHIDMAT UNTUK NEGARA"</b><br />
<br />
Yours sincerely,<br />
<b>(ASSOC. PROF. TS. DR. ZAILANI BIN ABDULLAH)</b><br />
Dean,<br />
Faculty of Entrepreneurship and Business (FEB) <br />
cum Advisor I-CREATE 2024
<br />
<div align="center"><i>This is a computer-generated document and no signature is required.</i></div>
cc: 
<br />
Fail Program I-CREATE 2024
		';
		
		
		$this->pdf->SetMargins($this->margin_left, 10, 25);
		$this->pdf->SetFont( 'arial','', $this->fontSize);
		$tbl = <<<EOD
		$html
EOD;
		

		$this->pdf->writeHTML($tbl, true, false, false, false, '');
	}
	
	public function writeTable(){
		$all = 700;
		$w1 = 50;
		$w2 = 30;
		$w3 = 145;
		$w4 = 20;
		$w5 = $all - $w1 - $w2 - $w3 - $w4;
		$course = $this->model->courseOffered->course;
		
		if($this->en){
		    $course_code = 'Course Code';
		    $course_name = 'Course Name';
		    $course_name_data = $course->course_name_bi;
		    $session = 'Session';
		    $total_lecture = 'Total Lecture';
		    $total_tutorial = 'Total Tutorial';
		}else{
		    $course_code = 'Kod Kursus';
		    $course_name = 'Nama Kursus';
		    $session = 'Sesi';
		    $course_name_data = $course->course_name;
		    $total_lecture = 'Jumlah Kuliah';
		    $total_tutorial = 'Jumlah Tutorial';
		}
		
		
		$html = '
		<table cellpadding="1" border="0">
		<tr>
			<td width="'.$w1.'"></td>
			<td width="'.$w3.'">'. $course_code .'</td>
			<td width="'.$w4.'">:</td>
			<td width="'.$w5.'">'.$course->course_code .'</td>
		</tr>';
		$html .='<tr>
			<td></td>
			<td>'. $course_name .'</td>
			<td>:</td>
			<td width="'.$w5.'">'.$course_name_data.'</td>
		</tr>';
		if($this->en){
		    $sesi = $this->model->staffInvolved->semester->sessionLongEn;
		}else{
		    $sesi = $this->model->staffInvolved->semester->sessionLong;
		}
		
		$html .='<tr>
			<td></td>
			<td>Semester</td>
			<td>:</td>
			<td>'.strtoupper($sesi).'</td>
		</tr>
		<tr>
			<td></td>
			<td>'. $session .'</td>
			<td>:</td>
			<td>'.$this->model->staffInvolved->semester->year.'</td>
		</tr>';

		if($this->model->countLecturesByStaff > 0){
		$html .='<tr>
			<td></td>
			<td>'. $total_lecture .'</td>
			<td>:</td>
			<td>'.$this->model->countLecturesByStaff.'</td>
			</tr>';
		}
		
		if($this->model->countTutorialsByStaff > 0){
			$html .= '<tr>
				<td></td>
				<td>'. $total_tutorial .'</td>
				<td>:</td>
				<td>'.$this->model->countTutorialsByStaff.'</td>
			</tr>';	
		}
		
		$html .= '</table>
		';
		$this->pdf->SetFont('arial','B', $this->fontSize);
		$tbl = <<<EOD
		$html
EOD;
		$this->pdf->SetMargins($this->margin_left, 10, 25);
		$this->pdf->writeHTML($tbl, true, false, false, false, '');
	}
	
	public function writeEnding(){
		
		
		
		$wd = 630;
		
		if($this->template->background_file == 2){
		    $wd = 720;
		}
        
		if($this->en){
		    $per4 = $this->template->per1_en;
		    $html = '<br />
		<table width="'. $wd .'" border="0"><tr><td><span style="text-align:justify;">3. &nbsp;&nbsp;&nbsp;</span><span style="text-align:justify;">However, this appointment is subject to any changes.
		
</span>
<br /><br />
<span style="text-align:justify;">4. &nbsp;&nbsp;&nbsp;'. str_replace('{TUANPUAN}', $this->tuan, $per4). '</span><br /><br />
		Thank you.
		<br />
		</td></tr></table>';
		}else{
		    $per4 = $this->template->per1;
		    $html = '<br />
		<table  width="'. $wd .'"><tr><td><span style="text-align:justify;">3. &nbsp;&nbsp;</span><span style="text-align:justify;">Untuk makluman, pelantikan ini adalah berkuatkuasa daripada semester berkenaan tertakluk kepada perubahan.
		

</span>
<br /><br />
<span style="text-align:justify;">4. &nbsp;&nbsp;&nbsp;</span><span style="text-align:justify;">'.str_replace('{TUANPUAN}', $this->tuan, $per4).'</span><br /><br />
		Sekian.';
		    if($this->template->background_file == 1){
		        $html .= '<br />';
		    }
		$html .= '</td></tr></table>';
		}
		
		
		$this->pdf->SetFont('arial','', $this->fontSize);
		$tbl = <<<EOD
		$html
EOD;
		
		$this->pdf->writeHTML($tbl, true, false, false, false, '');
	}
	

	
	public function writeSignitureImg(){
		$html = '';
		$sign = $this->template->signiture_file;
		//$html .= $sign;
		$file = Yii::getAlias('@upload/'. $sign);
		//$html .= '**' . $file;
		if(!$sign){
			return false;
		}
		
		if($this->model->status == 10){
			
		
			$html .= '
			<img src="images/dekan.png" />
			';

		}
		

		$y = $this->pdf->getY();
		$adjy = $this->template->adj_y;
		
		$posY = $y - 42 + $adjy;
		$this->pdf->setY($posY); 
$tbl = <<<EOD
$html
EOD;
		
		$this->pdf->writeHTML($tbl, true, false, false, false, '');
		
	}
	
	public function writeSigniture(){
		$tema = $this->template->tema;
		$tema = nl2br($tema);
		$benar = $this->template->yg_benar;
		$dekan = $this->template->dekan;
		$sk = 's.k';
		if($this->en){
		    $tda = 'Deputy Dean (Academic & Student Development)';
		    $dekan_text = 'Dean';
		    $sk = 'c.c';
		}else{
		    $tda = 'Timbalan Dekan (Akademik & Pembangunan Pelajar)';
		    $dekan_text = 'Dekan';
		    $sk = 's.k';
		}
		
		
	   $html = '<b>'.$tema.'</b>
		<br /><br />
		'.$benar.',<br /><br />';
	   
	   if($this->template->is_computer != 1){
	       $html .= '<br /><br />';
	   }
		
		
		$html .= '<b>'.strtoupper($dekan).'</b><br />';
	   
	   
		$html .=  $dekan_text . '<br /><br />';
		
		
		
		
		//$html .=  $sk .' - '. $tda .'';
		
		if($this->template->is_computer == 1){
		    $html .= '<div align="center"><i>';
		    if($this->en){
		        $html .= 'This is a computer-generated document and no signature is required.';
		    }else{
		        $html .= 'Surat ini adalah cetakan komputer dan tandatangan tidak diperlukan.';
		    }
		    
		    $html .= '</i></div><br />';
		}
		$this->pdf->SetFont('arial','', $this->fontSize);
		$tbl = <<<EOD
		$html
EOD;
		
		$this->pdf->writeHTML($tbl, true, false, false, false, '');
		
	}
	
	
	public function startPage(){
		// set document information
		$this->pdf->SetCreator(PDF_CREATOR);
		$this->pdf->SetAuthor('FKP');
		$this->pdf->SetTitle('SURAT PERLANTIKAN');
		$this->pdf->SetSubject('SURAT PERLANTIKAN');
		$this->pdf->SetKeywords('');

		// set default header data
		$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		//$this->pdf->writeHTML("<strong>hai</strong>", true, 0, true, true);
		// set header and footer fonts
		$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		//$this->pdf->SetMargins(25, 10, PDF_MARGIN_RIGHT);
		$this->pdf->SetMargins(0, 0, 0);
		//$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->pdf->SetHeaderMargin(0);

		 //$this->pdf->SetHeaderMargin(0, 0, 0);
		$this->pdf->SetFooterMargin(0);

		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE, -30); //margin bottom

		// set image scale factor
		$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$this->pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		$this->pdf->setImageScale(1.53);

		

		// add a page
		$this->pdf->AddPage("P");
	}
	
	
}