<?php

namespace App\http\Reports\Implementations;

use App\Http\Interfaces\ReportInterface;

class JsonReport implements ReportInterface
{
    public function generate(array $data): string
    {

        $output = array_map(function($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name']
            ];
        }, $data); 

        return "Conteudo Json Gerado: ". json_encode($output);
    }

    public function getFileType(): string
    {
        return 'JSON';
    }
}