<?php require_once 'admin_header.php'; ?>

<h2>Provider Approvals</h2>
<p>Review new service providers and manage their verification status.</p>

<div class="content-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Name / Company</th>
                    <th>Email</th>
                    <th>Approval Status</th>
                    <th>Credentials</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>kishol5</td>
                    <td>kishojeyapragash5@gmail.com</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                    <td>Not Submitted</td>
                    <td class="action-buttons">
                        <a href="#" class="btn-link">Approve</a>
                        <a href="#" class="btn-link" style="color: var(--status-rejected);">Reject</a>
                    </td>
                </tr>
                <tr>
                    <td>Modern Living Designs</td>
                    <td>provider@test.com</td>
                    <td><span class="status-badge status-approved">Approved</span></td>
                    <td><span class="status-badge status-verified">Verified</span></td>
                    <td class="action-buttons">
                        <button class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn-action delete" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                 <tr>
                    <td>Rejected Builders</td>
                    <td>rejected.provider@example.com</td>
                    <td><span class="status-badge status-rejected">Rejected</span></td>
                    <td>-</td>
                    <td class="action-buttons">
                        <button class="btn-action delete" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>