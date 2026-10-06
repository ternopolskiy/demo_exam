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
} elseif (!in_array($species, Application::SPECIES, true)) {
    $errors['species'] = 'Выберите вид животного из списка';
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
