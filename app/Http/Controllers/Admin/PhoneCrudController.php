<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PhoneRequest;
use App\Models\Phone;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\EditableColumns\Http\Controllers\Operations\MinorUpdateOperation;
use Backpack\Pro\Http\Controllers\Operations\BulkDeleteOperation;

class PhoneCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;
    use BulkDeleteOperation;
    use MinorUpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Phone::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/phone');
        CRUD::setEntityNameStrings('phone', 'phones');

        if (! backpack_user()->can('phone.view')) {
            CRUD::denyAccess(['show']);
        }
        if (! backpack_user()->can('phone.create')) {
            CRUD::denyAccess(['create']);
        }
        if (! backpack_user()->can('phone.list')) {
            CRUD::denyAccess(['list']);
        }
        if (! backpack_user()->can('phone.update')) {
            CRUD::denyAccess(['update']);
        }
        if (! backpack_user()->can('phone.delete')) {
            CRUD::denyAccess(['delete']);
        }
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([
            // any type of relationship
            'name' => 'brand_model_id', // name of relationship method in the model
            'type' => 'relationship',
            'label' => 'Brand - model', // Table column heading
            // OPTIONAL
            'entity' => 'brandModel', // the method that defines the relationship in your Model
            'attribute' => 'full_name', // foreign key attribute that is shown to user
            'model' => 'App\Models\BrandModel::class', // foreign key model
        ]);
        CRUD::column('item_cost');
        CRUD::column('imei_1');
        CRUD::column('imei_2');
        CRUD::column('rom_size');
        CRUD::column('color');
        CRUD::column('description');
        CRUD::column('item_sellout_price');
        CRUD::column('is_new');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PhoneRequest::class);

        CRUD::field('brandModel')->wrapper([
            'class' => 'form-group col-md-6',
        ]);
        CRUD::field('item_cost')->wrapper([
            'class' => 'form-group col-md-6',
        ]);
        CRUD::field('imei_1')->wrapper([
            'class' => 'form-group col-md-6',
        ]);
        CRUD::field('imei_2')->wrapper([
            'class' => 'form-group col-md-6',
        ]);
        CRUD::field('rom_size')->wrapper([
            'class' => 'form-group col-md-6',
        ]);
        CRUD::field('color')->wrapper([
            'class' => 'form-group col-md-6',
        ]);
//        CRUD::field('item_sellout_price')->wrapper([
//            'class' => 'form-group col-md-6',
//        ]);
        CRUD::field('is_new')->wrapper([
            'class' => 'form-group col-md-6 align-self-center',
        ]);
        CRUD::field('description')->type('textarea');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        $id = $this->crud->getCurrentEntryId() ?? $id;

        $phone = Phone::find($id);

        if ($phone->item_sellout_price != null || $phone->item_sellout_price != 0) {
            return response()->json(['message' => 'You cannot delete a sold phone!'], 403);
        }

        return $this->crud->delete($id);
    }
}
