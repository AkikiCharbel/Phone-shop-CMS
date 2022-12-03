<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;

/**
 * Class ImportController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ImportController extends Controller
{
//    public function index()
//    {
//        return view('admin.import', [
//            'title' => 'Import',
//            'breadcrumbs' => [
//                trans('backpack::crud.admin') => backpack_url('dashboard'),
//                'Import' => false,
//            ],
//            'page' => 'resources/views/admin/import.blade.php',
//            'controller' => 'app/Http/Controllers/Admin/ImportController.php',
//        ]);
//    }

    public function store(Request $request){
        dd(request()->all());
    }
}
