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

		<li class="menu-item {{ request()->is('admin/quotations*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-file"></i>
				<div data-i18n="Quotations">Quotations</div>
			</a>
			<ul class="menu-sub">
				<li class="menu-item {{ request()->is('admin/quotations') ? 'active' : ''}}">
					<a href="{{route('admin.quotations.index')}}" class="menu-link">
						<div data-i18n="All Quotations">All Quotations</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/quotations/create') ? 'active' : ''}}">
					<a href="{{route('admin.quotations.create')}}" class="menu-link">
						<div data-i18n="Create Quotation">Create Quotation</div>
					</a>
				</li>
			</ul>
		</li>

		<li class="menu-item {{ request()->is('admin/customers*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-group"></i>
				<div data-i18n="Customers">Customers</div>
			</a>
			<ul class="menu-sub">
				<li class="menu-item {{ request()->is('admin/customers') ? 'active' : ''}}">
					<a href="{{route('admin.customers.index')}}" class="menu-link">
						<div data-i18n="All Customers">All Customers</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/customers/create') ? 'active' : ''}}">
					<a href="{{route('admin.customers.create')}}" class="menu-link">
						<div data-i18n="Add Customer">Add Customer</div>
					</a>
				</li>
			</ul>
		</li>

		<li class="menu-item {{ request()->is('admin/items*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-box"></i>
				<div data-i18n="Items">Items</div>
			</a>
			<ul class="menu-sub">
				<li class="menu-item {{ request()->is('admin/items') ? 'active' : ''}}">
					<a href="{{route('admin.items.index')}}" class="menu-link">
						<div data-i18n="All Items">All Items</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/items/create') ? 'active' : ''}}">
					<a href="{{route('admin.items.create')}}" class="menu-link">
						<div data-i18n="Add Item">Add Item</div>
					</a>
				</li>
			</ul>
		</li>

		<li class="menu-item {{ request()->is('admin/follow-ups*') ? 'active' : ''}}">
			<a href="{{route('admin.followups.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-calendar-check"></i>
				<div data-i18n="Follow-ups">Follow-ups</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/reports*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
				<div data-i18n="Reports">Reports</div>
			</a>
			<ul class="menu-sub">
				<li class="menu-item {{ request()->is('admin/reports') ? 'active' : ''}}">
					<a href="{{route('admin.reports.index')}}" class="menu-link">
						<div data-i18n="All Reports">All Reports</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/reports/customer-wise') ? 'active' : ''}}">
					<a href="{{route('admin.reports.customer_wise')}}" class="menu-link">
						<div data-i18n="Customer Wise">Customer Wise</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/reports/date-wise') ? 'active' : ''}}">
					<a href="{{route('admin.reports.date_wise')}}" class="menu-link">
						<div data-i18n="Date Wise">Date Wise</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/reports/status-wise') ? 'active' : ''}}">
					<a href="{{route('admin.reports.status_wise')}}" class="menu-link">
						<div data-i18n="Status Wise">Status Wise</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/reports/monthly') ? 'active' : ''}}">
					<a href="{{route('admin.reports.monthly')}}" class="menu-link">
						<div data-i18n="Monthly">Monthly</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/reports/item-wise') ? 'active' : ''}}">
					<a href="{{route('admin.reports.item_wise')}}" class="menu-link">
						<div data-i18n="Item Wise">Item Wise</div>
					</a>
				</li>
			</ul>
		</li>

		<li class="menu-item {{ request()->is('admin/settings*') ? 'active' : ''}}">
			<a href="{{route('admin.settings.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-cog"></i>
				<div data-i18n="Company Settings">Company Settings</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/email-logs*') ? 'active' : ''}}">
			<a href="{{route('admin.email_logs.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-envelope"></i>
				<div data-i18n="Email Logs">Email Logs</div>
			</a>
		</li>

	</ul>
</aside>
