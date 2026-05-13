<?php
namespace app\models;

class CertificateMedal extends CertificateExcellence
{
    public function html_award()
    {
        $html = '<table border="0"><tr>
    <td align="'.$this->align.'">';
        $html .= '<table border="0" align="'.$this->align.'">';

        $html .= '
<tr><td></td></tr>
<tr><td align="'.$this->align.'" style="font-size:' . $this->template->textSize('field1_size', 26) . 'px">
' . $this->model->awardTextColor() . '</td></tr>';

        $html .= '</table>';
        $html .= '</td></tr>';
        $html .= '</table>';

$tbl = <<<EOD
$html
EOD;
        $this->pdf->SetFont('helvetica', 'b', 0);
        $this->pdf->writeHTML($tbl, true, false, false, false, '');
    }
}
