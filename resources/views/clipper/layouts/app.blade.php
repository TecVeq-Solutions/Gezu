@extends('clipper.layouts.master')

@section('content')
<div class="page-wrapper default-version">
    @include('clipper.partials.sidenav')
    @include('clipper.partials.topnav')

    <div class="body-wrapper">
        <div class="bodywrapper__inner">
            @include('clipper.partials.breadcrumb')
            @yield('panel')
        </div>
    </div>
</div>
@endsection
