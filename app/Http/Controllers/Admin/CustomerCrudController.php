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
use Illuminate\Http\RedirectResponse;

class CustomerCrudController extends CrudController
{
    use ListOperation;
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
        CRUD::column('name');
        CRUD::column('phone_number');
        CRUD::column('email');
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
}
