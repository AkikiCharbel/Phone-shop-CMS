<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SelloutRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SelloutCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SelloutCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Sellout::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/sellout');
        CRUD::setEntityNameStrings('sellout', 'sellouts');
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
        CRUD::column('customer');
        CRUD::column('amount');

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
        CRUD::setValidation(SelloutRequest::class);

        CRUD::field('customer');
        CRUD::field('amount');
        $this->crud->addFields([
            [   // repeatable
                'name' => 'soled_phones',
                'label' => 'Phones',
                'type' => 'repeatable',
                'subfields' => [ // also works as: "fields"
                    [  // Select2
                        'label' => 'Phone',
                        'type' => 'select2',
                        'name' => 'phones', // the db column for the foreign key

                        // optional
                        'entity' => 'phones', // the method that defines the relationship in your Model
                        'model' => "App\Models\Phone", // foreign key model
                        'attribute' => 'phone_info', // foreign key attribute that is shown to user

                        'wrapper' => ['class' => 'form-group col-md-12'],
                        // also optional
                        //                        'options'   => (function ($query) {
                        //                            return $query->orderBy('name', 'ASC')->where('depth', 1)->get();
                        //                        }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
                    ],
                    [
                        'name' => 'imei_1',
                        'type' => 'text',
                        'label' => 'IMEI',
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                ],
                // optional
                'new_item_label' => 'Add Phone', // customize the text of the button
                'init_rows' => '1',
            ]
        ]);

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
