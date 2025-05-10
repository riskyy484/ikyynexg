<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><?php echo $data['title']; ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo URLROOT; ?>/admin/add_user" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add User
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <p class="card-text display-4"><?php echo count($data['users']); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Servers</h5>
                <p class="card-text display-4"><?php echo count($data['servers']->data ?? []); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Total Nodes</h5>
                <p class="card-text display-4"><?php echo count($data['nodes']->data ?? []); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Users -->
<div class="card mb-4">
    <div class="card-header">
        <h4>Recent Users</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(array_slice($data['users'], 0, 5) as $user) : ?>
                    <tr>
                        <td><?php echo $user->id; ?></td>
                        <td><?php echo $user->username; ?></td>
                        <td><?php echo $user->email; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $user->role == 'admin' ? 'primary' : 'secondary'; ?>">
                                <?php echo ucfirst($user->role); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo URLROOT; ?>/admin/edit_user/<?php echo $user->id; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo URLROOT; ?>/admin/delete_user/<?php echo $user->id; ?>" method="post" class="d-inline">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Servers -->
<div class="card">
    <div class="card-header">
        <h4>Recent Servers</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach(array_slice($data['servers']->data ?? [], 0, 4) as $server) : ?>
            <div class="col-md-3 mb-4">
                <div class="card server-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $server->attributes->name; ?></h5>
                        <p class="card-text">
                            <strong>Owner:</strong> <?php echo $server->attributes->user; ?><br>
                            <strong>Status:</strong> <span class="badge bg-secondary">Loading...</span>
                        </p>
                        <a href="<?php echo $this->domain; ?>/server/<?php echo $server->attributes->identifier; ?>" 
                           class="btn btn-primary btn-sm" target="_blank">
                            Manage
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>