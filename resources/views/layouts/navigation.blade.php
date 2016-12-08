<nav class="navigation navbar-default " role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- navbar-brand is hidden on larger screens, but visible when the menu is collapsed -->
            <a class="navbar-brand" href="{{ route("index") }}">空塵計</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="main-navbar">
            <ul class="nav navbar-nav">
                <li class="{{ Request::is('/') ? 'activate' : '' }}">
                    <a href="{{ route('index') }}"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a>
                </li>
                {{-- <li class="{{ Request::is('introduction') ? 'activate' : '' }}">
                    <a href="{{ route('introduction') }}">細懸浮微粒?</a>
                </li> --}}
               {{--  <li class="{{ Request::is('immediate') ? 'activate' : '' }}">
                    <a href="{{ route('immediate') }}"><span class="glyphicon glyphicon-object-align-bottom" aria-hidden="true"></span>即時空汙資訊</a>
                </li> --}}
                <li class="{{ Request::is('instant_info') ? 'activate' : '' }}">
                    <a href="{{ route('instant_info.index') }}"><span class="glyphicon glyphicon-object-align-bottom" aria-hidden="true"></span>即時空汙資訊</a>
                </li>
                <li class="{{ Request::is('history-compare') ? 'activate' : '' }}">
                    <a href="{{ route('history-compare.index') }}"><span class="glyphicon glyphicon-hourglass" aria-hidden="true"></span>歷年空汙比較</a>
                </li>
                <li class="{{ Request::is('excessive') ? 'activate' : '' }}">
                    <a href="{{ route('research.excessive') }}"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>好日子與壞日子</a>
                </li>
                <li class="{{ Request::is('excel-export') ? 'activate' : '' }}">
                    <a href="{{ route('excel-export.index') }}"><span class="glyphicon glyphicon-save" aria-hidden="true"></span>歷史資料下載</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>
