<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-primary">📦 Container</h4>
    <hr>
    <ul class="nav nav-pills flex-column">
        <li class="nav-item">
            <a class="nav-link <?= service('uri')->getSegment(1) == 'dashboard' ? 'active' : '' ?>" href="/dashboard">📊 Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= service('uri')->getSegment(1) == 'kontainer' ? 'active' : '' ?>" href="/cari-kontainer">🔍 Cari Kontainer</a>
        </li>
    </ul>
    <hr class="mt-auto">
    <a href="/logout" class="btn btn-outline-danger">🚪 Log out</a>
</div>