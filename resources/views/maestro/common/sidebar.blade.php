<!-- Brand Logo -->
  <a href="{{ route('dashboard.index') }}" class="h1"><img src="{{config('site-settings.maestro_cdn_url').'public/front/img/logoNew.png'}}" style="padding-left: 38px;!important"> </img> </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{config('site-settings.maestro_cdn_url').'public/maestro/dist/img/user2-160x160.jpg'}}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="{{ route('dashboard.index') }}" class="d-block">{{ Auth::user()->name ?? Ucfirst(Auth::user()->first_name).' '.Ucfirst(Auth::user()->last_name) }}</a>
      </div>
    </div>

    <!-- SidebarSearch Form -->
    <div class="form-inline">
      <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="fas fa-search fa-fw"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item menu-open">
          <a href="#" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('dashboard.index') }}" class="nav-link active">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Master
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('role.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                  <p>Role And Permission</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('category.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                  <p>Category</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/charts/chartjs.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Skill</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/charts/flot.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Tag</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/charts/inline.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Category</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/charts/uplot.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Type</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('sponsors.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Sponsor Management</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('social-links.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Social Link</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-tree"></i>
            <p>
              Component
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('users.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Users</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('organization.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Organization</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('lab.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Lab</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/buttons.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Challenge</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/sliders.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Resource Module</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/modals.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Resource Collection</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/UI/navbar.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Resource Group</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-tree"></i>
            <p>
              Skills Management
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('skills.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Skills</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('skillstack.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Skill Stacks</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('skillgroup.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Skill Groups</p>
              </a>
            </li>
</ul>
<li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>
              Project Management
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview" style="display: none;">
            <li class="nav-item">
              <a href="{{ route('projects.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Project</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-stage.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Stage</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-vertical.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Vertical</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-type.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Type</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-industry.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Industry</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-status.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Status</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Pitch Templates</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i class="nav-icon fas fa-sign-out-alt"></i> {{ __('Logout') }}</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>