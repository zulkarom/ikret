<?php
namespace app\models;

class CertificateMedal extends CertificateMedalBase
{
    public function html_award()
    {
        $this->pdf->SetXY($this->template->textLeft(70), $this->template->textTop('field1_mt', 101));

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

    public function html_program()
    {
        $this->pdf->SetXY($this->template->textLeft(70), $this->template->textTop('field2_mt', 154));

        $html = '<table border="0"><tr>
    <td align="'.$this->align.'">';
        $html .= '<table border="0" align="'.$this->align.'">';

        $html .= '
<tr><td align="'.$this->align.'" style="font-size:' . $this->template->textSize('field2_size', 20) . 'px">
' . strtoupper($this->model->programNameLong) . '</td></tr>';

        $html .= '</table>';
        $html .= '</td></tr>';
        $html .= '</table>';

$tbl = <<<EOD
$html
EOD;
        $this->pdf->SetFont('iniriaserif', '', 0);
        $this->pdf->writeHTML($tbl, true, false, false, false, '');
    }
}
