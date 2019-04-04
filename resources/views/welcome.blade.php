@extends('shopify-app::layouts.default')

@section('styles')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
@stop

@section('content')
    <div id="app">
        <app></app>
    </div>
@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        // ESDK page and bar title
        window.mainPageTitle = 'Welcome Page';
        window.shop = '{{ShopifyApp::shop()->shopify_domain}}';
        ShopifyApp.ready(function () {
            ShopifyApp.Bar.initialize({
                title: 'Welcome'
            })
        });
    </script>
@endsection
