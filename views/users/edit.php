<?php

use App\Helpers\Sanitize;

// Get role IDs the user currently has
$userRoleIds = array_column($user['roles'], 'id');
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-person-gear"></i> Edit User</h1>
    <a href="/TransactionAPP/users" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="/TransactionAPP/users/<?= $user['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= Sanitize::escape($user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= Sanitize::escape($user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Kosongkan jika tidak diganti">
                        <div class="form-text">Minimal 6 karakter. Biarkan kosong untuk tetap menggunakan password lama.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Roles</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <?php foreach ($roles as $role): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="role_ids[]"
                                           value="<?= $role['id'] ?>" id="role_<?= $role['id'] ?>"
                                           <?= in_array($role['id'], $userRoleIds) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="role_<?= $role['id'] ?>">
                                        <?= htmlspecialchars($role['name']) ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                   <?= $user['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Save Changes
                    </button>
                    <a href="/TransactionAPP/users" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
