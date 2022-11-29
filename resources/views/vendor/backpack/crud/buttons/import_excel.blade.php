
{{--@if ($crud->hasAccess('import_exel'))--}}
<a href="javascript:void(0)" onclick="importExcel(this)" class="btn btn-warning"><i class="la la-arrow-down"></i> Import Excel</a>

{{--    <a href="javascript:void(0)" onclick="importExcel(this)" class="btn btn-sm btn-link" data-button-type="import">--}}
{{--        <span class="ladda-label"><i class="la la-plus"></i> Import {{ $crud->entity_name }}</span>--}}
{{--    </a>--}}
{{--@endif--}}

@push('after_scripts')
    <script>
        if (typeof importExcel != 'function') {
            $("[data-button-type=import]").unbind('click');

            function importExcel(button) {
                // ask for confirmation before deleting an item
                // e.preventDefault();
                var button = $(button);
                var route = button.attr('data-route');

                swal({
                    title: "Make sure the excel file columns match!",
                    text: "hello darkness my old friend",
                    icon: "warning",
                    content:{
                        element: "input",
                        attributes: {
                            type: "file",
                            className: 'border-0'
                        },
                    },
                    buttons: {
                        cancel: {
                            text: "Cancel",
                            value: null,
                            visible: true,
                            className: "bg-secondary",
                            closeModal: true,
                        },
                        delete: {
                            text: "Import",
                            value: true,
                            visible: true,
                            className: "bg-danger",
                        }
                    },
                }).then((value) => {
                    if (value) {
                        var ajax_calls = [];
                        var delete_route = "{{ url($crud->route) }}";

                        // submit an AJAX delete call
                        $.ajax({
                            url: delete_route,
                            type: 'POST',
                            data: { entries: crud.checkedItems },
                            success: function(result) {
                                if (Array.isArray(result)) {
                                    // Show a success notification bubble
                                    new Noty({
                                        type: "success",
                                        text: "<strong>{!! trans('backpack::crud.bulk_delete_sucess_title') !!}</strong><br>"+crud.checkedItems.length+"{!! trans('backpack::crud.bulk_delete_sucess_message') !!}"
                                    }).show();
                                } else {
                                    // if the result is an array, it means
                                    // we have notification bubbles to show
                                    if (result instanceof Object) {
                                        // trigger one or more bubble notifications
                                        Object.entries(result).forEach(function(entry, index) {
                                            var type = entry[0];
                                            entry[1].forEach(function(message, i) {
                                                new Noty({
                                                    type: type,
                                                    text: message
                                                }).show();
                                            });
                                        });
                                    } else {
                                        // Show a warning notification bubble
                                        new Noty({
                                            type: "warning",
                                            text: "<strong>{!! trans('backpack::crud.bulk_delete_error_title') !!}</strong><br>{!! trans('backpack::crud.bulk_delete_error_message') !!}"
                                        }).show();
                                    }
                                }

                                // Move to previous page in case of deleting all the items in table
                                if(crud.table.rows().count() === crud.checkedItems.length) {
                                    crud.table.page("previous");
                                }

                                crud.checkedItems = [];
                                crud.table.draw(false);
                            },
                            error: function(result) {
                                // Show an alert with the result
                                new Noty({
                                    type: "warning",
                                    text: "<strong>{!! trans('backpack::crud.bulk_delete_error_title') !!}</strong><br>{!! trans('backpack::crud.bulk_delete_error_message') !!}"
                                }).show();
                            }
                        });
                    }
                });
            }
        }
    </script>
@endpush
