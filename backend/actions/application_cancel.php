<?php

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->check()) {
    flash('error', 'Чтобы отменить заявку, войдите в систему.');
    redirect('../../frontend/login.php');
}

$applicationId = (int)($_POST['application_id'] ?? 0);

if (!Application::cancelByUser($applicationId, auth()->id())) {
    flash('error', 'Отменить заявку можно только со статусом «Новая».');
} else {
    flash('success', 'Заявка отменена.');
}

redirect('../../frontend/applications.php');
