@extends('admin.layouts.master')

@section('content')
<div class="page-wrapper default-version">
    @include('admin.partials.sidenav')
    @include('admin.partials.topnav')

    <div class="body-wrapper">
        <div class="bodywrapper__inner">

            @stack('topBar')

            @include('admin.partials.breadcrumb')

            @yield('panel')

        </div>
    </div>
</div>
@endsection
