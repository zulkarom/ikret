<?php
namespace app\models;

use Yii;
use yii\helpers\Html;

class CertificateAchievement
{

    public $model;
    public $template;
    public $pdf;
    public $system;
    public $filename;
    public $width;
    public $height;
    public $align = 'center';
    public $achievementId;
    protected $nameBoundary = null;

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
            $this->html_achievement_sentence();
            $this->html_achievement_name();
            $this->html_program();
            $this->drawStoredNameBoundary();
            
        } else {
            $html = $this->template->custom_html;
        }

        
    }


    public function html_name()
    {
        $margin_name = $this->template->textTop('name_mt', 235);
        $size = $this->template->textSize('name_size', 23);
        $kira = $this->model->memberCountAll;
        if($kira > 10){
            $size = min($size, 20.5);
        }

        $sideMargin = $this->nameSideMargin($margin_name, $size);
        $this->nameBoundary = [$margin_name, $sideMargin, 8];
        $this->writeTextBlock($margin_name, '<span style="font-size:' . $size . 'px">' . strtoupper($this->model->memberStr) . '</span>', $sideMargin);
    }

    public function html_achievement_sentence()
    {
        $title = $this->achievementWinnerTitle();
        $text = $title === ''
            ? Html::encode('have achieved')
            : 'have achieved the title of <b>' . Html::encode(strtoupper($title)) . '</b> in';
        $top = $this->template->textTop('field1_mt', 101);
        $size = $this->template->textSize('field1_size', 20);

        $this->pdf->SetFont('iniriaserif', '', 0);
        $this->writeTextBlock($top, '<span style="font-size:' . $size . 'px">' . $text . '</span>');
    }

    public function html_achievement_name()
    {
        $top = $this->template->textTop('field2_mt', 123);
        $size = $this->template->textSize('field2_size', 24);
        $text = strtoupper($this->achievementName());

        $this->pdf->SetFont('iniriaserif', 'b', 0);
        $this->writeTextBlock($top, '<span style="font-size:' . $size . 'px">' . Html::encode($text) . '</span>');
    }

    public function html_program()
    {
        //$a->awardTextColor()
        /* echo $this->model->committee->com_name_en;
        die();
         */
        $top = $this->template->textTop('field3_mt', 155);
        $size = $this->template->textSize('field3_size', 20);

        $this->pdf->SetFont('iniriaserif', '', 0);
        $this->writeTextBlock($top, '<span style="font-size:' . $size . 'px">' . strtoupper($this->model->programNameLong) . '</span>');
    }

    protected function achievement()
    {
        if(!$this->model->achievements){
            return null;
        }

        foreach($this->model->achievements as $achievement){
            if($this->achievementId && (int)$achievement->achieve_id !== (int)$this->achievementId){
                continue;
            }
            return $achievement;
        }

        return null;
    }

    protected function achievementWinnerTitle()
    {
        $achievement = $this->achievement();
        if(!$achievement || !$achievement->winnerTitle){
            return '';
        }

        return trim((string)$achievement->winnerTitle->title_name);
    }

    protected function achievementName()
    {
        $achievement = $this->achievement();
        if(!$achievement || !$achievement->achieve){
            return '';
        }

        return trim((string)$achievement->achieve->name);
    }

    protected function writeTextBlock($top, $html, $sideMargin = null)
    {
        $top = $this->pdfTop($top);
        if($sideMargin !== null){
            $left = max(0, (float)$sideMargin);
            $right = $left;
        }else{
            $left = $this->horizontalMargin('margin_left', 70);
            $right = $this->horizontalMargin('margin_right', 11);
            if ($right <= 0) {
                $right = $left;
            }
        }

        $width = $this->pdf->getPageWidth() - $left - $right;
        if ($width <= 0) {
            $left = 0;
            $width = $this->pdf->getPageWidth();
        }

        $this->pdf->writeHTMLCell($width, 0, $left, $top, '<div style="text-align:' . $this->align . '">' . $html . '</div>', 0, 1, false, true, $this->tcpdfAlign(), true);
    }

    protected function nameSideMargin($nameTop, $fontSize)
    {
        $pageWidth = $this->pdf->getPageWidth();
        $defaultLeft = $this->horizontalMargin('margin_left', 70);
        $defaultRight = $this->horizontalMargin('margin_right', 11);
        $defaultSide = min($defaultLeft, $defaultRight > 0 ? $defaultRight : $defaultLeft);
        $fieldTop = $this->template->nameLimitY('field1_mt', 101);
        $availableHeight = max(8, $this->pdfTop($fieldTop) - $this->pdfTop($nameTop) - 8);
        $lineHeight = max(6.5, (float)$fontSize * 0.42);
        $maxLines = max(1, (int)floor($availableHeight / $lineHeight));
        $plainName = trim(strip_tags(str_replace(['<br />', '<br>', '<br/>'], ' ', (string)$this->model->memberStr)));
        $nameLength = strlen($plainName);

        $candidates = [
            $pageWidth * 0.30,
            $pageWidth * 0.26,
            $pageWidth * 0.22,
            $pageWidth * 0.18,
            $pageWidth * 0.14,
            $pageWidth * 0.10,
            $defaultSide,
        ];

        foreach($candidates as $side){
            $side = max(0, min((float)$side, ($pageWidth / 2) - 20));
            $width = $pageWidth - ($side * 2);
            if($width <= 0){
                continue;
            }

            $estimatedCharsPerLine = max(18, (int)floor($width / max(1, (float)$fontSize * 0.12)));
            $estimatedLines = max(1, (int)ceil($nameLength / $estimatedCharsPerLine));
            if($estimatedLines <= $maxLines){
                return $side;
            }
        }

        return $defaultSide;
    }

    protected function drawNameBoundary($nameTop, $sideMargin, $bottomPadding)
    {
        if(!$this->template->showNameBorder()){
            return;
        }

        $top = $this->pdfTop($nameTop);
        $bottom = $this->pdfTop($this->template->nameLimitY('field1_mt', 101));
        $height = max(8, $bottom - $top);
        $left = max(0, (float)$sideMargin);
        $width = $this->pdf->getPageWidth() - ($left * 2);
        if($width <= 0){
            return;
        }

        $this->pdf->SetDrawColor(255, 0, 0);
        $this->pdf->SetTextColor(255, 0, 0);
        $this->pdf->SetLineWidth(0.8);
        $this->pdf->Rect($left, $top, $width, $height, 'D');
        $this->pdf->Line($left, $top, $left + $width, $top);
        $this->pdf->Line($left, $top + $height, $left + $width, $top + $height);
        $this->pdf->SetFont('helvetica', '', 6);
        $this->pdf->Text($left + 1, max(0, $top - 3), 'NAME AREA');
        $this->pdf->SetLineWidth(0.2);
        $this->pdf->SetDrawColor(0, 0, 0);
        $this->pdf->SetTextColor(0, 0, 0);
    }

    protected function drawStoredNameBoundary()
    {
        if($this->nameBoundary === null){
            return;
        }

        [$nameTop, $sideMargin, $bottomPadding] = $this->nameBoundary;
        $this->drawNameBoundary($nameTop, $sideMargin, $bottomPadding);
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

    protected function horizontalMargin($attribute, $default)
    {
        if($attribute === 'margin_left'){
            return max(0, (float)$this->template->textLeft($default));
        }

        return max(0, (float)$this->template->textRight($default));
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

        $right = $this->template->textRight(11);

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
