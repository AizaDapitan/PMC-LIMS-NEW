<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <!-- Meta -->
  <meta name="description" content="" />
  <meta name="author" />

  <!-- CSRF Token -->

  <title>Philsaga - Laboratory Information and Management System</title>
  <link rel="shortcut icon" type="image/x-icon" href="{{env('APP_URL')}}/assets/img/favicon.png" />


  <!-- vendor css -->
  <link href="{{env('APP_URL')}}/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="{{env('APP_URL')}}/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
  <link href="{{env('APP_URL')}}/lib/typicons.font/typicons.css" rel="stylesheet">
  <link href="{{env('APP_URL')}}/lib/prismjs/themes/prism-vs.css" rel="stylesheet">
  <link href="{{env('APP_URL')}}/lib/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="{{env('APP_URL')}}/lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet">
  <link href="{{env('APP_URL')}}/lib/datatables.net-buttons/css/buttons.bootstrap.min.css" rel="stylesheet">
  <link href="{{env('APP_URL')}}/lib/select2/css/select2.min.css" rel="stylesheet">

  <!-- DashForge CSS -->
  <link rel="stylesheet" href="{{env('APP_URL')}}/assets/css/dashforge.css">
  <link rel="stylesheet" href="{{env('APP_URL')}}/assets/css/dashforge.dashboard.css">
  <!-- vue JS -->
  <link rel="stylesheet" href="{{env('APP_URL')}}/assets/primeicons-master/primeicons.css" />

  @yield('pagecss')
</head>

