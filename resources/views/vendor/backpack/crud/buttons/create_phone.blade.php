@if ($crud->hasAccess('create'))
	<a href="{{ route('phone.create') }}" class="btn btn-primary" data-style="zoom-in"><span class="ladda-label"><i class="la la-plus"></i> Add Phone</span></a>
@endif
