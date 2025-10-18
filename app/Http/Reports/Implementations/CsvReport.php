<?php

namespace App\Reports\Implementations;

use App\Interfaces\ReportInterface;

class CsvReport implements ReportInterface
{
    public function generate(array $data): string
    {
        $output = "id,Nome\n";

        foreach ($data as $item) {
        $output .= "{$item['id']},{$item['name']}";
        }

        return "Conteudo CSV Gerado: ". $output;
    }

    public function getFileType(): string
    {
        return 'csv';
    }
}