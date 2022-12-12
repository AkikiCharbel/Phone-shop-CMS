<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('q');
        $rowTriggered = $request->get('triggeredBy')['rowNumber'];
        $oldPhoneId = false;
        foreach ($request->get('form') as $formItem) {
            if ($formItem['name'] == "soled_phones[$rowTriggered][soled_phone_id]") {
                $oldPhoneId = $formItem['value'];
            }
        }

        $result = Phone::where('item_sellout_price', null);
        if ($searchTerm) {
            $result->where(function (Builder $query) use ($searchTerm) {
                $query->where('imei_1', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('imei_2', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('rom_size', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('color', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhereHas('brandModel', function (Builder $query) use ($searchTerm) {
                        $query->where('name', 'LIKE', '%'.$searchTerm.'%');
                    })
                    ->orWhereHas('brandModel.brand', function (Builder $query) use ($searchTerm) {
                        $query->where('name', 'LIKE', '%'.$searchTerm.'%');
                    });
            });
        }
        if ($oldPhoneId) {
            $result->orWhere('id', $oldPhoneId);
        }

        return $result->paginate(10);
    }
}
