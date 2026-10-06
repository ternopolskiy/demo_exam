<?php

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->check()) {
    flash('error', 'Чтобы оставить отзыв, войдите в систему.');
    redirect('../../frontend/login.php');
}

$applicationId = (int)($_POST['application_id'] ?? 0);
$text = trim($_POST['text'] ?? '');

$application = Application::find($applicationId);

if ($application === null || (int)$application['user_id'] !== auth()->id()) {
    flash('error', 'Заявка не найдена.');
    redirect('../../frontend/applications.php');
}

if (Review::forApplication($applicationId) !== null) {
    flash('error', 'Отзыв по этой заявке уже оставлен.');
    redirect('../../frontend/applications.php');
}

if ($application['status'] !== Application::STATUS_COMPLETED) {
    flash('error', 'Отзыв можно оставить только после завершения приёма.');
    redirect('../../frontend/applications.php');
}

$errors = [];
if ($error = Validator::required($text, 'Отзыв')) {
    $errors['text'] = $error;
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    keep_old(['text']);
    redirect('../../frontend/applications.php');
}

Review::create($applicationId, auth()->id(), $text);

unset($_SESSION['errors']);
clear_old();
flash('success', 'Спасибо за отзыв!');
redirect('../../frontend/applications.php');
