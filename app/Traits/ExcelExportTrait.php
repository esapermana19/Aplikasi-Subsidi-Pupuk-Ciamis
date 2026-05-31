<?php

namespace App\Traits;

use Illuminate\Support\Facades\Response;

trait ExcelExportTrait
{
    /**
     * Helper untuk ekspor ke Excel dengan format HTML agar rapi
     */
    protected function generateExcelHtml($title, $columns, $data, $headerColor = '#10b981')
    {
        $output = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $output .= '<head><meta http-equiv="Content-type" content="text/html;charset=utf-8" /></head><body>';
        $output .= '<table border="1">';
        $output .= '<tr><th colspan="'.count($columns).'" style="font-size: 18px; font-weight: bold; height: 40px; vertical-align: middle;">'.strtoupper($title).'</th></tr>';
        $output .= '<tr><th colspan="'.count($columns).'" style="font-size: 12px; font-weight: normal;">Dicetak pada: '.date('d/m/Y H:i').'</th></tr>';
        $output .= '<tr></tr>';
        $output .= '<tr style="background-color: '.$headerColor.'; color: white;">';
        foreach ($columns as $col) {
            $output .= '<th style="padding: 10px; border: 1px solid #000;">' . $col . '</th>';
        }
        $output .= '</tr>';

        foreach ($data as $row) {
            $output .= '<tr>';
            foreach ($row as $cell) {
                $style = 'border: 1px solid #000;';
                if (is_string($cell) && is_numeric($cell) && strlen($cell) > 10) {
                    $style .= "mso-number-format:'\@';";
                }
                $output .= '<td style="'.$style.'">' . $cell . '</td>';
            }
            $output .= '</tr>';
        }
        $output .= '</table></body></html>';

        return $output;
    }

    protected function exportToExcel($filename, $title, $columns, $data, $headerColor = '#10b981')
    {
        $output = $this->generateExcelHtml($title, $columns, $data, $headerColor);

        return Response::make($output)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }
}
