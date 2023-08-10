<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('dashboard') }}" class="sidebar__main-logo"><img src="" alt=""></a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item ">
                    <a href="{{ route('dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item ">
                    <a href="{{ route('dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">Add Documents</span>
                    </a>
                </li>

                <li class="sidebar-menu-item ">
                    <a href="{{ route('dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">Document List</span>
                    </a>
                </li>

                <li class="sidebar-menu-item ">
                    <a href="{{ route('ranking.docList') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">User Doccuments</span>
                    </a>
                </li>


            </ul>
            <div class="text-center mb-3 text-uppercase">
                <span class="text--primary">Developed By Sazzad</span>
            </div>
        </div>
    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush
