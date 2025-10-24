<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Wrapper-->
    <div id="kt_app_sidebar_wrapper" class="app-sidebar-wrapper">
        <div class="hover-scroll-y my-5 my-lg-2 mx-4" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_header" data-kt-scroll-wrappers="#kt_app_sidebar_wrapper"
            data-kt-scroll-offset="5px">

            <!--begin::Sidebar menu-->
            <div id="kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false"
                class="app-sidebar-menu-primary menu menu-column menu-rounded menu-sub-indention menu-state-bullet-primary px-3 mb-5">

                <!--begin: Desarrollos -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('desarrollos*') ? 'active' : '' }}" href="/desarrollos">
                        <span class="menu-icon">
                            <i class="ki-outline ki-home-2 fs-2"></i>
                        </span>
                        <span class="menu-title">Desarrollos</span>
                    </a>
                </div>
                <!--end: Desarrollos -->

                <!--begin: Consulta Adara -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('consulta*') ? 'active' : '' }}" href="/consulta">
                        <span class="menu-icon">
                            <i class="ki-outline ki-search-list fs-2"></i>
                        </span>
                        <span class="menu-title">Consulta Adara</span>
                    </a>
                </div>
                <!--end: Consulta Adara -->

                <!--begin: iFrames / Reportes -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('reports*') ? 'active' : '' }}" href="/reports">
                        <span class="menu-icon">
                            <i class="ki-outline ki-element-7 fs-2"></i>
                        </span>
                        <span class="menu-title">Reportes</span>
                    </a>
                </div>
                <!--end: iFrames -->

                <!--begin: CRM Naboo -->
                <div class="menu-item menu-accordion {{ request()->is('projects*', 'phases*', 'stages*', 'lotsAdara*') ? 'show' : '' }}"
                    data-kt-menu-trigger="click">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-briefcase fs-2"></i>
                        </span>
                        <span class="menu-title">Catálogo Naboo</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">
                        <!-- Proyecto -->
                        <div class="menu-item">
                            <a class="menu-link {{ request()->is('projects*') ? 'active' : '' }}" href="/projects">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Proyectos</span>
                            </a>
                        </div>
                        <!-- Fases -->
                        <div class="menu-item">
                            <a class="menu-link {{ request()->is('phases*') ? 'active' : '' }}" href="/phases">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Fases</span>
                            </a>
                        </div>
                        <!-- Etapas -->
                        <div class="menu-item">
                            <a class="menu-link {{ request()->is('stages*') ? 'active' : '' }}" href="/stages">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Etapas</span>
                            </a>
                        </div>
                        <!-- Lotes -->
                        <div class="menu-item">
                            <a class="menu-link {{ request()->is('lotsAdara*') ? 'active' : '' }}" href="/lotsAdara">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Lotes</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!--end: CRM Naboo -->
            </div>
            <!--end::Sidebar menu-->
        </div>
    </div>
    <!--end::Wrapper-->
</div>
