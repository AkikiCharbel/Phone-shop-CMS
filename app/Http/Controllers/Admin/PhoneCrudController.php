<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PhoneRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PhoneCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PhoneCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Phone::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/phone');
        CRUD::setEntityNameStrings('phone', 'phones');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('purchase_id');
        CRUD::column('brand_model_id');
        CRUD::column('item_cost');
        CRUD::column('imei_1');
        CRUD::column('imei_2');
        CRUD::column('rom_size');
        CRUD::column('color');
        CRUD::column('description');
        CRUD::column('item_sellout_price');
        CRUD::column('is_new');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PhoneRequest::class);

        CRUD::field('purchase_id');
        CRUD::field('brand_model_id');
        CRUD::field('item_cost');
        CRUD::field('imei_1');
        CRUD::field('imei_2');
        CRUD::field('rom_size');
        CRUD::field('color');
        CRUD::field('description');
        CRUD::field('item_sellout_price');
        CRUD::field('is_new');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
