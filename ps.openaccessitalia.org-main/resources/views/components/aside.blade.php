<!--begin::Aside-->
<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
    <!--begin::Brand-->
    <div class="brand flex-column-auto" id="kt_brand">
        <!--begin::Logo-->
        <a href="/" class="brand-logo">
            <img alt="Logo" src="/img/brand_w.png" class="h-30px" />
        </a>
        <!--end::Logo-->
        <!--begin::Toggle-->
        <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
            <span class="svg-icon svg-icon svg-icon-xl">
                <!--begin::Svg Icon | path:assets/media/svg/icons/Text/Toggle-Right.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M22 11.5C22 12.3284 21.3284 13 20.5 13H3.5C2.6716 13 2 12.3284 2 11.5C2 10.6716 2.6716 10 3.5 10H20.5C21.3284 10 22 10.6716 22 11.5Z" fill="black" />
                        <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M14.5 20C15.3284 20 16 19.3284 16 18.5C16 17.6716 15.3284 17 14.5 17H3.5C2.6716 17 2 17.6716 2 18.5C2 19.3284 2.6716 20 3.5 20H14.5ZM8.5 6C9.3284 6 10 5.32843 10 4.5C10 3.67157 9.3284 3 8.5 3H3.5C2.6716 3 2 3.67157 2 4.5C2 5.32843 2.6716 6 3.5 6H8.5Z" fill="black" />
                    </g>
                </svg>
                <!--end::Svg Icon-->
            </span>
        </button>
        <!--end::Toolbar-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside Menu-->
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
        <!--begin::Menu Container-->
        <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
            <!--begin::Menu Nav-->
            <ul class="menu-nav">
                <li class="menu-item" aria-haspopup="true">
                    <a href="/" class="menu-link">
                        <i class="menu-icon text-light flaticon-home"></i>
                        <span class="menu-text text-light">Dashboard</span>
                    </a>
                </li>
                @if(\Auth::user()->piracy && env("PIRACY_SHIELD_ENABLED") == "1")
                <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <i class="menu-icon text-light fas fa-shield-alt"></i>
                        <span class="menu-text text-light">Piracy Shield</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu ">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">Piracy Shield</span>
                                </span>
                            </li>
                            <li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <i class="menu-icon text-light fas fa-list"></i>
                                    <span class="menu-text">Lists</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu " kt-hidden-height="160" style="">
                                    <i class="menu-arrow"></i>
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/piracy/lists/tickets" class="menu-link">
                                                <i class="menu-icon text-light fas fa-ticket-alt"></i>
                                                <span class="menu-text">Tickets</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/piracy/lists/fqdn" class="menu-link">
                                                <i class="menu-icon text-light fas fa-link"></i>
                                                <span class="menu-text">FQDN</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/piracy/lists/ipv4" class="menu-link">
                                                <i class="menu-icon text-light fas fa-hashtag"></i>
                                                <span class="menu-text">IPv4</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/piracy/lists/ipv6" class="menu-link">
                                                <i class="menu-icon text-light fas fa-hashtag"></i>
                                                <span class="menu-text">IPv6</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item" aria-haspopup="true">
                                <a href="/piracy/whitelist" class="menu-link">
                                    <i class="menu-icon text-light fas fa-thumbs-up"></i>
                                    <span class="menu-text">Whitelist</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if(\Auth::user()->cncpo && env("CNCPO_ENABLED") == "1")
                <li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <i class="menu-icon text-light fas fa-child"></i>
                        <span class="menu-text text-light">CNCPO</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu " kt-hidden-height="80" style="">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item" aria-haspopup="true">
                                <a href="/cncpo/files" class="menu-link">
                                    <i class="menu-icon text-light fas fa-file"></i>
                                    <span class="menu-text">Files</span>
                                </a>
                            </li>
                            <li class="menu-item" aria-haspopup="true">
                                <a href="/cncpo/blacklist" class="menu-link">
                                    <i class="menu-icon text-light fas fa-fire"></i>
                                    <span class="menu-text">Blacklist</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if(\Auth::user()->adm && env("ADM_ENABLED") == "1")
                <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <i class="menu-icon text-light fas fa-user-secret"></i>
                        <span class="menu-text text-light">ADM</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu ">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">ADM</span>
                                </span>
                            </li>
                            <li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <i class="menu-icon text-light fas fa-dice"></i>
                                    <span class="menu-text">Betting</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu " kt-hidden-height="80" style="">
                                    <i class="menu-arrow"></i>
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/adm/betting/files" class="menu-link">
                                                <i class="menu-icon text-light fas fa-file"></i>
                                                <span class="menu-text">Files</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/adm/betting/blacklist" class="menu-link">
                                                <i class="menu-icon text-light fas fa-fire"></i>
                                                <span class="menu-text">Blacklist</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <i class="menu-icon text-light fas fa-smoking"></i>
                                    <span class="menu-text">Smoking</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu " kt-hidden-height="80" style="">
                                    <i class="menu-arrow"></i>
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/adm/smoking/files" class="menu-link">
                                                <i class="menu-icon text-light fas fa-file"></i>
                                                <span class="menu-text">Files</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/adm/smoking/blacklist" class="menu-link">
                                                <i class="menu-icon text-light fas fa-fire"></i>
                                                <span class="menu-text">Blacklist</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if(\Auth::user()->manual && env("MANUAL_ENABLED") == "1")
                <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <i class="menu-icon text-light fas fa-hand-scissors"></i>
                        <span class="menu-text text-light">Manual</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu ">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">Manual</span>
                                </span>
                            </li>
                            <li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <i class="menu-icon text-light fas fa-list"></i>
                                    <span class="menu-text">Lists</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu " kt-hidden-height="120" style="">
                                    <i class="menu-arrow"></i>
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/manual/lists/fqdn" class="menu-link">
                                                <i class="menu-icon text-light fas fa-link"></i>
                                                <span class="menu-text">FQDN</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/manual/lists/ipv4" class="menu-link">
                                                <i class="menu-icon text-light fas fa-hashtag"></i>
                                                <span class="menu-text">IPv4</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/manual/lists/ipv6" class="menu-link">
                                                <i class="menu-icon text-light fas fa-hashtag"></i>
                                                <span class="menu-text">IPv6</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if(!\Auth::user()->admin)
                <li class="menu-item" aria-haspopup="true">
                    <a href="/profile" class="menu-link">
                        <i class="menu-icon text-light fas fa-user"></i>
                        <span class="menu-text text-light">Profile</span>
                    </a>
                </li>
                @endif
                @if(\Auth::user()->admin)
                <li class="menu-item  menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <i class="menu-icon text-light fas fa-user-cog"></i>
                        <span class="menu-text text-light">Admin</span>
                        <i class="menu-arrow text-light"></i>
                    </a>
                    <div class="menu-submenu ">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">Admin</span>
                                </span>
                            </li>
                            <li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <i class="menu-icon text-light fas fa-users"></i>
                                    <span class="menu-text">Users</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu " kt-hidden-height="80" style="">
                                    <i class="menu-arrow"></i>
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/admin/users/new" class="menu-link">
                                                <i class="menu-icon text-light fas fa-plus"></i>
                                                <span class="menu-text">New</span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="/admin/users/list" class="menu-link">
                                                <i class="menu-icon text-light fas fa-list-alt"></i>
                                                <span class="menu-text">List</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                                    <a href="javascript:;" class="menu-link menu-toggle">
                                        <i class="menu-icon text-light fas fa-cog"></i>
                                        <span class="menu-text">Settings</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="menu-submenu " kt-hidden-height="40" style="">
                                        <i class="menu-arrow"></i>
                                        <ul class="menu-subnav">
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="/admin/settings/edit" class="menu-link">
                                                    <i class="menu-icon text-light fas fa-edit"></i>
                                                    <span class="menu-text">Edit</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
                                    <a href="javascript:;" class="menu-link menu-toggle">
                                        <i class="menu-icon text-light fas fa-book"></i>
                                        <span class="menu-text">Logs</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="menu-submenu " kt-hidden-height="80" style="">
                                        <i class="menu-arrow"></i>
                                        <ul class="menu-subnav">
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="/admin/logs/actions" class="menu-link">
                                                    <i class="menu-icon text-light fas fa-bolt"></i>
                                                    <span class="menu-text">Actions</span>
                                                </a>
                                            </li>
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="/admin/logs/ps_api" class="menu-link">
                                                    <i class="menu-icon text-light fas fa-code"></i>
                                                    <span class="menu-text">PS APIs</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="menu-item" aria-haspopup="true">
                                    <a href="/admin/tests" class="menu-link">
                                        <i class="menu-icon text-light fas fa-tasks"></i>
                                        <span class="menu-text">Tests</span>
                                    </a>
                                </li>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
            </ul>
            <!--end::Menu Nav-->
        </div>
        <!--end::Menu Container-->
    </div>
    <!--end::Aside Menu-->
</div>
<!--end::Aside-->

<script>
	$(document).ready(function(){
		var href = window.location.href.replaceAll('{{ env('APP_URL', '') }}','');
        $($($($('[href="'+href+'"]').closest('li').closest('ul')).closest('li').closest('ul')).closest('li').closest('ul')).closest('li').addClass('menu-item-open');
		$($($('[href="'+href+'"]').closest('li').closest('ul')).closest('li').closest('ul')).closest('li').addClass('menu-item-open');
		$($('[href="'+href+'"]').closest('li').closest('ul')).closest('li').addClass('menu-item-open');
		$('[href="'+href+'"]').closest('li').addClass('menu-item-active');
	});
</script>