<body>

  <aside class="aside aside-fixed">
    <div class="aside-header">
      <div id="logo">
        <a href="{{env('APP_URL')}}/assets/img/logo-dark.svg" target="_blank" class="aside-logo standard-logo" data-dark-logo="{{env('APP_URL')}}/assets/img/logo-dark.svg"><img src="{{env('APP_URL')}}/assets/img/logo.svg" alt="LIMS logo"></a>
      </div>
      <a href="" class="aside-menu-link">
        <i data-feather="menu"></i>
        <i data-feather="x"></i>
      </a>
    </div>
    <div class="aside-body">
      <div class="aside-loggedin">
        <div class="d-flex justify-content-center">
          <a href="{{env('APP_URL')}}/assets/img/user.png" target="_blank" class="avatar wd-100"><img src="{{env('APP_URL')}}/assets/img/user.png" class="rounded-circle" alt="" /></a>
        </div>
        <div class="aside-loggedin-user tx-center">
          <h6 class="tx-semibold mg-b-0">{{ Auth::user()->name }}</h6>
          <p class="tx-color-03 tx-13 mg-b-0 tx-uppercase">{{ Auth::user()->role }}</p>
        </div>
      </div><!-- aside-loggedin -->
      <ul class="nav nav-aside">
        <li class="nav-label">LIMS</li>

        @if((Auth::user()->role == "ADMIN" || Auth::user()->role == "admin") || $showDeptRequestorModule > 0)
          <li class="nav-item with-sub {{ (request()->is('deptuser/*')) ? 'active show' : '' }}">
            <a href="" class="nav-link"><i data-feather="home"></i> <span>Dept. Requesters(User)</span><span class="badge badge-danger rounded-circle ml-3">{{ $unsaved}}</span></a>
            <ul>
              <li class="{{ (request()->is('deptuser/dashboard*')) ? 'active' : '' }}"><a href="{{ route('deptuser.index') }}">Dashboard</a></li>
              <li class="{{ (request()->is('deptuser/create-transmittal')) ? 'active' : '' }}"><a href="{{ route('deptuser.create') }}">Create Transmittal</a></li>
              <li class="{{ (request()->is('deptuser/unsaved-transmittal')) ? 'active' : '' }}"><a href="{{ route('deptuser.unsavedTrans') }}"><span>Unsaved Transmittal</span><span class="badge badge-danger rounded-circle ml-3">{{ $unsaved}}</span></a></li>
            </ul>
          </li>
        @endif
        <!-- <li class="nav-item with-sub {{ (request()->is('deptofficer/*')) ? 'active show' : '' }}">
          <a href="#" class="nav-link"><i data-feather="home"></i> <span>Dept. Requesters(Officer)</span></a>
          <ul>
            <li class="{{ (request()->is('deptofficer/dashboard*')) ? 'active' : '' }}"><a href="{{ route('deptofficer.index') }}">Dashboard</a></li>
            
          </ul>
        </li> -->
        @if((Auth::user()->role == "ADMIN" || Auth::user()->role == "admin") || $showDeptOfficerModule > 0)
          <li class="nav-item {{ (request()->is('deptofficer/dashboard*')) ? 'active' : '' }}"><a href="{{ route('deptofficer.index') }}" class="nav-link"><i data-feather="bell"></i> <span>Dept. Requesters(Officer)</span><span class="badge badge-danger rounded-circle ml-3">{{ $forOffApproval}}</span></a></li>
        @endif

        @if((Auth::user()->role == "ADMIN" || Auth::user()->role == "admin") || $showReceivingModule > 0)
          <li class="nav-item {{ (request()->is('qaqcreceiver/dashboard*')) ? 'active' : '' }}"><a href="{{ route('qaqcreceiver.index') }}" class="nav-link"><i data-feather="bell"></i> <span>Receiving</span><span class="badge badge-danger rounded-circle ml-3">{{ $forReceive }}</span></a></li>
        @endif

        @if((Auth::user()->role == "ADMIN" || Auth::user()->role == "admin") || $showAssayerModule > 0)
          <li class="nav-item with-sub {{ (request()->is('assayer/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="bell"></i> <span>Assayer</span><span class="badge badge-danger rounded-circle ml-3">{{ $forAssayer}}</span></a>
            <ul>
              <li class="{{ (request()->is('assayer/dashboard*')) ? 'active' : '' }}"><a href="{{ route('assayer.index') }}"><span>Dashboard</span><span class="badge badge-danger rounded-circle ml-3">{{ $forAssayer}}</span></a></li>
              <li class="{{ (request()->is('assayer/create*')) ? 'active' : '' }}"><a href="{{ route('assayer.create', [' ']) }}">Add New</a></li>
              <li class="{{ (request()->is('assayer/worksheet*')) ? 'active' : '' }}"><a href="{{ route('assayer.worksheet') }}">Worksheet</a></li>
            </ul>
          </li>
        @endif

        @if((Auth::user()->role == "ADMIN" || Auth::user()->role == "admin") || $showDigesterModule > 0)
          <li class="nav-item with-sub {{ (request()->is('digester/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="bell"></i> <span>Tech/Digester</span><span class="badge badge-danger rounded-circle ml-3">{{ $forDigester}}</span></a>
            <ul>
              <li class="{{ (request()->is('digester/dashboard*')) ? 'active' : '' }}"><a href="{{ route('digester.index') }}"><span>Dashboard</span><span class="badge badge-danger rounded-circle ml-3">{{ $forDigesterWorksheet}}</span></a></li>
              <li class="{{ (request()->is('digester/transmittal*')) ? 'active' : '' }}"><a href="{{ route('digester.transmittal', [' ']) }}"><span>Transmittal</span><span class="badge badge-danger rounded-circle ml-3">{{ $forDigesterTrans}}</span></a></li>
            </ul>
          </li>
        @endif

        @if((Auth::user()->role == "ADMIN" || Auth::user()->role == "admin") || $showAnalystModule > 0)
          <li class="nav-item with-sub {{ (request()->is('analyst/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="bell"></i> <span>Analyst</span><span class="badge badge-danger rounded-circle ml-3">{{ $forAnalyst}}</span></a>
            <ul>
              <li class="{{ (request()->is('analyst/dashboard*')) ? 'active' : '' }}"><a href="{{ route('analyst.index') }}"><span>Dashboard</span><span class="badge badge-danger rounded-circle ml-3">{{ $forAnalystWorksheet}}</span></a></li>
              <li class="{{ (request()->is('analyst/transmittal*')) ? 'active' : '' }}"><a href="{{ route('analyst.transmittal', [' ']) }}"><span>Transmittal</span><span class="badge badge-danger rounded-circle ml-3">{{ $forAnalystTrans}}</span></a></li>
            </ul>
          </li>
        @endif

        @if((Auth::user()->role == "ADMIN" || Auth::user()->role == "admin") || $showOfficerModule > 0)
          <li class="nav-item with-sub {{ (request()->is('officer/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="bell"></i> <span>Officer</span><span class="badge badge-danger rounded-circle ml-3">{{ $OfficerNotif}}</span></a>
            <ul>
              <li class="{{ (request()->is('officer/dashboard*')) ? 'active' : '' }}"><a href="{{ route('officer.index') }}"><span>Dashboard</span><span class="badge badge-danger rounded-circle ml-3">{{ $forOfficer}}</span></a></li>
              <li class="{{ (request()->is('officer/posted*')) ? 'active' : '' }}"><a href="{{ route('officer.posted') }}"><span>Posted</span></a></li>
              <li class="{{ (request()->is('officer/transmittal*')) ? 'active' : '' }}"><a href="{{ route('officer.transmittal') }}"><span>Transmittal</span></a></li>
              <li class="{{ (request()->is('officer/unsaved*')) ? 'active' : '' }}"><a href="{{ route('officer.unsavedTrans') }}"><span>Unsaved Transmittal</span><span class="badge badge-danger rounded-circle ml-3">{{ $unsavedOfficer}}</span></a></li>
              <li class="{{ (request()->is('officer/Solutions-dashboard*')) ? 'active' : '' }}"><a href="{{ route('officer.solutions') }}"><span>Solutions Dashboard</span><span class="badge badge-danger rounded-circle ml-3">{{ $officerSolutions}}</span></a></li>
            </ul>
          </li>
        @endif

        @if((Auth::user()->role == "ADMIN" || Auth::user()->role == "admin") || $showMaintenanceModule > 0)
          <li class="nav-label mg-t-25">Maintenance</li>
          <li class="nav-item with-sub {{ (request()->is('users/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="users"></i> <span>User Maintenance</span></a>
            <ul>
              <li class="{{ (request()->is('users/dashboard*')) ? 'active' : '' }}"><a href="{{ route('users.index') }}">Dashboard</a></li>
              <li class="{{ (request()->is('users/create*')) ? 'active' : '' }}"><a href="{{ route('users.create') }}">Create</a></li>
            </ul>
          </li>
          <li class="nav-item with-sub {{ (request()->is('roles/*')) ? 'active show' : '' }}">
            <a href="" class="nav-link"><i data-feather="users"></i> <span>Roles</span></a>
            <ul>
              <li class="{{ (request()->is('roles/dashboard*')) ? 'active' : '' }}"><a href="{{ route('roles.index') }}">Dashboard</a></li>
              <li class="{{ (request()->is('roles/create')) ? 'active' : '' }}"><a href="{{ route('roles.create') }}">Create</a></li>
            </ul>
          </li>
          <li class="nav-item with-sub {{ (request()->is('permissions/*')) ? 'active show' : '' }}">
            <a href="" class="nav-link"><i data-feather="users"></i> <span>Permissions</span></a>
            <ul>
              <li class="{{ (request()->is('permissions/dashboard*')) ? 'active' : '' }}"><a href="{{ route('permissions.index') }}">Dashboard</a></li>
              <li class="{{ (request()->is('permissions/create')) ? 'active' : '' }}"><a href="{{ route('permissions.create') }}">Create</a></li>
            </ul>
          </li>
          <li class="nav-item {{ (request()->is('accessrights/user*')) ? 'active' : '' }}"><a href="{{ route('accessrights.user') }}" class="nav-link"><i data-feather="settings"></i> <span>User Access Rights</span></a></li>
          <li class="nav-item {{ (request()->is('accessrights/role*')) ? 'active' : '' }}"><a href="{{ route('accessrights.role') }}" class="nav-link"><i data-feather="settings"></i> <span>Role Access Rights</span></a></li>
          <li class="nav-item with-sub {{ (request()->is('reports/*')) ? 'active show' : '' }}">
            <a href="" class="nav-link"><i data-feather="file"></i> <span>Reports</span></a>
            <ul> 
              <li class="{{ (request()->is('reports/audit-logs*')) ? 'active' : '' }}"><a href="{{ route('reports.auditLogs') }}">User Action Monitoring</a></li>
              <li class="{{ (request()->is('reports/error-logs*')) ? 'active' : '' }}"><a href="{{ route('reports.errorLogs') }}">Application Error Logs</a></li>
            </ul>
          </li>
          <li class="nav-item {{ (request()->is('applications/dashboard*')) ? 'active' : '' }}"><a href="{{ route('applications.index') }}" class="nav-link"><i data-feather="settings"></i> <span>Application Maintenance</span></a></li>
          
          <li class="nav-label mg-t-25">Master File</li>
          <li class="nav-item with-sub {{ (request()->is('supervisors/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="users"></i> <span>Shifting Supervisor</span></a>
            <ul>
              <li  class="{{ (request()->is('supervisors/dashboard*')) ? 'active' : '' }}"><a href="{{ route('supervisors.index') }}">Dashboard</a></li>
              <li class="{{ (request()->is('supervisors/create*')) ? 'active' : '' }}"><a href="{{ route('supervisors.create') }}">Create</a></li>
            </ul>
          </li>
          <li class="nav-item with-sub {{ (request()->is('fireassayers/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="users"></i> <span>Fire Assayer</span></a>
            <ul>
              <li  class="{{ (request()->is('fireassayers/dashboard*')) ? 'active' : '' }}"><a href="{{ route('fireassayers.index') }}">Dashboard</a></li>
              <li class="{{ (request()->is('fireassayers/create*')) ? 'active' : '' }}"><a href="{{ route('fireassayers.create') }}">Create</a></li>
            </ul>
          </li>
          <li class="nav-item with-sub {{ (request()->is('assistantofficers/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="users"></i> <span>Assistant QA Officer</span></a>
            <ul>
              <li  class="{{ (request()->is('assistantofficers/dashboard*')) ? 'active' : '' }}"><a href="{{ route('assistantofficers.index') }}">Dashboard</a></li>
              <li class="{{ (request()->is('assistantofficers/create*')) ? 'active' : '' }}"><a href="{{ route('assistantofficers.create') }}">Create</a></li>
            </ul>
          </li>
          <li class="nav-item with-sub {{ (request()->is('chiefchemists/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="users"></i> <span>Chief Chemist</span></a>
            <ul>
              <li  class="{{ (request()->is('chiefchemists/dashboard*')) ? 'active' : '' }}"><a href="{{ route('chiefchemists.index') }}">Dashboard</a></li>
              <li class="{{ (request()->is('chiefchemists/create*')) ? 'active' : '' }}"><a href="{{ route('chiefchemists.create') }}">Create</a></li>
            </ul>
          </li>
          <li class="nav-item with-sub {{ (request()->is('qaanalysts/*')) ? 'active show' : '' }}">
            <a href="#" class="nav-link"><i data-feather="users"></i> <span>Analyst</span></a>
            <ul>
              <li  class="{{ (request()->is('qaanalysts/dashboard*')) ? 'active' : '' }}"><a href="{{ route('qaanalysts.index') }}">Dashboard</a></li>
              <li class="{{ (request()->is('qaanalysts/create*')) ? 'active' : '' }}"><a href="{{ route('qaanalysts.create') }}">Create</a></li>
            </ul>
          </li>
        @endif
        
      </ul>
    </div>
  </aside>

  <div class="content ht-100v pd-0">
    <div class="content-header">
      <div class="content-search content-company wd-100p-f mx-wd-500-f excerpt-1 pd-r-20">
        <h3 class="tx-15 mg-b-0 text-md-white">Philsaga - Laboratory Information and Management System</h3>
      </div>
      <div class="position-relative d-flex">
        <button id="themeMode" type="button" class="btn btn-white rounded-circle theme-mode">
          <i data-feather="moon"></i>
        </button>
        <div class="dropdown dropdown-profile">
          <a href="" class="dropdown-link" data-toggle="dropdown" data-display="static">
            <div class="avatar avatar-sm">
              <img src="{{env('APP_URL')}}/assets/img/user.png" class="rounded-circle" alt="" />
            </div>
          </a>
          <!-- dropdown-link -->
          <div class="dropdown-menu dropdown-menu-right tx-13">
            <h6 class="tx-semibold mg-b-5">{{ Auth::user()->name }}</h6>
            <p class="tx-12 tx-color-03">{{ Auth::user()->role }}</p>
            <div class="dropdown-divider"></div>

            <a href="#" class="dropdown-item"><i data-feather="settings"></i> Account Settings</a>
            <a href="{{ route('auth.change_password') }}" class="dropdown-item"><i data-feather="edit-3"></i>Change Password</a>
            <a href="{{ route('logout') }}" class="dropdown-item"><i data-feather="log-out"></i>Log Out</a>
          </div>
          <!-- dropdown-menu -->
        </div>
      </div>

      <!-- dropdown -->
    </div><!-- content-header -->
    <notifications />

    <div class="content-body py-5 px-4" id="app">
      <div id="myModal1" class="modal">
        <!-- Modal content -->
        <div class="modal-content" id="content">
          <span class="close" id="close">&times;</span>
          <p style="font-size: 18px; font-weight:bold;">In exactly 1 hour the system will undergo maitenance! Please save your work!</p>
        </div>
      </div>
      <div style="margin-top:-40px">
        @if($reason)
        <div class="alert alert-danger alert-dismissable">
          <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button> -->
          <span class="fa fa-exclamation"></span>
          <label aria-labelledby="notifications" id="notifications">{{ $reason }} </label>
          <label aria-labelledby="countdown" id="countdown" style="float:right; font-weight:bold">Time Remaining : </label>
          <label aria-labelledby="datetime" id="datetime" style="display:block">Shutdown Date : {{ $scheduledate }} {{ $scheduletime}} </label>
        </div>
        @else
        <label aria-labelledby="countdown" id="countdown" style="display:none; font-weight:bold">Time Remaining : </label>
        @endif
      </div>
      @yield('content')
    </div>


    <!-- Success -->
    <div class="pos-fixed b-10 r-10">
      <div id="toast_success" class="toast bg-success bd-0 wd-350" data-delay="3000" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success">
          <h6 class="tx-white tx-14 mg-b-0 mg-r-auto tx-semibold">
            <i data-feather="check-circle" width="14"></i> Success
          </h6>
          <button type="button" class="ml-2 mb-1 close tx-normal" data-dismiss="toast" aria-label="Close">
            <i data-feather="x" aria-hidden="true" class="tx-white wd-15"></i>
          </button>
        </div>
        @if (Session::has('success'))
        <div class="toast-body bg-success tx-white">
          {{ Session::get('success') }}
        </div>
        @endif
        remove
      </div>
    </div>

    <!-- Error -->
    <div class="pos-fixed b-10 r-10">
      <div id="toast_error" class="toast bg-danger bd-0 wd-350" data-delay="3000" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body pd-6 tx-white">
          <button type="button" class="ml-2 mb-1 close tx-normal tx-shadow-none" data-dismiss="toast" aria-label="Close">
            <span class="tx-white" aria-hidden="true">&times;</span>
          </button>
          <h6 class="mg-b-15 mg-t-15 tx-white">
            <i data-feather="alert-circle"></i> ERROR!
          </h6>
          <p>{{ Session::get('error') }}</p>
        </div>
      </div>
    </div>

    <!-- Error -->
    <div class="pos-fixed b-10 r-10">
      <div id="toastDynamicError" class="toast bg-danger bd-0 wd-350" data-delay="60000" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body pd-6 tx-white">
          <button type="button" class="ml-2 mb-1 close tx-normal tx-shadow-none" data-dismiss="toast" aria-label="Close">
            <span class="tx-white" aria-hidden="true">&times;</span>
          </button>
          <h6 class="mg-b-15 mg-t-15 tx-white">
            <i data-feather="alert-circle"></i> ERROR!
          </h6>
          <p id="errorMessage"></p>
        </div>
      </div>
    </div>

    <div class="pos-absolute b-10 r-20 z-index-200">
      <div id="toastErrorMessage" class="toast bg-danger bd-0 wd-350" data-delay="10000" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger">
          <h6 class="tx-white tx-14 mg-b-0 mg-r-auto tx-semibold">
            <i data-feather="x-circle" width="14"></i> Failed
          </h6>
          <button type="button" class="ml-2 mb-1 close tx-normal" data-dismiss="toast" aria-label="Close">
            <i data-feather="x" aria-hidden="true" class="tx-white wd-15"></i>
          </button>
        </div>
        <div class="toast-body bg-danger tx-white">
          <ul id="errorMessage" style="list-style-type: none; padding-inline-start: 0px">
            @if ($errors->any()) @foreach ($errors->all() as $error)
            <li><i class="mg-r-10"></i> {{ $error }}</li>
            @endforeach @endif
          </ul>
        </div>
      </div>
    </div>

    <div class="modal effect-scale" id="prompt-banner-error" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle">Failed</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p id="bannerErrorMessage"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
    <script src="{{ asset('/js/app.js') }}"></script>
    <script src="{{env('APP_URL')}}/lib/jquery/jquery.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/feather-icons/feather.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>

    <!-- vendor script -->
    <script src="{{env('APP_URL')}}/lib/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-buttons/js/buttons.dataTables.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-buttons/js/buttons.colVis.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-buttons/js/buttons.bootstrap.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-buttons/js/jszip.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-buttons/js/pdfmake.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/datatables.net-buttons/js/vfs_fonts.js"></script>
    <script src="{{env('APP_URL')}}/lib/select2/js/select2.min.js"></script>
    <script src="{{env('APP_URL')}}/lib/jqueryui/jquery-ui.min.js"></script>

    <script src="{{env('APP_URL')}}/assets/js/dashforge.js"></script>
    <script src="{{env('APP_URL')}}/assets/js/dashforge.aside.js"></script>


    @yield('pagejs')
    <script>
      let saveFile = () => {
        // Get the data from each element on the form.
        const cssCode = editor.getCss();
        const htmlCode = editor.getHtml();

        // This variable stores all the data.
        let data = cssCode + " \r\n " + htmlCode;

        // Write data in 'Output.txt' .
        fs.writeFile("Output.txt", data, (err) => {
          // In case of a error throw err.
          if (err) throw err;
        });
      };
    </script>
    <script>
      var modal = document.getElementById("myModal1");
      var tday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
      var tmonth = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      var shown = 0;
      var span = document.getElementById("close");
      // When the user clicks on <span> (x), close the modal
      span.onclick = function() {
        modal.style.display = "none";
      }

      function GetClock() {
        var d = new Date();
        var nday = d.getDay(),
          nmonth = d.getMonth(),
          ndate = d.getDate(),
          nyear = d.getFullYear();
        var nhour = d.getHours(),
          nmin = d.getMinutes(),
          nsec = d.getSeconds(),
          ap;
        var ohour = nhour + 1;
        if (nhour <= 9) nhour = "0" + nhour;
        if (nhour == 0) {
          ap = " AM";
          nhour = 12;
        } else if (nhour < 12) {
          ap = " AM";
        } else if (nhour == 12) {
          ap = " PM";
        } else if (nhour > 12) {
          ap = " PM";
          nhour -= 12;
        }

        if (nmin <= 9) nmin = "0" + nmin;
        if (nsec <= 9) nsec = "0" + nsec;

        var clocktext = "" + tday[nday] + ", " + tmonth[nmonth] + " " + ndate + ", " + nyear + " " + nhour + ":" + nmin + ":" + nsec + ap + "";
        // document.getElementById('clockbox').innerHTML = clocktext;
        var schedule = {!!json_encode($scheduledate) !!} + ' ' + {!!json_encode($scheduletime) !!};
        // dt = dt.replace(':00.0000000','');
        var mnth = nmonth + 1;
        var dte = ndate;
        if (mnth <= 9) mnth = "0" + mnth;
        if (dte <= 9) dte = "0" + dte;
        var curDateless1hour = nyear + '-' + mnth + '-' + dte + ' ' + ohour + ":" + nmin;
        var curDate = nyear + '-' + mnth + '-' + dte + ' ' + (ohour - 1) + ":" + nmin;
        // console.log(dt);
        // console.log(dd2);
        if (schedule == curDateless1hour && shown == 0) {
          shown = 1;
              //alert("In exactly 1 hour the system will undergo maitenance! Please save your work.");

              //console.log("In exactly 1 hour the system will undergo maitenance! Please save your work.");
          modal.style.display = "block";
          return false;
        }
        if (schedule == curDate) {
          $.ajax({
            url: '{!! route('applications.systemDown_auto') !!}',
            type: 'GET',
            async: false,
            success: function(response) {}
          });
        }
        // console.log(schedule);
        // console.log(curDate);
        if (schedule > curDate) {
          var TimeDiff = timeDiffCalc(new Date(schedule), new Date());
        } else {
          TimeDiff = "Maintenance is in progress!";
        }

        document.getElementById('countdown').innerHTML = "Time Remaining : " + TimeDiff;
      }
      GetClock();
      setInterval(GetClock, 1000);

      function timeDiffCalc(dateFuture, dateNow) {
        // console.log(dateNow);
        let diffInMilliSeconds = Math.abs(dateFuture - dateNow) / 1000;
        // calculate days
        const days = Math.floor(diffInMilliSeconds / 86400);
        diffInMilliSeconds -= days * 86400;

        // calculate hours
        const hours = Math.floor(diffInMilliSeconds / 3600) % 24;
        diffInMilliSeconds -= hours * 3600;

        // calculate minutes
        const minutes = Math.floor(diffInMilliSeconds / 60) % 60;
        diffInMilliSeconds -= minutes * 60;

        // calculate minutes
        const seconds = Math.floor(diffInMilliSeconds);
        diffInMilliSeconds -= seconds;
        // if(seconds > 0){

        let difference = '';
        if (days > 0) {
          difference += (days === 1) ? `${days} day, ` : `${days} days, `;
        }

        difference += (hours === 0 || hours === 1) ? `${hours} hour, ` : `${hours} hours, `;

        difference += (minutes === 0 || hours === 1) ? `${minutes} minute, ` : `${minutes} minutes, `;

        difference += (seconds === 0 || seconds === 1) ? `${seconds} seconds` : `${seconds} seconds`;

        return difference;
        // }
      }
    </script>
</body>

</html>