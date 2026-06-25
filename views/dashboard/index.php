<?php use App\Core\Session; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-speedometer2"></i> Dashboard</h1>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-cash-stack display-1 text-primary"></i>
                <h3 class="mt-3">Selamat datang, <?= htmlspecialchars(Session::user()['name'] ?? 'User') ?>!</h3>
                <p class="text-muted">TransactionAPP siap digunakan. Gunakan menu di sidebar untuk navigasi.</p>
            </div>
        </div>
    </div>
</div>
