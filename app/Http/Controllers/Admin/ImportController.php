<?php

namespace App\Http\Controllers\Admin;

use App\Imports\PurchasePhoneImport;
use App\Services\ImportService;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ImportController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ImportController extends Controller
{
    use WithConditionalSheets;

    public function store(Request $request): JsonResponse
    {
        $import = new ImportService();
        $import->onlySheets('Stock');
        $excel = Excel::import($import, request()->all()['file']);
        $excel = Excel::toArray($import, request()->all()['file']);
        dd($excel);
        try {

            return response()->json(['message' => 'Import success'], Response::HTTP_CREATED);

        } catch (Exception $exception) {
            return response()->json(['message' => 'Import failed'], Response::HTTP_BAD_REQUEST);
        }

    }

    public function conditionalSheets(): array
    {
        return [
            'phoneSheet' => new PurchasePhoneImport(),
        ];
    }
}
