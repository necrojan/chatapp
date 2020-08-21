<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin') }}">
                    <i class="nav-icon icon-speedometer"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('responses.index') }}">
                    <i class="nav-icon icon-speech"></i> Global Response
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('responses.personal.index') }}">
                    <i class="nav-icon icon-wallet"></i> Personal Response
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('chat') }}">
                    <i class="nav-icon icon-lock"></i> Chat
                </a>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
