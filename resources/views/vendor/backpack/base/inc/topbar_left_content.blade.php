{{-- This file is used to store topbar (left) items --}}

{{-- <li class="nav-item px-3"><a class="nav-link" href="#">Dashboard</a></li>
<li class="nav-item px-3"><a class="nav-link" href="#">Users</a></li>
<li class="nav-item px-3"><a class="nav-link" href="#">Settings</a></li> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>


@if(isset($crud->entity_name))
    @if($crud->entity_name == 'sellout' and !in_array($crud->getCurrentOperation(), ['list', 'show']))
        <script src="{{ asset('js/sellout.js') }}"></script>
    @endif
@endif
