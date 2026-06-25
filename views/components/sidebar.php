<?php

use App\Core\Session;

$user = Session::user();
$permissions = $user['permissions'] ?? [];

// Helper: cek permission
function can(string $perm, array $permissions): bool
{
    return in_array($perm, $permissions, true);
}

// Active helper
function isActive(string $path): string
{
    $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return str_starts_with($current, $path) ? 'active' : '';
}
?>

<?php if (Session::isLoggedIn()): ?>
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= isActive('/TransactionAPP') && !str_starts_with(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/TransactionAPP/users') ? 'active' : '' ?>"
                   href="/TransactionAPP">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <?php if (can('manage-users', $permissions)): ?>
            <li class="nav-item">
                <a class="nav-link <?= isActive('/TransactionAPP/users') ? 'active' : '' ?>"
                   href="/TransactionAPP/users">
                    <i class="bi bi-people"></i> Users
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <hr>
        <div class="px-3 small text-muted">
            Logged in as <strong><?= htmlspecialchars($user['name'] ?? 'Unknown') ?></strong>
        </div>
    </div>
</nav>
<?php endif; ?>
