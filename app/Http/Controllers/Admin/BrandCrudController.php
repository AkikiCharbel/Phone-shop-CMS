<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BrandRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class BrandCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Brand::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/brand');
        CRUD::setEntityNameStrings('brand', 'brands');
        if (! backpack_user()->can('brand.view')) {
            CRUD::denyAccess(['show']);
        }
        if (! backpack_user()->can('brand.create')) {
            CRUD::denyAccess(['create']);
        }
        if (! backpack_user()->can('brand.list')) {
            CRUD::denyAccess(['list']);
        }
        if (! backpack_user()->can('brand.update')) {
            CRUD::denyAccess(['update']);
        }
        if (! backpack_user()->can('brand.delete')) {
            CRUD::denyAccess(['delete']);
        }
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
    }

    protected function setupShowOperation()
    {
        CRUD::column('name');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(BrandRequest::class);

        CRUD::field('name');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
