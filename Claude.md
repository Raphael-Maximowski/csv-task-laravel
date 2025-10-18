# Contexto do Projeto - CSV Task

## Visão Geral
Este é um projeto Laravel 12 (PHP 8.2+) que implementa um sistema de geração de relatórios utilizando o padrão **Factory Method**. O projeto demonstra a aplicação de design patterns para gerar relatórios em diferentes formatos (CSV e PDF).

## Arquitetura

### Estrutura de Pastas
```
app/
├── Http/
│   ├── Controllers/
│   │   └── ReportController.php
│   ├── Interfaces/
│   │   └── ReportInterface.php
│   ├── Reports/
│   │   ├── Factories/
│   │   │   └── ReportFactory.php
│   │   └── Implementations/
│   │       ├── CsvReport.php
│   │       └── PdfReport.php
│   └── Services/
│       └── ReportService.php
routes/
├── api.php
└── web.php
```

### Padrões de Design Implementados

#### 1. Factory Method Pattern
- **Classe:** `ReportFactory` (app/Http/Reports/Factories/ReportFactory.php)
- **Propósito:** Encapsular a criação de diferentes tipos de relatórios
- **Implementações:** `CsvReport` e `PdfReport`

#### 2. Strategy Pattern (Interface)
- **Interface:** `ReportInterface` (app/Http/Interfaces/ReportInterface.php)
- **Métodos:**
  - `generate(array $data): string` - Gera o conteúdo do relatório
  - `getFileType(): string` - Retorna o tipo de arquivo

#### 3. Service Layer Pattern
- **Classe:** `ReportService` (app/Http/Services/ReportService.php)
- **Responsabilidade:** Orquestrar a lógica de negócio de geração de relatórios

## Fluxo de Funcionamento

1. **Requisição HTTP:** Cliente faz GET para `/api/reports/{type}` onde `{type}` pode ser 'csv' ou 'pdf'
2. **Controller:** `ReportController::download()` recebe a requisição
3. **Service:** Delega para `ReportService::generateReport()`
4. **Factory:** `ReportFactory` cria a instância apropriada (`CsvReport` ou `PdfReport`)
5. **Geração:** O método `generate()` é chamado com os dados
6. **Resposta:** Retorna o conteúdo com headers apropriados

## Endpoints da API

### GET /api/reports/{type}
- **Parâmetros:**
  - `type` (string): 'csv' ou 'pdf'
- **Resposta:** Arquivo para download com o relatório gerado
- **Exemplo:** `GET /api/reports/csv`

## Dados de Exemplo
O sistema atualmente usa dados mockados:
```php
$sourceData = [
    ['id' => '1', 'name' => 'Item A'],
    ['id' => '2', 'name' => 'Item B'],
    ['id' => '3', 'name' => 'Item C']
];
```

## Dependências Principais
- **Laravel Framework:** ^12.0
- **PHP:** ^8.2
- **Laravel Tinker:** ^2.10.1

## Falhas e Problemas Identificados

### 1. CRÍTICO: Erro de Sintaxe em Rotas
**Arquivo:** `routes/api.php:7`
```php
Route::get('/reports/{type}', [ReportController::class, 'download&']);
```
**Problema:** Caractere `&` após o nome do método 'download'
**Impacto:** A rota não funcionará, causando erro de sintaxe
**Solução:** Remover o `&`, deixando apenas `'download'`

### 2. CRÍTICO: Namespace Incorreto em ReportInterface
**Arquivo:** `app/Http/Interfaces/ReportInterface.php:5`
```php
namespace App\Interfaces;
```
**Problema:** O namespace declarado não corresponde à estrutura de diretórios real (`app/Http/Interfaces`)
**Impacto:** Classes que tentam importar `App\Interfaces\ReportInterface` causarão erro de autoload
**Locais Afetados:**
- `app/Http/Reports/Implementations/CsvReport.php:5`
- `app/Http/Reports/Implementations/PdfReport.php:5`
- `app/Http/Reports/Factories/ReportFactory.php:7`
- `app/Http/Services/ReportService.php:6`

**Solução:** Alterar para `namespace App\Http\Interfaces;` OU mover o arquivo para `app/Interfaces/`

