<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SelloutRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
//TODO:: change it in production but for the time being to prevent the red notice
use \Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SelloutCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;
    use FetchOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\Sellout::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sellout');
        CRUD::setEntityNameStrings('sellout', 'sellouts');
    }

    protected function setupListOperation(): void
    {
        CRUD::column('customer');
        CRUD::column('amount');
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SelloutRequest::class);

        $this->crud->addFields([
            [
                'type' => "relationship",
                'name' => 'customer', // the method on your model that defines the relationship
                'ajax' => true,
                'minimum_input_length'    => 0,
                'inline_create' => [ // specify the entity in singular
                    'data_source' => backpack_url('monster/fetch/contact-number'),
                    'entity' => 'User', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                    'modal_route' => route('customer-inline-create'), // InlineCreate::getInlineCreateModal()
                    'create_route' =>  route('customer-inline-create-save'), // InlineCreate::storeInlineCreate()
                ]
            ],
            [
                'name' => 'amount',
                'type' => 'number',
                'label' => 'Amount',
            ],
            [   // repeatable
                'name' => 'soled_phones',
                'label' => 'Phones',
                'type' => 'repeatable',
                'subfields' => [
                    [
                        'label' => 'Phone',
                        'type' => 'select2',
                        'name' => 'phones', // the db column for the foreign key

                        'entity' => 'phones', // the method that defines the relationship in your Model
                        'model' => "App\Models\Phone", // foreign key model
                        'attribute' => 'phone_info', // foreign key attribute that is shown to user

                        'wrapper' => ['class' => 'form-group col-md-12'],

                        // 'options'   => (function ($query) {
                        //      return $query->orderBy('name', 'ASC')->where('depth', 1)->get();
                        // }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
                    ],
                    [
                        'name' => 'imei_1',
                        'type' => 'text',
                        'label' => 'IMEI',
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                ],

                'new_item_label' => 'Add Phone',
                'init_rows' => '1',
            ],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }


    protected function fetchCustomer()
    {
        return $this->fetch([
            'model' => User::class,
            'query' => function($model) {
                $model = $model->whereHas('roles', function ($q){
                    $q->where('name', 'customer');
                });
                $search = request()->input('q') ?? false;
                if ($search) {
                    return $model->whereRaw('name LIKE "%' . $search . '%"');
                }else{
                    return $model;
                }
            },
        ]);
    }
}
