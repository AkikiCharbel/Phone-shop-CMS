<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SelloutRequest;
use App\Models\Phone;
use App\Models\Sellout;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
//TODO:: change it in production but for the time being to prevent the red notice
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\BulkDeleteOperation;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Illuminate\Http\RedirectResponse;

class SelloutCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation { store as traitStore; }
    use UpdateOperation { update as traitUpdate; }
    use DeleteOperation;
    use ShowOperation;
    use FetchOperation;
    use BulkDeleteOperation;

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
        $this->crud->setCreateContentClass('col-md-12 bold-labels');

        $this->crud->addFields([
            [
                'type' => 'relationship',
                'name' => 'customer_id', // the method on your model that defines the relationship
                'ajax' => true,
                'minimum_input_length' => 0,
                'inline_create' => [ // specify the entity in singular
                    'data_source' => backpack_url('monster/fetch/contact-number'),
                    'entity' => 'User', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                    'modal_route' => route('customer-inline-create'), // InlineCreate::getInlineCreateModal()
                    'create_route' => route('customer-inline-create-save'), // InlineCreate::storeInlineCreate()
                ],
            ],
            [   // repeatable
                'name' => 'soled_phones',
                'label' => 'Phones',
                'type' => 'repeatable',
                'subfields' => [
                    [
                        'label' => 'Phone',
                        'type' => 'select2_from_ajax',
                        'name' => 'phone_id',
                        'entity' => 'availablePhones',
                        'attribute' => 'phone_info',
                        'data_source' => url('api/admin/available-phones'), // url to controller search function (with /{id} should return model)

                        'placeholder' => 'Select a phone',
                        'minimum_input_length' => 0,
                        'model' => "App\Models\Phone",
                        // 'dependencies'            => ['category'], // when a dependency changes, this select2 is reset to null
                        'method' => 'POST',
                        'include_all_form_fields' => true, // optional - only send the current field through AJAX (for a smaller payload if you're not using multiple chained select2s)
                    ],
                    [
                        'name' => 'price_sold',
                        'type' => 'number',
                        'label' => 'Price Sold',
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'prefix' => '$',
                    ],
                    [
                        'name' => 'soled_phone_id',
                        'type' => 'hidden',
                    ],
                ],

                'new_item_label' => 'Add Phone',
                'init_rows' => '1',
            ],
            [
                'name' => 'amount',
                'type' => 'number',
                'label' => 'Amount',
            ],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
        $this->crud->setUpdateContentClass('col-md-12 bold-labels');
    }

    protected function fetchCustomer()
    {
        return $this->fetch([
            'model' => User::class,
            'query' => function ($model) {
                $model = $model->whereHas('roles', function ($q) {
                    $q->where('name', 'customer');
                });
                $search = request()->input('q') ?? false;
                if ($search) {
                    return $model->whereRaw('name LIKE "%'.$search.'%"');
                } else {
                    return $model;
                }
            },
        ]);
    }

    public function store(): RedirectResponse
    {
        $response = $this->traitStore();
        foreach (request()->get('soled_phones') as $phoneObj) {
            $phone = Phone::find($phoneObj['phone_id']);
            $phone->item_sellout_price = $phoneObj['price_sold'];
            $phone->save();
            $this->crud->entry->phones()->attach($phone->id);
        }

        return $response;
    }

    public function update()
    {
        $response = $this->traitUpdate();
        $phoneIds = [];
        foreach (request()->get('soled_phones') as $phoneObj) {
            $phone = Phone::find($phoneObj['phone_id']);
            $phone->item_sellout_price = $phoneObj['price_sold'];
            $phone->save();
            $phoneIds[] = $phone->id;
            $this->crud->entry->phones()->syncWithoutDetaching($phone->id);
        }

        $diff = $this->crud->entry->phones->pluck('id')->diff(collect($phoneIds));
        foreach ($diff as $removedPhoneId){
            $phone = Phone::find($removedPhoneId);
            $phone->item_sellout_price = null;
            $phone->save();
            $this->crud->entry->phones()->detach($removedPhoneId);
        }

        return $response;
    }
}
