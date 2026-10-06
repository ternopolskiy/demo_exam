<?php

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->check()) {
    flash('error', 'Чтобы записаться на приём, войдите в систему.');
    redirect('../../frontend/login.php');
}

$petName = trim($_POST['pet_name'] ?? '');
$species = trim($_POST['species'] ?? '');
$service = trim($_POST['service'] ?? '');
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$paymentMethod = trim($_POST['payment_method'] ?? '');

$errors = [];

if ($error = Validator::required($petName, 'Кличка питомца')) {
    $errors['pet_name'] = $error;
}
if ($error = Validator::required($species, 'Вид животного')) {
    $errors['species'] = $error;
}
if ($error = Validator::required($service, 'Услуга')) {
    $errors['service'] = $error;
}
if ($error = Validator::required($date, 'Дата приёма')) {
    $errors['date'] = $error;
}
if ($error = Validator::required($time, 'Время приёма')) {
    $errors['time'] = $error;
}
if (!in_array($paymentMethod, ['Наличными', 'Картой в клинике'], true)) {
    $errors['payment_method'] = 'Выберите способ оплаты';
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    keep_old(['pet_name', 'species', 'service', 'date', 'time']);
    redirect('../../frontend/application_create.php');
}

Application::create([
    'user_id' => auth()->id(),
    'pet_name' => $petName,
    'species' => $species,
    'service' => $service,
    'date' => $date,
    'time' => $time,
    'payment_method' => $paymentMethod,
]);

unset($_SESSION['errors']);
clear_old();
flash('success', 'Заявка отправлена администратору и ожидает подтверждения.');
redirect('../../frontend/applications.php');
