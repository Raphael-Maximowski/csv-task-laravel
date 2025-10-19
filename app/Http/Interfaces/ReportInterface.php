<?php

// app/Interfaces/ReportInterface.php

namespace App\Http\Interfaces;

interface ReportInterface
{
    // Define um método para gerar o conteúdo do relatório
    public function generate(array $data): string;

    // Define um método para retornar o tipo de arquivo
    public function getFileType(): string;
}