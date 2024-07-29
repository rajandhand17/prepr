<!-- Brand Logo -->
  <a href="{{ route('dashboard.index') }}" class="h1"><img src="{{config('site-settings.aws_url').'public/front/img/logoNew.png'}}" style="padding-left: 38px;!important"> </img> </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    @if (auth()->check())
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{config('site-settings.aws_url').'public/maestro/dist/img/user2-160x160.jpg'}}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
          <a href="{{ route('dashboard.index') }}" class="d-block">{{ Auth::user()->name ?? Ucfirst(Auth::user()->first_name).' '.Ucfirst(Auth::user()->last_name) }}</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item {{ request()->segment(2) == 'dashboard' ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->segment(2) == 'dashboard' ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('dashboard.index') }}" class="nav-link {{ Route::currentRouteName() == 'dashboard.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item {{ request()->segment(2) == 'role' || request()->segment(2) == 'category' || request()->segment(2) == 'sponsors' || request()->segment(2) == 'social-links' || request()->segment(2) == 'ranks' ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->segment(2) == 'role' || request()->segment(2) == 'category' || request()->segment(2) == 'sponsors' || request()->segment(2) == 'social-links' || request()->segment(2) == 'ranks' ? 'active' : '' }}">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Master
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('role.index') }}" class="nav-link {{ Route::currentRouteName() == 'role.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                  <p>Role And Permission</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('category.index') }}" class="nav-link {{ Route::currentRouteName() == 'category.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                  <p>Category</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('sponsors.index') }}" class="nav-link {{ Route::currentRouteName() == 'sponsors.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Sponsor Management</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('social-links.index') }}" class="nav-link {{ Route::currentRouteName() == 'social-links.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Social Link</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('ranks.index') }}" class="nav-link {{ Route::currentRouteName() == 'ranks.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Rank </p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item {{ request()->segment(2) == 'users' || request()->segment(2) == 'resource-module' || request()->segment(2) == 'organization' || request()->segment(2) == 'pre-built-achievement' || request()->segment(2) == 'challenge' ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->segment(2) == 'users' || request()->segment(2) == 'resource-module' || request()->segment(2) == 'organization' || request()->segment(2) == 'pre-built-achievement' || request()->segment(2) == 'challenge' ? 'active' : '' }}">
            <i class="nav-icon fas fa-tree"></i>
            <p>
              Component
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('users.index') }}" class="nav-link {{ Route::currentRouteName() == 'users.index' ? 'active' : '' }}">
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
            {{-- <li class="nav-item">
              <a href="{{ route('lab.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Lab</p>
              </a>
            </li> --}}
            <li class="nav-item">
              <a href="{{ route('challenge.index') }}" class="nav-link {{ Route::currentRouteName() == 'challenge.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Challenge Management</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('lab.index') }}" class="nav-link {{ Route::currentRouteName() == 'lab.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Lab Management</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('resource-module.index') }}" class="nav-link {{ Route::currentRouteName() == 'resource-module.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Resource Module</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('pre-built-achievement.index') }}" class="nav-link {{ Route::currentRouteName() == 'pre-built-achievement.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Pre built Achievement</p>
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
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>
              Tag Management
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview" style="display: none;">
            <li class="nav-item">
              <a href="{{ route('tags.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Tag</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('taggroup.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Tag Groups</p>
              </a>
            </li>

          </ul>
        </li>


        <li class="nav-item {{ request()->segment(2) == 'projects' || request()->segment(2) == 'projects-stage' || request()->segment(2) == 'projects-vertical' || request()->segment(2) == 'projects-type' || request()->segment(2) == 'projects-industry' || request()->segment(2) == 'projects-status' || request()->segment(2) == 'projects-pitch-template' || request()->segment(2) == 'projects-submission-requirement' ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->segment(2) == 'projects' || request()->segment(2) == 'projects-stage' || request()->segment(2) == 'projects-vertical' || request()->segment(2) == 'projects-type' || request()->segment(2) == 'projects-industry' || request()->segment(2) == 'projects-status' || request()->segment(2) == 'projects-pitch-template' || request()->segment(2) == 'projects-submission-requirement' ? 'active' : '' }}">
            <i class="nav-icon fas fa-tree"></i>
            <p>
              Project Management
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('projects.index') }}" class="nav-link {{ Route::currentRouteName() == 'projects.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Project</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-stage.index') }}" class="nav-link {{ Route::currentRouteName() == 'projects-stage.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Stage</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-vertical.index') }}" class="nav-link {{ Route::currentRouteName() == 'projects-vertical.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Vertical</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-type.index') }}" class="nav-link {{ Route::currentRouteName() == 'projects-type.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Type</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-industry.index') }}" class="nav-link {{ Route::currentRouteName() == 'projects-industry.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Industry</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-status.index') }}" class="nav-link {{ Route::currentRouteName() == 'projects-status.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Projects Status</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-pitch-template.index') }}" class="nav-link {{ Route::currentRouteName() == 'projects-pitch-template.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Pitch Templates</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('projects-submission-requirement.index') }}" class="nav-link {{ Route::currentRouteName() == 'projects-submission-requirement.index' ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Submission Requirement</p>
              </a>
            </li>
          </ul>
        </li>
          <li class="nav-item">
              <a href="{{ route('challenge-template.index')}} " class="nav-link {{ Route::currentRouteName() == 'challenge-template.index' ? 'active' : ''  }}">
                  <i class="far fa fa-book nav-icon"></i>
                  <p>Challenge Marketplace</p>
              </a>
          </li>
        <li class="nav-item">
          <a href="{{ route('trophyawards.index') }}" class="nav-link {{ Route::currentRouteName() == 'trophyawards.index' ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Trophy Awards</p>
          </a>
        </li>
          <li class="nav-item">
          <a href="{{ route('lab-marketplace.index')}} " class="nav-link {{ Route::currentRouteName() == 'lab-marketplace.index' ? 'active' : ''  }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Lab Marketplace</p>
          </a>
        </li>
          <li class="nav-item">
              <a href="{{ route('clone-lab.index') }}" class="nav-link {{ Route::currentRouteName() == 'clone-lab.index' ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Clone Lab</p>
              </a>
          </li>
          <li class="nav-item">
              <a href="{{ route('vendor-management.index') }}" class="nav-link {{ Route::currentRouteName() == 'vendor-management.index' ? 'active' : '' }}">
                  <i class="nav-icon fas fa-tree"></i>
                  <p>Vendor Management</p>
              </a>
          </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>
              Activity Awards
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview" style="display: none;">
            <li class="nav-item">
              <a href="{{ route('communitytrophy.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Regular Awards</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('skillsaward.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Skill Awards</p>
              </a>
            </li>

          </ul>
        </li>
        <li class="nav-item">
          <a href="{{ route('emailTemplates.index')}} " class="nav-link {{ Route::currentRouteName() == 'emailTemplates.index' ? 'active' : ''  }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Email Templates</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('explore.index')}} " class="nav-link {{ Route::currentRouteName() == 'explore.index' ? 'active' : ''  }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Explore Page</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('emailLogs.index')}} " class="nav-link {{ Route::currentRouteName() == 'emailLogs.index' ? 'active' : ''  }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Email Logs</p>
          </a>
        </li>
          <li class="nav-item">
              <a href="{{ route('setting.index')}} " class="nav-link {{ Route::currentRouteName() == 'setting.index' ? 'active' : ''  }}">
                  <i class="far fa fa-cog nav-icon"></i>
                  <p>Setting</p>
              </a>
          </li>
        <li class="nav-item">
          <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i class="nav-icon fas fa-sign-out-alt"></i> {{ __('Logout') }}</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
      </ul>
    </nav>
    @endif
    <!-- /.sidebar-menu -->
  </div>
