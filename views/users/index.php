<?php

use App\Core\Session;
use App\Helpers\Sanitize;

$currentUserPermissions = Session::user()['permissions'] ?? [];
$canManageUsers = in_array('manage-users', $currentUserPermissions, true);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-people"></i> Users</h1>
    <?php if ($canManageUsers): ?>
    <a href="/TransactionAPP/users/create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add User
    </a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada user. Klik <strong>"Add User"</strong> untuk menambahkan.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= Sanitize::escape($user['name']) ?></td>
                            <td><?= Sanitize::escape($user['email']) ?></td>
                            <td>
                                <?php foreach ($user['roles'] as $role): ?>
                                    <span class="badge bg-primary me-1"><?= Sanitize::escape($role['name']) ?></span>
                                <?php endforeach; ?>
                                <?php if (empty($user['roles'])): ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($canManageUsers): ?>
                                <a href="/TransactionAPP/users/<?= $user['id'] ?>/edit" class="btn btn-sm btn-warning me-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="/TransactionAPP/users/<?= $user['id'] ?>/delete" method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Hapus user ini?');">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
