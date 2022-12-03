{{--@if ($crud->hasAccess('import_exel'))--}}
<a href="javascript:void(0)" onclick="importExcel(this)" class="btn btn-warning"><i class="la la-arrow-down"></i> Import Excel</a>
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
                            id: 'excel-file',
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
                        var formData = new FormData();
                        formData.append('file', $('#excel-file')[0].files[0]);

                        var import_route = "/api/admin/import-phones";

                        // submit an AJAX delete call
                        $.ajax({
                            url: import_route,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(result) {
                                    // Show a success notification bubble
                                    new Noty({
                                        type: "success",
                                        text: "<strong>The file is imported successfully</strong><br>"
                                    }).show();
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
                                    text: "<strong>Error in uploading File</strong><br>"
                                }).show();
                            }
                        });
                    }
                });
            }
        }
    </script>
@endpush

<!-- Button trigger modal -->
