<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Purchase;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PurchasePhoneImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        dd($collection);
        foreach ($collection as $row){
            $purchase = Purchase::updateOrCreate(
                ['shipping_date' => $row[3], 'shipping_source' => $row[2]],
                ['shipping_date' => $row[3], 'shipping_source' => $row[2]]
            );
            $brand = Brand::updateOrCreate(['name' => $row[5]], ['name' => $row[5]]);
            $brandModel = $brand->brandModel()->create([
                'message' => 'A new comment.',
            ]);
        }
    }
}
