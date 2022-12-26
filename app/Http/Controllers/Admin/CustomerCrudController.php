<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerCrudController extends CrudController
{
    use ListOperation { index as traitIndex; }
    use CreateOperation { store as traitStore; }
    use UpdateOperation { update as traitUpdate; }
    use DeleteOperation;
    use ShowOperation;
    use InlineCreateOperation;

    public function setup(): void
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/customer');
        CRUD::setEntityNameStrings('customer', 'customers');
        $this->crud->setListView('vendor.backpack.crud.customers-list');

        if (! backpack_user()->can('customer.view')) {
            CRUD::denyAccess(['show']);
        }
        if (! backpack_user()->can('customer.create')) {
            CRUD::denyAccess(['create']);
        }
        if (! backpack_user()->can('customer.list')) {
            CRUD::denyAccess(['list']);
        }
        if (! backpack_user()->can('customer.update')) {
            CRUD::denyAccess(['update']);
        }
        if (! backpack_user()->can('customer.delete')) {
            CRUD::denyAccess(['delete']);
        }
    }

    protected function setupListOperation(): void
    {
        $this->crud->addClause('whereHas', 'roles', function ($query) {
            $query->where('name', 'customer');
        });
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
        CRUD::column('name');
        CRUD::column('phone_number');
        CRUD::column('email');
    }

    protected function setupShowOperation(): void
    {
        CRUD::column('name');
        CRUD::column('phone_number');
        CRUD::column('email');
        $this->crud->addColumns([
            [
                'name' => 'selloutsList',
                'label' => 'Sellouts',
                'type' => 'table-accept-html',
                'columns' => [
                    'amount' => 'Amount To Pay',
                    'amount_left' => 'Amount Left',
                    'link' => 'Link',
                ],
            ],
            [
                'name' => 'amountLeft',
                'label' => 'Sum Amount Left',
                'type' => 'text',
            ],
        ]);
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(CustomerRequest::class);

        CRUD::field('name');
        CRUD::field('phone_number');
        CRUD::field('email');
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }

    public function store(): RedirectResponse
    {
        $response = $this->traitStore();

        $this->crud->entry->assignRole('customer');

        return $response;
    }

    public function index()
    {
        /** @var View $response */
        $response = $this->traitIndex();

        /** @var Builder $query */
        $query = $response->getData()['crud']->query;
        $customers = $query->get();

        $this->getWidgets($query->count(), $customers->sum('amountLeft'));

        return $response;
    }

    public function getWidgets($newCustomers, $totalMoneyLeft)
    {
        $widgets = [];
        $widgets[] = [
            'type' => 'progress',
            'class' => 'card text-white text-center bg-info mb-2',
            'value' => number_format($newCustomers),
            'description' => 'Customers',
            'hint' => 'New Customers Added',
            'wrapper' => ['class' => 'col-md-4'],
        ];
        $widgets[] = [
            'type' => 'progress',
            'class' => 'card text-white text-center bg-success mb-2',
            'value' => number_format($totalMoneyLeft),
            'description' => 'Money Left',
            'hint' => 'Total money left with customers',
            'wrapper' => ['class' => 'col-md-4'],
        ];

//        if (backpack_user()->can('purchase.view') || backpack_user()->can('purchase.list')) {
//            $widgets[] = [
//                'type' => 'progress',
//                'class' => 'card text-white text-center bg-danger mb-2',
//                'value' => number_format($moneyProfit).' $',
//                'description' => 'Money profit',
//                'hint' => 'Money profit from the sellouts',
//                'wrapper' => ['class' => 'col-md-4'],
//            ];
//        }

        Widget::add()->to('before_content')->type('div')->class('row')->content($widgets);
    }
}
