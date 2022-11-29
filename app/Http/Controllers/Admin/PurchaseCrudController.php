<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PurchaseRequest;
use App\Models\Phone;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\RedirectResponse;

class PurchaseCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation { store as traitStore; }
    use UpdateOperation { update as traitUpdate; }
    use DeleteOperation;
    use ShowOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\Purchase::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/purchase');
        CRUD::setEntityNameStrings('purchase', 'purchases');
        $this->crud->addButtonFromView('top', 'import_excel', 'import_excel', 'beginning');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('date');
        CRUD::column('shipping_source');
        CRUD::column('shipping_date');
    }

    protected function setupCreateOperation(): void
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
    }

    protected function setupUpdateOperation(): void
    {
        $this->crud->setUpdateContentClass('col-md-12 bold-labels');
        $this->setupCreateOperation();
    }

    public function store(): RedirectResponse
    {
        $response = $this->traitStore();

        $this->crud->entry->phones()->createMany(request()->get('phone_list'));

        return $response;
    }

    public function update(): array|RedirectResponse
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
