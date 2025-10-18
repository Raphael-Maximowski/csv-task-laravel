<?php

namespace App\Services;

use App\Reports\Factories\ReportFactory;
use App\Interfaces\ReportInterface;

class ReportService
{
protected ReportFactory $reportFactory;

// Injeta a Factory via construtor
public function __construct(ReportFactory $reportFactory)
{
    $this->reportFactory = $reportFactory;
}

public function generateReport(string $type, array $sourceData): ReportInterface
{

$reportGenerator = $this->reportFactory->createReport($type);

$reportContent = $reportGenerator->generate($sourceData);

return $reportGenerator;
}

}