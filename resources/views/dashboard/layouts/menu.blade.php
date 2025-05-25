{{--<li class=" nav-item"><a href="index.html"><i class="icon-home3"></i><span data-i18n="nav.dash.main" class="menu-title">Dashboard</span><span class="tag tag tag-primary tag-pill float-xs-right mr-2">2</span></a>--}}
{{--    <ul class="menu-content">--}}
{{--        <li><a href="index.html" data-i18n="nav.dash.main" class="menu-item">Dashboard</a>--}}
{{--        </li>--}}
{{--        <li><a href="dashboard-2.html" data-i18n="nav.dash.main" class="menu-item">Dashboard 2</a>--}}
{{--        </li>--}}
{{--    </ul>--}}
{{--</li>--}}
<li class=" nav-item"><a href="changelog.html"><i class="icon-copy"></i><span data-i18n="nav.changelog.main" class="menu-title">Dashboard</span><span class="tag tag tag-pill tag-danger float-xs-right">1.0</span></a>
</li>

@can('view_role')
<li class=" nav-item"><a href="{{ route('roles.index') }}"><i class="icon-list"></i><span data-i18n="nav.roles.main" class="menu-title">Roles</span></a>
</li>
@endcan

@can('view_permission')
<li class=" nav-item"><a href="{{ route('permissions.index') }}"><i class="icon-list"></i><span data-i18n="nav.permissions.main" class="menu-title">Permissions</span></a>
</li>
@endcan

