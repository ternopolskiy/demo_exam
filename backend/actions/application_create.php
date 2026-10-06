<?php

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->check()) {
    flash('error', 'Чтобы записаться на приём, войдите в систему.');
    redirect('../../frontend/login.php');
}

$petId = (int)($_POST['pet_id'] ?? 0);
$service = trim($_POST['service'] ?? '');
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$paymentMethod = trim($_POST['payment_method'] ?? '');

$errors = [];

$pet = Pet::find($petId);
if ($pet === null || (int)$pet['user_id'] !== auth()->id()) {
    $errors['pet_id'] = 'Выберите питомца из списка';
}
if ($error = Validator::required($service, 'Услуга')) {
    $errors['service'] = $error;
} elseif (!in_array($service, Application::SERVICES, true)) {
    $errors['service'] = 'Выберите услугу из списка';
}
if ($error = Validator::date($date)) {
    $errors['date'] = $error;
}
if ($error = Validator::time($time)) {
    $errors['time'] = $error;
}
if (!in_array($paymentMethod, ['Наличными', 'Картой в клинике'], true)) {
    $errors['payment_method'] = 'Выберите способ оплаты';
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    keep_old(['pet_id', 'service', 'date', 'time', 'payment_method']);
    redirect('../../frontend/application_create.php');
}

Application::create([
    'user_id' => auth()->id(),
    'pet_id' => $pet['id'],
    'pet_name' => $pet['name'],
    'species' => $pet['species'],
    'service' => $service,
    'date' => $date,
    'time' => $time,
    'payment_method' => $paymentMethod,
]);

unset($_SESSION['errors']);
clear_old();
flash('success', 'Заявка отправлена администратору и ожидает подтверждения.');
redirect('../../frontend/applications.php');
