    <!--begin::Aside-->
<div class="aside aside-left  aside-fixed  d-flex flex-column flex-row-auto"  id="kt_aside">
	<!--begin::Brand-->
	<div class="brand flex-column-auto " id="kt_brand">
		<!--begin::Logo-->
		<a href="{{route('admin.home')}}" class="brand-logo">
			<img alt="Logo" class="w-140px" src="{{asset("/")}}cp_assets/logos/Romano-TopHeader-Logo.png"/>
		</a>
		<!--end::Logo-->
	</div>
	<!--end::Brand-->

	<!--begin::Aside Menu-->
	<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
		<!--begin::Menu Container-->
		<div
			id="kt_aside_menu"
			class="aside-menu my-4 "
			data-menu-vertical="1"
			 data-menu-scroll="1" data-menu-dropdown-timeout="500" 			>
			<!--begin::Menu Nav-->
			<ul class="menu-nav ">
				<li class="menu-item  {{Route::is('admin.home') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.home')}}" class="menu-link ">
						<i class="menu-icon flaticon2-architecture-and-city"></i>
						<span class="menu-text">Home</span>
					</a>
				</li>
				<li class="menu-item  {{Route::is('admin.users.*') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.users.index')}}" class="menu-link ">
						<i class="menu-icon flaticon-users"></i>
						<span class="menu-text">Users</span>
					</a>
				</li>
				<li class="menu-item  {{Route::is('admin.categories.*') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.categories.index')}}" class="menu-link ">
						<i class="menu-icon flaticon2-layers-1"></i>
						<span class="menu-text">Categories</span>
					</a>
				</li>
				<li class="menu-item  {{Route::is('admin.products.*') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.products.index')}}" class="menu-link ">
						<i class="menu-icon flaticon2-cube"></i>
						<span class="menu-text">Products</span>
					</a>
				</li>
				<li class="menu-item  {{Route::is('admin.posts.*') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.posts.index')}}" class="menu-link ">
						<i class="menu-icon flaticon2-writing"></i>
						<span class="menu-text">Posts</span>
					</a>
				</li>
				
				<li class="menu-item  {{Route::is('admin.orders.*') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.orders.index')}}" class="menu-link ">
						<i class="menu-icon flaticon2-shopping-cart"></i>
						<span class="menu-text">Orders</span>
					</a>
				</li>
				<li class="menu-item  {{Route::is('admin.discount-codes.*') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.discount-codes.index')}}" class="menu-link ">
						<i class="menu-icon flaticon2-tag"></i>
						<span class="menu-text">Discount Codes</span>
					</a>
				</li>
				<li class="menu-item  {{Route::is('admin.contact.*') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.contact.index')}}" class="menu-link ">
						<i class="menu-icon flaticon2-email"></i>
						<span class="menu-text">Contacts</span>
					</a>
				</li>
				
				<li class="menu-item {{Route::is('admin.slider-products.*') || Route::is('admin.featured-products.*') ? 'menu-item-active menu-item-open' : ''}}  menu-item-submenu" aria-haspopup="true"  data-menu-toggle="hover">
					<a  href="javascript:;" class="menu-link menu-toggle">
						<i class="menu-icon flaticon2-architecture-and-city"></i>
						<span class="menu-text">Home Page</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu ">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
						
						<li class="menu-item {{Route::is('admin.slider-products.*') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
							<a  href="{{route('admin.slider-products.index', ['type' => 'fall_winter'])}}" class="menu-link ">
								<i class="menu-bullet menu-bullet-line">
									<span></span>
								</i>
								<span class="menu-text">Slider Products</span>
							</a>
						</li>
						<li class="menu-item {{Route::is('admin.featured-products.*') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
							<a  href="{{route('admin.featured-products.index', ['section' => 'A'])}}" class="menu-link ">
								<i class="menu-bullet menu-bullet-line">
									<span></span>
								</i>
								<span class="menu-text">Featured Products</span>
							</a>
						</li>
					</ul>
				</div>
				</li>
				<li class="menu-item  {{Route::is('admin.setting') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.setting')}}" class="menu-link ">
						<i class="menu-icon flaticon2-settings"></i>
						<span class="menu-text">Settings</span>
					</a>
				</li>
				<li class="menu-item  {{Route::is('admin.content-settings.index') ? 'menu-item-active' : ''}}" aria-haspopup="true" >
					<a  href="{{route('admin.content-settings.index')}}" class="menu-link ">
						<i class="menu-icon flaticon2-settings"></i>
						<span class="menu-text">Content settings of site</span>
					</a>
				</li>
				
				<li class="menu-item text-danger" aria-haspopup="true"  onclick="logout()">
					<a  href="{{route('admin.logout')}}" class="menu-link ">
						<i class="menu-icon text-danger fas fa-power-off"></i>
						<span class="menu-text">Logout</span>
					</a>
				</li>
				<li class="menu-item d-none">
                        <form id="logoutForm" action="{{route('admin.logout')}}" method="post" style="margin-left: 25px" class="mt-3">
                            @csrf
                        </form>
                </li>
			</ul>
		</div>
	</div>
</div>		
						<!--end::Menu Nav-->
</div>
		<!--end::Menu Container-->
	</div>
	<!--end::Aside Menu-->
</div>
<!--end::Aside-->

@push('scripts')
<script>
	function logout(){
		event.preventDefault();
		document.getElementById('logoutForm').submit();
	}
</script>
@endpush