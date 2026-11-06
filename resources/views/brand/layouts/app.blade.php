@extends('brand.layouts.master')

@section('content')
<div class="page-wrapper default-version">
    @include('brand.partials.sidenav')
    @include('brand.partials.topnav')

    <div class="body-wrapper">
        <div class="bodywrapper__inner">
            @include('brand.partials.breadcrumb')
            @yield('panel')
        </div>
    </div>
</div>
@endsection
