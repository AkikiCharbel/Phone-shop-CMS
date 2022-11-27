<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BrandModelRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\BulkDeleteOperation;

/**
 * Class BrandModelCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BrandModelCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use BulkDeleteOperation;
    use \Backpack\EditableColumns\Http\Controllers\Operations\MinorUpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\BrandModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/brand-model');
        CRUD::setEntityNameStrings('brand model', 'brand models');
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
//        CRUD::column('brand')->type('editable_select');
//        CRUD::column('name')->type('editable_text');
        CRUD::addColumn([
            'name' => 'brand_id',
            'label' => 'Brand',
            'type' => 'editable_select',
            'options' => \App\Models\Brand::all()->pluck('name', 'id')->toArray(),

            // Optionals
            'underlined' => true, // show a dotted line under the editable column for differentiation? default: true
            'save_on_focusout' => true, // if user clicks out, the value should be saved (instead of greyed out)
            'save_on_change' => true,
            'on_error' => [
                'text_color' => '#df4759', // set a custom text color instead of the red
                'text_color_duration' => 0, // how long (in miliseconds) should the text stay that color (0 for infinite, aka until page refresh)
                'text_value_undo' => false, // set text to the original value (user will lose the value that was recently input)
            ],
            'on_success' => [
                'text_color' => '#42ba96', // set a custom text color instead of the green
                'text_color_duration' => 3000, // how long (in miliseconds) should the text stay that color (0 for infinite, aka until page refresh)
            ],
            'auto_update_row' => true, // update related columns in same row, after the AJAX call?
        ]);

        CRUD::addColumn([
            'name' => 'name',
            'type' => 'editable_text',
            'label' => 'Name',

            // Optionals
            'underlined' => true, // show a dotted line under the editable column for differentiation? default: true
            'min_width' => '120px', // how wide should the column be?
            'select_on_click' => false, // select the entire text on click? default: false
            'save_on_focusout' => false, // if user clicks out, the value should be saved (instead of greyed out)
            'on_error' => [
                'text_color' => '#df4759', // set a custom text color instead of the red
                'text_color_duration' => 0, // how long (in miliseconds) should the text stay that color (0 for infinite, aka until page refresh)
                'text_value_undo' => false, // set text to the original value (user will lose the value that was recently input)
            ],
            'on_success' => [
                'text_color' => '#42ba96', // set a custom text color instead of the green
                'text_color_duration' => 3000, // how long (in miliseconds) should the text stay that color (0 for infinite, aka until page refresh)
            ],
            'auto_update_row' => true, // update related columns in same row, after the AJAX call?
        ]);
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
        CRUD::setValidation(BrandModelRequest::class);

        CRUD::field('brand_id');
        CRUD::field('name');

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
