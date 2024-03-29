@extends('app')
@section('header')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/starting.css') }}">
@endsection
@section('content')
    <div class="buzz-container">
        <div class="global-container container starting text-center">
            @if (isset($type))
                <div class="modeempty-header">
                    <div class="modeempty_text">
                        <i class="fa fa-info-circle "></i>
                        <h4> {{ $type }} Are Not Ready Yet!</h4>
                        <p>
                            <b>Must add 5 {{ $type }} at least!</b>
                        </p>
                    </div>
                </div>
            @else
                <h1 class="title">Welcome to Buzzy</h1>
                <h5 class="thanks">Thanks for Buying and Using Buzzy Script.</h5>
                <div class="clear"></div>

                <div class="modeempty-header green">
                    <div class="modeempty_text">

                        @if (Auth::check() && Auth::user()->isAdmin())
                            <h4>Your homepage is not ready yet!</h4>
                            <p> Create your first post <a href="{{ route('post.create') }}">here.</a></p>
                            <BR><BR>
                            <b>Go to Admin Panel Settings and Configure the Site!</b>
                            <br>
                            <a href="{{ route('admin.configs') }}" class="button button-big button-orange">Admin Panel
                                Settings</a>
                        @else
                            <i class="fa fa-check green"></i>
                            <h4>Your Buzzy script successfully installed!</h4>
                            <p>
                                <b>Your homepage is not ready yet!</b><br>
                                Let's add some posts using great editor.
                                <br>
                                But before that you may want to check some configurations.
                                For this please connect to admin panel.
                            <div class="loginat">
                                <b>We have created an admin account for you.</b>
                                <br>
                                Email: <u>admin@admin.com </u><br>
                                Password: <u>admin</u>
                                <br>
                                <small>Login form in the upper right corner of this page or <a
                                        href="{{ route('login') }}">Click here to Login</a></small>
                                <div class="modeadmin_header">
                                    <div class="modeadmin_text">
                                        <p>Warning: You must change the administrator password in the user settings after
                                            logging in.</p>
                                    </div>
                                </div>
                            </div>
                            </p>
                        @endif


                    </div>
                </div>
                <div class="clear"></div>
                <div class="copyright">

                    <strong>Copyright &copy; {{ now()->format('Y') }} <a href="http://akbilisim.com"
                            target="_blank"><b>akbilisim</b></a>.</strong>
                    All rights reserved.

                    &nbsp;&nbsp;&nbsp;

                    <b><a href="http://buzzy.akbilisim.com" target="_blank">Buzzy</a></b> Version :
                    {{ config('buzzy.version') }}
                </div>
            @endif


        </div>
    </div>
@endsection
