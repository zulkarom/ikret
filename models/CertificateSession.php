<?php
namespace app\models;

use Yii;

class CertificateSession
{

    public $model;
    public $template;
    public $pdf;
    public $system;
    public $filename;
    public $width;
    public $height;
    public $align = 'center';

    public $frontend = false;

    public function generatePdf()
    {

        if ($this->template->is_portrait == 1) {
            $o = "P";
        } else {
            $o = "L";
        }
        $this->pdf = new StartPdf($o, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        
        $this->pdf->image_background = 'images/' . $this->template->template_file;

        

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
        //$left = $this->template->margin_left + 0;
        $left = 75;
        $this->pdf->SetFont('montserrat', 'b', 10);
        //$this->pdf->SetTextColor(35, 22, 68);
        $preset = $this->template->set_type;
        if ($preset == 1) {
            $this->pdf->SetXY($left,0);
            $this->html_name();
            $this->pdf->SetXY($left,65);
            $this->html_position();
            $this->pdf->SetXY($left,110);
            $this->html_program();
            $this->pdf->SetXY($left,0);
            $this->pdf->SetFont('montserrat', '', 10);
        } else {
            $html = $this->template->custom_html;
        }
    }


    public function html_name()
    {


        $margin_name = $this->template->name_mt;

        $html = '<table border="0">
<tr>

    <td align="'.$this->align.'">';

        $html .= '<table border="0" align="'.$this->align.'">';

        if ($margin_name > 0) {
            $size = $this->template->name_size;
            $html .= '
<tr><td height="' . $margin_name . '"></td></tr>
<tr><td align="'.$this->align.'" style="font-size:' . $size . 'px">' . strtoupper($this->model->fullname) . '</td></tr>';
        }



        $html .= '</table>';

        $html .= '</td>
</tr>';
        $html .= '</table>';

$tbl = <<<EOD
$html
EOD;

        $this->pdf->writeHTML($tbl, true, false, false, false, '');
    }

    public function html_position()
    {
        /* echo $this->model->committee->com_name_en;
        die();
         */
        //$margin_name = $this->template->field1_mt;
        //echo $this->model->session_name;die();
        $html = '<table border="0"><tr>
    <td align="'.$this->align.'">';
        $html .= '<table border="0" align="'.$this->align.'">';
       


            $html .= '
<tr><td height="100"></td></tr>
<tr><td align="'.$this->align.'" style="font-size:23px">
' . strtoupper($this->model->session_name) . '<br/>
<span style="font-size:16px">BY ' . strtoupper($this->model->speaker) . '</span>
</td></tr>';
        
        $html .= '</table>';
        $html .= '</td></tr>';
        $html .= '</table>';

$tbl = <<<EOD
$html
EOD;

        $this->pdf->writeHTML($tbl, true, false, false, false, '');
    }

    public function html_program()
    {
        /* echo $this->model->committee->com_name_en;
        die();
         */
        //$margin_name = $this->template->field1_mt;
        $html = '<table border="0"><tr>
    <td align="'.$this->align.'">';
        $html .= '<table border="0" align="'.$this->align.'">';
       


            $html .= '
<tr><td height="55"></td></tr>
<tr><td align="'.$this->align.'" style="font-size:23px">
' . strtoupper($this->model->program_name) . '
</td></tr>';
        
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

        $right = 12;

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