### 3. MÉDIO: Namespace Incorreto em Implementações
**Arquivos:**
- `app/Http/Reports/Implementations/CsvReport.php:3`
- `app/Http/Reports/Implementations/PdfReport.php:3`

```php
namespace App\Reports\Implementations;
```
**Problema:** Falta o `Http` no namespace
**Correto:** `namespace App\Http\Reports\Implementations;`

### 4. MÉDIO: Namespace Incorreto em ReportService
**Arquivo:** `app/Http/Services/ReportService.php:3`
```php
namespace App\Services;
```
**Problema:** Falta o `Http` no namespace
**Correto:** `namespace App\Http\Services;`

### 5. BAIXO: Falta de Newline em CSV
**Arquivo:** `app/Http/Reports/Implementations/CsvReport.php:14`
```php
$output .= "{$item['id']},{$item['name']}";
```
**Problema:** Falta `\n` no final de cada linha
**Impacto:** Todas as linhas de dados ficarão concatenadas em uma única linha
**Solução:** Alterar para `$output .= "{$item['id']},{$item['name']}\n";`

### 6. BAIXO: Headers HTTP Incorretos
**Arquivo:** `app/Http/Controllers/ReportController.php:34-36`
```php
->header('Content-Type', 'text' . $report->getFileType())
->header('Content-Disposition', 'attachment; filename=relatorio' . $report->getFileType());
```
**Problemas:**
- Content-Type deveria ser `text/csv` ou `application/pdf`, não `textcsv` ou `textpdf`
- Falta ponto antes da extensão no filename (`relatorio.csv` vs `relatóriocsv`)

**Solução:**
```php
->header('Content-Type', 'text/' . $report->getFileType())
->header('Content-Disposition', 'attachment; filename=relatorio.' . $report->getFileType());
```

### 7. BAIXO: Duplicação de Lógica
**Arquivo:** `app/Http/Controllers/ReportController.php:33`
```php
return response($report->generate($sourceData), 200)
```
**Problema:** O método `generate()` já foi chamado na linha 31 dentro do `ReportService`
**Impacto:** Os dados são processados duas vezes desnecessariamente
**Nota:** O `ReportService::generateReport()` não retorna o conteúdo gerado, apenas o objeto `ReportInterface`

### 8. BAIXO: Diretório Incorreto
**Local:** `/home/raphael/Faculdade/csv-task/ReportInterface.php/`
**Problema:** Existe um diretório vazio chamado `ReportInterface.php` na raiz do projeto
**Impacto:** Pode causar confusão
**Solução:** Remover este diretório

### 9. BAIXO: Falta de Validação
**Arquivo:** `app/Http/Controllers/ReportController.php:19`
**Problema:** Não há validação do parâmetro `$type` antes de processá-lo
**Recomendação:** Adicionar validação de request ou middleware

### 10. BAIXO: Dados Hardcoded
**Arquivo:** `app/Http/Controllers/ReportController.php:22-26`
**Problema:** Dados mockados estão no controller
**Recomendação:** Em produção, estes dados deveriam vir de um banco de dados ou serviço externo

## Resumo de Prioridades

### Para o Código Funcionar (URGENTE):
1. Corrigir syntax error na rota (`download&` → `download`)
2. Ajustar todos os namespaces para corresponder à estrutura de diretórios

### Para Funcionar Corretamente (IMPORTANTE):
3. Adicionar `\n` no CSV
4. Corrigir headers HTTP (Content-Type e filename)

### Para Melhorias (OPCIONAL):
5. Remover diretório `ReportInterface.php/` da raiz
6. Adicionar validação de inputs
7. Implementar integração com banco de dados real

## Como Testar

Após corrigir os problemas críticos:
```bash
# Iniciar servidor
php artisan serve

# Testar endpoints
curl http://localhost:8000/api/reports/csv
curl http://localhost:8000/api/reports/pdf
```

## Próximos Passos Recomendados
1. Corrigir todos os erros críticos listados acima
2. Adicionar testes unitários para as classes de relatório
3. Implementar integração com banco de dados
4. Adicionar mais formatos de relatório (Excel, JSON, XML)
5. Implementar autenticação/autorização nas rotas de API
6. Adicionar logging e tratamento de erros mais robusto
