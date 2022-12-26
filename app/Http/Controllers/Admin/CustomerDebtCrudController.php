<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SelloutPaymentRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

class CustomerDebtCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(User::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/customer-debt');
        CRUD::setEntityNameStrings('customer debt', 'customer debts');
        $this->crud->denyAccess(['create', 'delete', 'update']);
        if (! backpack_user()->can('customer-dept.view')) {
            CRUD::denyAccess(['show']);
        }
        if (! backpack_user()->can('customer-dept.list')) {
            CRUD::denyAccess(['list']);
        }
    }

    protected function setupListOperation()
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
        CRUD::column('amountLeft');
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

    /**
     * this function is to override the list method, so I can show only the customers with debt.
     */
    public function search()
    {
        $this->crud->hasAccessOrFail('list');

        $this->crud->applyUnappliedFilters();

        $start = (int) request()->input('start');
        $length = (int) request()->input('length');
        $search = request()->input('search');

        // if a search term was present
        if ($search && $search['value'] ?? false) {
            // filter the results accordingly
            $this->crud->applySearchTerm($search['value']);
        }
        // start the results according to the datatables pagination
        if ($start) {
            $this->crud->skip($start);
        }
        // limit the number of results according to the datatables pagination
        if ($length) {
            $this->crud->take($length);
        }
        // overwrite any order set in the setup() method with the datatables order
        $this->crud->applyDatatableOrder();

        $entries = $this->crud->getEntries();

        // if show entry count is disabled we use the "simplePagination" technique to move between pages.
        if ($this->crud->getOperationSetting('showEntryCount')) {
            $totalEntryCount = (int) (request()->get('totalEntryCount') ?: $this->crud->getTotalQueryCount());
            $filteredEntryCount = $this->crud->getFilteredQueryCount() ?? $totalEntryCount;
        } else {
            $totalEntryCount = $length;
            $filteredEntryCount = $entries->count() < $length ? 0 : $length + $start + 1;
        }

        // store the totalEntryCount in CrudPanel so that multiple blade files can access it
        $this->crud->setOperationSetting('totalEntryCount', $totalEntryCount);

        foreach ($entries as $key => $entry) {
            if ($entry->amountLeft <= 0) {
                unset($entries[$key]);
            }
        }
        return $this->crud->getEntriesAsJsonForDatatables($entries, $totalEntryCount, $filteredEntryCount, $start);
    }
}
