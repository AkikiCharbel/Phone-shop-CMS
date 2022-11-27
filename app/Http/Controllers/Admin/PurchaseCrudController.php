<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PurchaseRequest;
use App\Models\Phone;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PurchaseCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PurchaseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Purchase::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/purchase');
        CRUD::setEntityNameStrings('purchase', 'purchases');
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
        CRUD::column('date');
        CRUD::column('shipping_source');
        CRUD::column('shipping_date');

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
        $this->crud->setCreateContentClass('col-md-12 bold-labels');
        CRUD::setValidation(PurchaseRequest::class);

        CRUD::field('shipping_source')->wrapper(['class' => 'form-group col-md-4']);
        CRUD::field('shipping_date')->wrapper(['class' => 'form-group col-md-4']);
        CRUD::field('date')->wrapper(['class' => 'form-group col-md-4'])->label('Purchase Date');
        $this->crud->addFields([
            [   // repeatable
                'name' => 'phone_list',
                'label' => 'Phones',
                'type' => 'repeatable',
                'subfields' => [ // also works as: "fields"
                    [  // Select2
                        'label' => 'Brand - model',
                        'type' => 'select2',
                        'name' => 'brand_model_id', // the db column for the foreign key

                        // optional
                        'entity' => 'phones.brandModel', // the method that defines the relationship in your Model
                        'model' => "App\Models\BrandModel", // foreign key model
                        'attribute' => 'full_name', // foreign key attribute that is shown to user

                        'wrapper' => ['class' => 'form-group col-md-4'],
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
                    [
                        'name' => 'imei_2',
                        'type' => 'text',
                        'label' => 'IMEI 2',
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name' => 'item_cost',
                        'type' => 'number',
                        'label' => 'Item Cost',
                        'wrapper' => [
                            'class' => 'form-group col-md-2',
                            'min' => 0,
                        ],
                        'prefix' => '$',
                    ],
                    [
                        'name' => 'item_sellout_price',
                        'type' => 'number',
                        'label' => 'Item Sellout Price',
                        'prefix' => '$',
                        'wrapper' => ['class' => 'form-group col-md-2'],
                    ],
                    [
                        'name' => 'rom_size',
                        'type' => 'number',
                        'label' => 'ROM size',
                        'prefix' => 'GB',
                        'wrapper' => [
                            'class' => 'form-group col-md-2',
                            'min' => 0,
                        ],
                    ],
                    [
                        'name' => 'is_new',
                        'type' => 'switch',
                        'color' => 'primary',
                        'onLabel' => '✓',
                        'offLabel' => '✕',
                        'default' => true,
                        'wrapper' => [
                            'class' => 'form-group col-md-1 align-self-center mt-4',
                        ],
                    ],
                    [
                        'name' => 'color',
                        'type' => 'text',
                        'label' => 'Color',
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name' => 'description',
                        'type' => 'textarea',
                        'label' => 'Description',
                    ],
                    [
                        'name' => 'id',
                        'type' => 'hidden',
                    ],
                ],

                // optional
                'new_item_label' => 'Add Phone', // customize the text of the button
                'init_rows' => '1',
            ],
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
        $this->crud->setUpdateContentClass('col-md-12 bold-labels');
        $this->setupCreateOperation();
    }

    public function store()
    {
        $response = $this->traitStore();

        $this->crud->entry->phones()->createMany(request()->get('phone_list'));

        return $response;
    }

    public function update()
    {
        $response = $this->traitUpdate();

        $purchaseId = $this->crud->entry->id;
        $stayingIds = [];

        foreach (request()->get('phone_list') as $phone) {
            $phone['purchase_id'] = $purchaseId;
            $createdPhone = Phone::updateOrCreate(['id' => $phone['id']], $phone);
            $stayingIds[] = $createdPhone->id;
        }

        $this->crud->entry->phones()->whereNotIn('id', $stayingIds)->delete();

        return $response;
    }
}
