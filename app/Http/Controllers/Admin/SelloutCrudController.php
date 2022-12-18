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
use Backpack\CRUD\app\Library\Widget;
use Backpack\Pro\Http\Controllers\Operations\BulkDeleteOperation;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SelloutCrudController extends CrudController
{
    use ListOperation { index as traitIndex; }
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
        $this->crud->setListView('vendor.backpack.crud.customized-list');
        CRUD::setEntityNameStrings('sellout', 'sellouts');

        if (! backpack_user()->can('sellout.view')) {
            CRUD::denyAccess(['show']);
        }
        if (! backpack_user()->can('sellout.create')) {
            CRUD::denyAccess(['create']);
        }
        if (! backpack_user()->can('sellout.list')) {
            CRUD::denyAccess(['list']);
        }
        if (! backpack_user()->can('sellout.update')) {
            CRUD::denyAccess(['update']);
        }
        if (! backpack_user()->can('sellout.delete')) {
            CRUD::denyAccess(['delete']);
        }
    }

    protected function setupListOperation(): void
    {
        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'from_to',
            'label' => 'Date range',
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'created_at', '>=', Carbon::parse($dates->from)->toDateString());
                $this->crud->addClause('where', 'created_at', '<=', Carbon::parse($dates->to)->toDateString());
            });
        CRUD::column('customer');
        CRUD::column('amount');
    }

    protected function setupShowOperation(): void
    {
        $this->crud->setShowContentClass('col-md-12 bold-labels');

        CRUD::column('customer');
        CRUD::column('amount');
        $this->crud->addColumn([
            'name' => 'soledPhonesShow',
            'label' => 'Soled Phones',
            'type' => 'table',
            'columns' => [
                'brand_name' => 'Brand',
                'brand_model_name' => 'Model',
                'imei_1' => 'IMEI 1',
                'imei_2' => 'IMEI 2',
                'rom_size' => 'ROM Size',
                'color' => 'Color',
                'item_sellout_price' => 'Sold Price',
                'is_new' => 'New Phone',
            ],
        ]);
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
                        'wrapper' => ['class' => 'form-group col-md-9'],
                    ],
                    [
                        'name' => 'price_sold',
                        'type' => 'number',
                        'label' => 'Price Sold',
                        'wrapper' => [
                            'class' => 'form-group col-md-3',
                        ],
                        'attributes' => [
                            'onchange' => 'getTotalPrice()',
                        ],
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
                'wrapper' => ['class' => 'form-group col-md-6'],
                'prefix' => '$',
            ],
            [
                'name' => 'selloutPayments',
                'label' => 'sellout Payments',
                'type' => 'relationship',
                'wrapper' => ['class' => 'form-group col-md-6'],
                'subfields' => [
                    [
                        'name' => 'amount',
                        'label' => 'amount',
                        'type' => 'number',
                        'prefix' => '$',
                        'attributes' => [
                            'onchange' => 'getMoneyLeft()',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'amount_left',
                'label' => 'Money Left',
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                    'step' => 'any',
                ],
                'attributes' => [
                    'disabled' => 'disabled',
                    'step' => 'any',
                ],
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
        foreach ($diff as $removedPhoneId) {
            $phone = Phone::find($removedPhoneId);
            $phone->item_sellout_price = null;
            $phone->save();
            $this->crud->entry->phones()->detach($removedPhoneId);
        }

        return $response;
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $id = $this->crud->getCurrentEntryId() ?? $id;

        $sellout = Sellout::find($id);
        $sellout->phones()->update(['item_sellout_price' => null]);
        $sellout->phones()->detach();

        return $this->crud->delete($id);
    }

    public function index()
    {
        /** @var View $response */
        $response = $this->traitIndex();

        /** @var Builder $query */
        $query = $response->getData()['crud']->query;
        $sellouts = $query->get();
        $phonesQuery = Phone::whereIn('id', $sellouts->pluck('id'));

        $totalSoledPhones = $phonesQuery->count();
        $totalMoneyProfit = $phonesQuery->sum('item_sellout_price') - $phonesQuery->sum('item_cost');

        $this->getWidgets($totalMoneyProfit, $totalSoledPhones);

        return $response;
    }

    public function getWidgets($moneyProfit, $totalSoledPhones)
    {
        $widgets = [];
        $widgets[] = [
            'type' => 'progress',
            'class' => 'card text-white text-center bg-success mb-2',
            'value' => number_format($totalSoledPhones),
            'description' => 'Total Soled Phones',
            'hint' => 'Phones newly sold',
            'wrapper' => ['class' => 'col-md-4'],
        ];

        if (backpack_user()->can('purchase.view') || backpack_user()->can('purchase.list')) {
            $widgets[] = [
                'type' => 'progress',
                'class' => 'card text-white text-center bg-danger mb-2',
                'value' => number_format($moneyProfit).' $',
                'description' => 'Money profit',
                'hint' => 'Money profit from the sellouts',
                'wrapper' => ['class' => 'col-md-4'],
            ];
        }

        Widget::add([
            'type' => 'div',
            'class' => 'row',
            'content' => $widgets,
        ]);
    }
}
