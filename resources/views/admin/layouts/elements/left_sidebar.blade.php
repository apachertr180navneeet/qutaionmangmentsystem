<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
	<div class="app-brand demo">
		<a href="{{route('admin.dashboard')}}" class="app-brand-link">
			<span class="app-brand-text demo menu-text fw-bold ms-2">Admin</span>
		</a>

		<a href="javascript:void(0);"
			class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
			<i class="bx bx-chevron-left bx-sm align-middle"></i>
		</a>
	</div>

	<div class="menu-inner-shadow"></div>

	<ul class="menu-inner py-1">
		<li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : ''}}">
			<a href="{{route('admin.dashboard')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-home-circle"></i>
				<div data-i18n="Dashboard">Dashboard</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/quotations*') ? 'active' : ''}}">
			<a href="{{route('admin.quotations.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-file"></i>
				<div data-i18n="Quotations">Quotations</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/customers*') ? 'active' : ''}}">
			<a href="{{route('admin.customers.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-group"></i>
				<div data-i18n="Customers">Customers</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/items*') ? 'active' : ''}}">
			<a href="{{route('admin.items.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-box"></i>
				<div data-i18n="Items">Items</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/follow-ups*') ? 'active' : ''}}">
			<a href="{{route('admin.followups.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-calendar-check"></i>
				<div data-i18n="Follow-ups">Follow-ups</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/reports*') ? 'active' : ''}}">
			<a href="{{route('admin.reports.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
				<div data-i18n="Reports">Reports</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/settings*') ? 'active' : ''}}">
			<a href="{{route('admin.settings.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-cog"></i>
				<div data-i18n="Company Settings">Company Settings</div>
			</a>
		</li>



	</ul>
</aside>
