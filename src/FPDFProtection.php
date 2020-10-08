<?php

namespace TheoryTest\Car;

use FPDF;

/**
 * @codeCoverageIgnore
 */
class FPDFProtection extends FPDF
{
    /**
     * Create a PDF table
     * @param array $header This should be the table headers as an array
     * @param array $data This should be that table data as a multi-dimensional array
     * @param array|string $widths The widths of the table columns as aran array if required
     * @param int $height The hight given to the table fields
     * @param boolean $left If left aligned columns set to true for center aligned set to false
     */
    public function basicTable($header, $data, $widths = '', $height = 6, $left = false)
    {
        $first = true;
        $this->SetFont('Arial', 'B');
        foreach ($header as $col) {
            if ($first === true) {
                $first = false;
                $currentwidth = intval(current($widths));
            } else {
                $currentwidth = intval(next($widths));
            }
            $this->Cell($currentwidth, 7, $col, 1, 0, 'C');
        }
        $this->Ln();

        $this->SetFont('Arial', '');
        foreach ($data as $row) {
            reset($widths);
            $first = true;
            $i = 1;
            foreach ($row as $col) {
                if ($first === true) {
                    $first = false;
                    $currentwidth = intval(current($widths));
                } else {
                    $currentwidth = intval(next($widths));
                }
                if ($left !== false) {
                    if ($i == $left) {
                        $align = 'L';
                    } else {
                        $align = 'C';
                    }
                } else {
                    $align = 'C';
                }
                $this->Cell($currentwidth, $height, $col, 1, 0, $align);
                $i++;
            }
            $this->Ln();
        }
    }
}
