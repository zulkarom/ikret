<?php
namespace app\models;

use yii\helpers\Html;

trait CertificateNameLayoutTrait
{
    protected function certificateNameList($nameString)
    {
        $names = array_map('trim', preg_split('/,|<br\s*\/?>/i', (string)$nameString));
        $names = array_filter($names, function($name) {
            return $name !== '';
        });

        return array_values($names);
    }

    protected function certificateNameLineHeight($fontSize)
    {
        return max(4.5, (float)$fontSize * 0.25);
    }

    protected function certificateOneNamePerLineHtml($names, $fontSize)
    {
        $lines = array_map(function($name) {
            return Html::encode(strtoupper($name));
        }, $names);

        return '<span style="font-size:' . $fontSize . 'px; line-height:0.92;">' . implode('<br>', $lines) . '</span>';
    }

    protected function certificateCommaNameHtml($nameString, $fontSize)
    {
        return '<span style="font-size:' . $fontSize . 'px; line-height:1;">' . Html::encode(strtoupper((string)$nameString)) . '</span>';
    }

    protected function certificateAllNamesFitOneLine($names, $width, $fontSize)
    {
        $this->pdf->SetFont('iniriaserif', '', max(1, (float)$fontSize * 0.63));
        foreach($names as $name){
            if($this->pdf->GetStringWidth(strtoupper($name)) > $width){
                return false;
            }
        }

        return true;
    }

    protected function certificatePreferredNameHtml($nameString, $width, $fontSize, $nameAreaHeight, $commaHtml = null, $estimateFactor = 0.12)
    {
        $names = $this->certificateNameList($nameString);
        $lineHeightTotal = count($names) * $this->certificateNameLineHeight($fontSize);

        if(count($names) > 1 && $lineHeightTotal <= max(1, $nameAreaHeight) && $this->certificateAllNamesFitOneLine($names, $width, $fontSize)){
            return [$this->certificateOneNamePerLineHtml($names, $fontSize), $lineHeightTotal];
        }

        $html = $commaHtml === null ? $this->certificateCommaNameHtml($nameString, $fontSize) : $commaHtml;

        return [$html, $this->certificateEstimatedNameTextHeight($nameString, $width, $fontSize, $estimateFactor)];
    }

    protected function certificateEstimatedNameTextHeight($nameString, $width, $fontSize, $estimateFactor = 0.12)
    {
        $plainName = trim(strip_tags(str_replace(['<br />', '<br>', '<br/>'], ' ', (string)$nameString)));
        $nameLength = strlen($plainName);
        $estimatedCharsPerLine = max(18, (int)floor($width / max(1, (float)$fontSize * $estimateFactor)));
        $estimatedLines = max(1, (int)ceil($nameLength / $estimatedCharsPerLine));

        return $estimatedLines * max(6.5, (float)$fontSize * 0.42);
    }
}
