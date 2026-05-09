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
        $this->pdf->SetFont('iniriaserif', '', 10);
        //$this->pdf->SetTextColor(35, 22, 68);
        $preset = $this->template->set_type;
        if ($preset == 1) {
            $this->html_name();
            $this->html_position();
            $this->html_program();
            $this->pdf->SetFont('iniriaserif', '', 10);
        } else {
            $html = $this->template->custom_html;
        }
    }


    public function html_name()
    {


        $top = $this->template->textTop('name_mt', 350);
        $size = $this->template->textSize('name_size', 27);
        $this->writeTextBlock($top, '<span style="font-size:' . $size . 'px">' . strtoupper($this->model->fullname) . '</span>');
    }

    public function html_position()
    {
        /* echo $this->model->committee->com_name_en;
        die();
         */
        //$margin_name = $this->template->field1_mt;
        //echo $this->model->session_name;die();
        $speaker = trim((string)$this->model->speaker);
        $speakerHtml = $speaker === '' ? '' : '<br/><span style="font-size:16px">BY ' . strtoupper($speaker) . '</span>';
        $top = $this->template->textTop('field1_mt', 410);
        $size = $this->template->textSize('field1_size', 23);
        $this->writeTextBlock($top, '<span style="font-size:' . $size . 'px">' . strtoupper($this->model->session_name) . $speakerHtml . '</span>');
    }

    public function html_program()
    {
        /* echo $this->model->committee->com_name_en;
        die();
         */
        //$margin_name = $this->template->field1_mt;
        $top = $this->template->textTop('field2_mt', 480);
        $size = $this->template->textSize('field2_size', 23);
        $this->writeTextBlock($top, '<span style="font-size:' . $size . 'px">' . strtoupper($this->model->program_name) . '</span>');
    }

    protected function writeTextBlock($top, $html)
    {
        $top = $this->pdfTop($top);
        $left = $this->horizontalMargin('margin_left');
        $right = $this->horizontalMargin('margin_right');
        if ($right <= 0) {
            $right = $left;
        }

        $width = $this->pdf->getPageWidth() - $left - $right;
        if ($width <= 0) {
            $left = 0;
            $width = $this->pdf->getPageWidth();
        }

        $this->pdf->writeHTMLCell($width, 0, $left, $top, '<div style="text-align:' . $this->align . '">' . $html . '</div>', 0, 1, false, true, $this->tcpdfAlign(), true);
    }

    protected function pdfTop($value)
    {
        $value = (float)$value;
        $pageHeight = $this->pdf->getPageHeight();

        if ($value > $pageHeight) {
            return $value / 3.779527559;
        }

        return $value;
    }

    protected function horizontalMargin($attribute)
    {
        return $this->template->$attribute === null || $this->template->$attribute === '' ? 0 : max(0, (float)$this->template->$attribute);
    }

    protected function tcpdfAlign()
    {
        if ($this->align === 'left') {
            return 'L';
        }

        if ($this->align === 'right') {
            return 'R';
        }

        return 'C';
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
