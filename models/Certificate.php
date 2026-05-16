<?php
namespace app\models;

use Yii;
use yii\helpers\Html;

class Certificate
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
        $this->pdf->SetFont('iniriaserif', '', 10);
        //$this->pdf->SetTextColor(35, 22, 68);
        $preset = $this->template->set_type;
        if ($preset == 1) {
            $this->html_name();
            $this->html_program();
            $this->drawStoredNameLimitLine();
            $this->pdf->SetFont('iniriaserif', '', 10);
        } else {
            $this->writeTextBlock(0, $this->template->custom_html);
        }

        
    }


    public function html_name()
    {
        $top = $this->template->textTop('name_mt', 250);
        $size = $this->template->textSize('name_size', 23);
        $kira = $this->model->memberCountAll;
        if($kira > 10){
            $size = min($size, 21);
        }

        $this->writeNameBlock($top, $size);
    }

    public function html_program()
    {
        /* echo $this->model->committee->com_name_en;
        die();
         */
        $top = $this->template->textTop('field1_mt', 89) + 120;
        $size = $this->template->textSize('field1_size', 20);
        $this->writeTextBlock($top, '<span style="font-size:' . $size . 'px">' . strtoupper($this->model->programNameLong) . '</span>');
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

    protected function writeNameBlock($top, $fontSize)
    {
        $topSetting = (float)$top;
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

        $limitBottom = $this->pdfTop($this->template->nameLimitY('field1_mt', 101));
        $html = $this->preferredNameHtml($width, $fontSize, $this->configuredNameAreaHeight($topSetting));
        $showNameBorder = $this->template->showNameBorder();
        $tableBorder = $showNameBorder ? '1' : '0';
        $tableStyle = $showNameBorder ? 'border:3px solid #ff0000;' : '';
        $cellStyle = $showNameBorder ? 'border:1px solid #ff0000; color:#000000;' : '';
        $content = '<table border="' . $tableBorder . '" cellpadding="0" cellspacing="0" width="100%" style="' . $tableStyle . '"><tr><td align="' . $this->align . '" style="' . $cellStyle . '">' . $html . '</td></tr></table>';

        $this->pdf->writeHTMLCell($width, 0, $left, $top, $content, 0, 1, false, true, $this->tcpdfAlign(), true);
        $this->nameLimitLine = [$left, $width, $limitBottom];
    }

    protected function preferredNameHtml($width, $fontSize, $nameAreaHeight)
    {
        $names = $this->memberNames();
        $lineHeightTotal = count($names) * $this->nameLineHeight($fontSize);

        if(count($names) > 1 && $lineHeightTotal <= max(1, $nameAreaHeight)){
            $this->pdf->SetFont('iniriaserif', '', max(1, (float)$fontSize * 0.63));
            $allNamesFitOneLine = true;
            foreach($names as $name){
                if($this->pdf->GetStringWidth(strtoupper($name)) > $width){
                    $allNamesFitOneLine = false;
                    break;
                }
            }

            if($allNamesFitOneLine){
                $lines = array_map(function($name) {
                    return Html::encode(strtoupper($name));
                }, $names);

                return '<span style="font-size:' . $fontSize . 'px; line-height:0.92;">' . implode('<br>', $lines) . '</span>';
            }
        }

        return '<span style="font-size:' . $fontSize . 'px; line-height:1;">' . Html::encode(strtoupper($this->model->memberStr)) . '</span>';
    }

    protected function configuredNameAreaHeight($top)
    {
        if(method_exists($this->template, 'nameLimitY')){
            return max(8, (float)$this->template->nameLimitY('field1_mt', 101) - (float)$top);
        }

        return 8;
    }

    protected function memberNames()
    {
        $names = array_map('trim', preg_split('/,|<br\s*\/?>/i', (string)$this->model->memberStr));
        $names = array_filter($names, function($name) {
            return $name !== '';
        });

        return array_values($names);
    }

    protected function nameLineHeight($fontSize)
    {
        return max(4.5, (float)$fontSize * 0.36);
    }

    protected function drawStoredNameLimitLine()
    {
        if(!$this->template->showNameBorder() || $this->nameLimitLine === null){
            return;
        }

        [$left, $width, $limitBottom] = $this->nameLimitLine;
        $this->pdf->SetDrawColor(255, 0, 0);
        $this->pdf->SetLineWidth(0.5);
        $this->pdf->Line($left, $limitBottom, $left + $width, $limitBottom);
        $this->pdf->SetDrawColor(0, 0, 0);
        $this->pdf->SetLineWidth(0.2);
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
        $left = $this->template->textLeft(10);
        $right = $this->template->textRight(10);

        $this->pdf->SetMargins($left, 0, $right);
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
