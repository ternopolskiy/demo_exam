<?php

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->isAdmin()) {
    redirect('../../frontend/admin.php');
}

$applicationId = (int)($_POST['application_id'] ?? 0);
$status = (string)($_POST['status'] ?? '');

if (!Application::updateStatus($applicationId, $status)) {
    flash('error', 'Недопустимый переход статуса.');
    redirect('../../frontend/admin.php');
}

flash('success', 'Статус заявки обновлён: ' . $status);
redirect('../../frontend/admin.php?updated=' . $applicationId);
