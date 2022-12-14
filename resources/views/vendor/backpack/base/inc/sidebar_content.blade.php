{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@if(backpack_user()->hasPermissionTo('user-management.list'))
    <!-- Users, Roles, Permissions -->
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
        </ul>
    </li>
@endif


@if(backpack_user()->hasPermissionTo('sellout.view'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('sellout') }}"><i class="nav-icon las la-shopping-basket"></i> Sellouts</a></li>
@endif
@if(backpack_user()->hasPermissionTo('customer.view'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('customer') }}"><i class="nav-icon las la-user-tie"></i> Customers</a></li>
@endif
@if(backpack_user()->hasPermissionTo('phone.view'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('phone') }}"><i class="nav-icon las la-mobile"></i> Phones</a></li>
@endif

@if(backpack_user()->hasPermissionTo('purchase.view'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('purchase') }}"><i class="nav-icon las la-store-alt"></i> Purchases</a></li>
@endif
@if(backpack_user()->hasPermissionTo('brand.view'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('brand') }}"><<i class="nav-icon las la-copyright"></i> Brands</a></li>
@endif
@if(backpack_user()->hasPermissionTo('brand-model.view'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('brand-model') }}"><i class="nav-icon las la-stream"></i> Brand models</a></li>
@endif
@if(backpack_user()->hasPermissionTo('customer-dept.view'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('customer-debt') }}"><i class="nav-icon las la-hand-holding-usd"></i> Customer debts</a></li>
@endif
