<?php
namespace app\models;

use Yii;
use yii\helpers\Html;

class SessionQr
{

    public $model;
    public $pdf;
    public $yp;

    public function generatePdf()
    {
        $this->pdf = new SessionQrStart(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $this->startPage();
        $this->writeIntro();
        $this->writeQr();
        $this->writeFooter();
        $this->pdf->Output('I-CREATE ATTENDANCE.pdf', 'I');
    }

    public function writeIntro(){
        $dateTime = $this->formatDateTimeRange($this->model->datetime_start, $this->model->datetime_end);

        $program = '';
        if($this->model->program){
            $program = '<br />(' . Html::encode($this->model->programNameShort) . ')';
        }
        

        $html = '<br /><br /><div align="center"><img src="images/logo-sm.png" width="400" />
        <h2>' . Html::encode($this->model->session_name) . $program . '</h2>
        <div><i>Date Time</i><br />
        ' . Html::encode($dateTime) . '

        </div><br />

        <h3>ATTENDANCE QR CODE</h3>
        <i>Login to <a href="https://fkp-portal.umk.edu.my/icreate">https://fkp-portal.umk.edu.my/icreate</a> to scan</i>
        
        </div><br /><br />';

$tbl = <<<EOD
$html
EOD;
        
        $this->pdf->writeHTML($tbl, true, false, false, false, '');
        $this->yp = $this->pdf->getY();
    }

    protected function formatDateTimeRange($startValue, $endValue)
    {
        $timezone = new \DateTimeZone('Asia/Kuala_Lumpur');
        $start = $this->createLocalDateTime($startValue, $timezone);
        $end = $this->createLocalDateTime($endValue, $timezone);

        if(!$start && !$end){
            return '';
        }

        if(!$start){
            return $end->format('d M Y, h:i A');
        }

        if(!$end){
            return $start->format('d M Y, h:i A');
        }

        if($start->format('Y-m-d') === $end->format('Y-m-d')){
            return $start->format('d M Y, h:i A') . ' - ' . $end->format('h:i A');
        }

        return $start->format('d M Y, h:i A') . ' - ' . $end->format('d M Y, h:i A');
    }

    protected function createLocalDateTime($value, \DateTimeZone $timezone)
    {
        $value = trim((string)$value);
        if($value === ''){
            return null;
        }

        try {
            return (new \DateTimeImmutable($value, $timezone))->setTimezone($timezone);
        } catch(\Exception $e) {
            return null;
        }
    }

    public function writeQr()
    { 
        // set style for barcode
$style = array(
    'border' => 2,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);
$token = $this->model->token;
$url = 'https://fkp-portal.umk.edu.my/icreate/site/qr?t='.$token;
$yp = $this->yp;
        $this->pdf->write2DBarcode($url, 'QRCODE,M', 65, $yp, 90, 90, $style, 'N');

        $this->yp = $this->pdf->getY();

    }

    public function writeFooter(){


        $html = '<div align="center">
        <br /><br /><br /><br />
        <i style="font-size:12px">Printed at '.date("l jS \of F Y h:i:s A").'</i>
        </div>';

$tbl = <<<EOD
$html
EOD;
        
        $this->pdf->writeHTML($tbl, true, false, false, false, '');
        $this->yp = $this->pdf->getY();
    }

    public function startPage()
    {
        $filename = 'I-CREATE_ATTENDANCE';
        $this->pdf->SetCreator('I-CREATE_ATTENDANCE');
        $this->pdf->SetAuthor('I-CREATE_ATTENDANCE');
        $this->pdf->SetTitle($filename);
        $this->pdf->SetSubject($filename);
        $this->pdf->SetKeywords('');

        // set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $this->pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $this->pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->pdf->SetMargins(25, 10, PDF_MARGIN_RIGHT);


        // $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetHeaderMargin(0);

        // $this->pdf->SetHeaderMargin(0, 0, 0);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        // $this->pdf->SetAutoPageBreak(false, 0); // margin bottom
        //$this->pdf->SetAutoPageBreak(TRUE, - 30); // margin bottom
        $this->pdf->SetAutoPageBreak(false);

        // set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once (dirname(__FILE__) . '/lang/eng.php');
            $this->pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        $this->pdf->setImageScale(1.53);


        // add a page
        $this->pdf->AddPage("P");
    }
}
