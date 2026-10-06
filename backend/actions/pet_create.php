<?php

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->check()) {
    flash('error', 'Чтобы добавить питомца, войдите в систему.');
    redirect('../../frontend/login.php');
}

$name = trim($_POST['name'] ?? '');
$species = trim($_POST['species'] ?? '');

$errors = [];

if ($error = Validator::required($name, 'Кличка')) {
    $errors['name'] = $error;
}
if ($error = Validator::required($species, 'Вид животного')) {
    $errors['species'] = $error;
} elseif (!in_array($species, Pet::SPECIES, true)) {
    $errors['species'] = 'Выберите вид животного из списка';
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    keep_old(['name', 'species']);
    redirect('../../frontend/profile.php');
}

Pet::create(auth()->id(), $name, $species);

unset($_SESSION['errors']);
clear_old();
flash('success', 'Питомец добавлен в профиль.');
redirect('../../frontend/profile.php');
