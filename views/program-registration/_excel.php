<?php

use kartik\export\ExportMenu;

$exportColumns = [
    ['class' => 'yii\grid\SerialColumn'],
            'course_code',
            'course_name',
            'course_name_bi',
            'credit_hour',
            [
                'attribute' => 'program.pro_name_short',
                'label' => 'Program',
            ],
        ];

?>
<div style="display: none;">
<?=ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $exportColumns,
    'filename' => 'I-CREATE_DATA_' . date('Y-m-d'),
    'onRenderSheet'=>function($sheet, $grid){
        $sheet->getStyle('A2:'.$sheet->getHighestColumn().$sheet->getHighestRow())
        ->getAlignment()->setWrapText(true);
    },
    'exportConfig' => [
        ExportMenu::FORMAT_PDF => false,
        ExportMenu::FORMAT_EXCEL_X => false,
    ],
]);?>
</div>