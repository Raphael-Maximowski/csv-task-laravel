<?php

namespace App\http\Reports\Implementations;

use App\Http\Interfaces\ReportInterface;

class CsvReport implements ReportInterface
{
    public function generate(array $data): string
    {
        $output = "\nid | Nome\n";

        foreach ($data as $item) {
            $output .= "\n{$item['id']} | {$item['name']}";
        }

        return "Conteudo CSV Gerado: ". $output;
    }

    public function getFileType(): string
    {
        return 'csv';
    }
}