<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\Purchase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PurchasePhoneImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        DB::transaction(function () use ($collection){
            $collection->shift();
            foreach ($collection as $row){
                $purchase = Purchase::updateOrCreate(
                    ['shipping_date' => Date::excelToDateTimeObject($row[3]), 'shipping_source' => $row[2]],
                    ['shipping_date' => Date::excelToDateTimeObject($row[3]), 'shipping_source' => $row[2], 'date' => Date::excelToDateTimeObject($row[3])]
                );

                $brand = Brand::updateOrCreate(['name' => $row[5]], ['name' => $row[5]]);

                $brandModel = BrandModel::updateOrCreate(
                    ['brand_id' => $brand->id, 'name' => $row[6]],
                    ['brand_id' => $brand->id, 'name' => $row[6]]
                );

                if ($row[10] == null){
                    dd($row);
                }
                $brandModel->phones()->create([
                    'purchase_id' => $purchase->id,
                    'item_cost' => $row[4],
                    'imei_1' => $row[7],
                    'imei_2' => $row[8],
                    'rom_size' => explode(' ', trim($row[9]))[0],
                    'color' => $row[10],
                    'description' => null,
                    'item_sellout_price' => null,
                    'is_new' => $row[16],
                ]);
            }
        });

    }

}
