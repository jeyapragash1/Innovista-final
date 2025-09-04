<?php require_once 'admin_header.php'; ?>

<h2>User Management</h2>
<p>View all customers and service providers. You can deactivate accounts to block access.</p>

<div class="content-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>kisho</td>
                    <td>kishojeyapragash@gmail.com</td>
                    <td>Customer</td>
                    <td><span class="status-badge status-active">Active</span></td>
                    <td class="action-buttons">
                        <a href="#" class="btn-link" style="color: var(--status-rejected);">Deactivate</a>
                        <button class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn-action delete" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>Eve Davis</td>
                    <td>eve.d@example.com</td>
                    <td>Customer</td>
                    <td><span class="status-badge status-inactive">Inactive</span></td>
                    <td class="action-buttons">
                        <a href="#" class="btn-link" style="color: var(--status-approved);">Activate</a>
                        <button class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn-action delete" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>