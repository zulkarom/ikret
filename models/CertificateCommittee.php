<?php
namespace app\models;

use Yii;

class CertificateCommittee
{

    public $model;
    public $template;
    public $pdf;
    public $system;
    public $filename;
    public $width;
    public $height;
    public $align = 'center';
    protected $nameLimitLine = null;

    public $frontend = false;

    public function generatePdf()
    {

        if ($this->template->is_portrait == 1) {
            $o = "P";
        } else {
            $o = "L";
        }
        $this->pdf = new StartPdf($o, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        
        $this->pdf->image_background = $this->template->backgroundFile();

        

        if($this->template->align == 1){
            $this->align = 'left';
        }else if($this->template->align == 2){
            $this->align = 'right';
        }else{
            $this->align = 'center';
        }

        $this->startPage();
        $this->writeData();
        $this->pdf->Output($this->filename . '.pdf', 'I');
    }

    public function writeData()
    { 
        $left = $this->align === 'center' ? 0 : $this->template->textLeft(75);
        $this->pdf->SetFont('iniriaserif', '', 10);
        //$this->pdf->SetTextColor(35, 22, 68);
        $preset = $this->template->set_type;
        if ($preset == 1) {
            if($left > 0){
                $this->pdf->SetXY($left,0);
            }
            $this->html_name();
            if($left > 0){
                $this->pdf->SetX($left);
            }
            $this->html_position();
            $this->drawStoredNameLimitLine();
            if($left > 0){
                $this->pdf->SetXY($left,0);
            }
            $this->pdf->SetFont('iniriaserif', '', 10);
            if($left > 0){
                $this->pdf->SetXY($left,0);
            }
        } else {
            $html = $this->template->custom_html;
        }
    }


    public function html_name()
    {


        $margin_name = $this->template->textTop('name_mt', 0);

        $showNameBorder = $this->template->showNameBorder();
        $tableBorder = '0';
        $tableStyle = '';
        $cellStyle = 'color:#000000;';

        $html = '<table border="' . $tableBorder . '" width="100%" style="' . $tableStyle . '">
<tr>

    <td align="'.$this->align.'">';

        $html .= '<table border="' . $tableBorder . '" width="100%" align="'.$this->align.'">';

        if ($margin_name > 0) {
            $size = $this->template->textSize('name_size', 27);
            $html .= '
<tr><td height="' . $margin_name . '" style="' . $cellStyle . '"></td></tr>
<tr><td align="'.$this->align.'" style="' . $cellStyle . 'font-size:' . $size . 'px">' . strtoupper($this->model->user->fullname) . '</td></tr>';
        }



        $html .= '</table>';

        $html .= '</td>
</tr>';
        $html .= '</table>';

$tbl = <<<EOD
$html
EOD;

        $this->pdf->writeHTML($tbl, true, false, false, false, '');
        $left = $this->template->textLeft(70);
        $right = $this->template->textRight(11);
        if($right <= 0){
            $right = $left;
        }
        $width = $this->pdf->getPageWidth() - $left - $right;
        $this->nameLimitLine = [$left, $width, $this->pdfTop($margin_name), $this->pdfTop($this->template->nameLimitY('field1_mt', 101))];
    }

    protected function drawStoredNameLimitLine()
    {
        if(!$this->template->showNameBorder() || $this->nameLimitLine === null){
            return;
        }

        [$left, $width, $top, $limitBottom] = $this->nameLimitLine;
        $this->pdf->SetDrawColor(255, 0, 0);
        $this->pdf->SetLineWidth(0.5);
        $this->pdf->Line($left, $top, $left + $width, $top);
        $this->pdf->Line($left, $limitBottom, $left + $width, $limitBottom);
        $this->pdf->SetDrawColor(0, 102, 255);
        $pageHeight = $this->pdf->getPageHeight();
        [$marginLeft, $marginRight] = $this->guideMargins();
        $this->pdf->Line($marginLeft, 0, $marginLeft, $pageHeight);
        $this->pdf->Line($this->pdf->getPageWidth() - $marginRight, 0, $this->pdf->getPageWidth() - $marginRight, $pageHeight);
        $this->pdf->SetDrawColor(0, 0, 0);
        $this->pdf->SetLineWidth(0.2);
    }

    protected function guideMargins()
    {
        $left = $this->template->textLeft(70);
        $right = $this->template->textRight(11);
        if($right <= 0){
            $right = $left;
        }

        return [$left, $right];
    }

    protected function pdfTop($value)
    {
        $value = (float)$value;
        $pageHeight = $this->pdf->getPageHeight();

        if($value > $pageHeight){
            return $value / 3.779527559;
        }

        return $value;
    }

    public function html_position()
    {
        /* echo $this->model->committee->com_name_en;
        die();
         */
        //$margin_name = $this->template->field1_mt;
        $html = '<table border="0" width="100%"><tr>
    <td align="'.$this->align.'">';
        $html .= '<table border="0" width="100%" align="'.$this->align.'">';
       
            //$size = $this->template->field1_size;

            $l = '';
		if($this->model->committee->is_jawatankuasa == 1){
			if($this->model->is_leader == 1){
				$l = 'Head of ';
			}
		}

            $html .= '
<tr><td height="' . $this->template->textTop('field1_mt', 100) . '"></td></tr>
<tr><td align="'.$this->align.'" style="font-size:' . $this->template->textSize('field1_size', 23) . 'px">
' . strtoupper($l.$this->model->committee->com_name_en) . '</td></tr>';
        
        $html .= '</table>';
        $html .= '</td></tr>';
        $html .= '</table>';

$tbl = <<<EOD
$html
EOD;

        $this->pdf->writeHTML($tbl, true, false, false, false, '');
    }

    public function startPage()
    {
        $this->filename = 'ICREATE_ECERT';
        $this->pdf->SetCreator('ICREATE_ECERT');
        $this->pdf->SetAuthor('ICREATE_ECERT');
        $this->pdf->SetTitle($this->filename);
        $this->pdf->SetSubject($this->filename);
        $this->pdf->SetKeywords('');

        // set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        // $this->pdf->writeHTML("<strong>hai</strong>", true, 0, true, true);
        // set header and footer fonts
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
        // $this->pdf->SetMargins(25, 10, PDF_MARGIN_RIGHT);

        //$right = $this->template->margin_right + 0;

        $right = $this->template->textRight(12);

        $this->pdf->SetMargins(0, 0, $right);
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

        if ($this->template->is_portrait == 1) {
            $this->pdf->AddPage("P", '', false, false);
            $this->pdf->portrait = true;
        } else {
            $this->pdf->portrait = false;
            $this->pdf->AddPage("L", '', false, false);
        }

        // add a page
    }

    function toPixels($value){
        //pt=point=0.352777778 mm, mm=millimeter=2.8346456675057350125948125904915 points, cm=centimeter=28.346456675057350125948125904915 points, in=inch=72 points=25.4mm
        switch(PDF_UNIT){//http://www.unitconversion.org/unit_converter/typography.html
            case "pt": return $value * 1.328352013;
            case "mm": return $value * 3.779527559;
            case "in": return $value * 96;
            case "cm": return $value * 37.795275591;
        }
        return "TEST";
    }
}
