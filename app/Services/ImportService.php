<?php

namespace App\Services;

use App\Imports\PurchasePhoneImport;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportService implements WithMultipleSheets
{
    use WithConditionalSheets;

    public function conditionalSheets(): array
    {
        return [
            'Stock' => new PurchasePhoneImport(),
        ];
    }
}
