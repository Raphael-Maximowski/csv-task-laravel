<?php

namespace App\Reports\Implementations;

use App\Interfaces\ReportInterface;

class PdfReport implements ReportInterface
{
public function generate(array $data): string
{
$count = count($data);
return "Conteudo PDF Gerado: Relatório de {$count} itens com cabeçalho e rodapé";
}

public function getFileType(): string
{
return 'pdf';
}
}