<?php
require_once __DIR__ . '/../backend/bootstrap.php';
$pageTitle = 'Запись на приём';

if (!auth()->check()) {
    flash('error', 'Войдите в систему, чтобы записаться на приём.');
    redirect('../frontend/login.php');
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
require __DIR__ . '/partials/header.php';
?>
<div class="card form-card">
<h1>Запись на приём</h1>
<form method="post" action="../backend/actions/application_create.php" class="form" novalidate>
<div class="field<?= isset($errors['pet_name']) ? ' has-error' : '' ?>">
<label for="pet_name">Кличка питомца</label>
<input type="text" id="pet_name" name="pet_name" value="<?= e(old('pet_name')) ?>">
<?php if (isset($errors['pet_name'])): ?><div class="error-text"><?= e($errors['pet_name']) ?></div><?php endif; ?>
</div>
<div class="field<?= isset($errors['species']) ? ' has-error' : '' ?>">
<label for="species">Вид животного</label>
<select id="species" name="species">
<option value="">Выберите вид животного</option>
<?php foreach (Application::SPECIES as $option): ?>
<option value="<?= e($option) ?>"<?= old('species') === $option ? ' selected' : '' ?>><?= e($option) ?></option>
<?php endforeach; ?>
</select>
<?php if (isset($errors['species'])): ?><div class="error-text"><?= e($errors['species']) ?></div><?php endif; ?>
</div>
<div class="field<?= isset($errors['service']) ? ' has-error' : '' ?>">
<label for="service">Услуга</label>
<select id="service" name="service">
<option value="">Выберите услугу</option>
<?php foreach (Application::SERVICES as $option): ?>
<option value="<?= e($option) ?>"<?= old('service') === $option ? ' selected' : '' ?>><?= e($option) ?></option>
<?php endforeach; ?>
</select>
<?php if (isset($errors['service'])): ?><div class="error-text"><?= e($errors['service']) ?></div><?php endif; ?>
</div>
<div class="field-row">
<div class="field<?= isset($errors['date']) ? ' has-error' : '' ?>">
<label for="date">Дата приёма</label>
<input type="text" id="date" name="date" value="<?= e(old('date')) ?>" placeholder="ДД.ММ.ГГГГ">
<?php if (isset($errors['date'])): ?><div class="error-text"><?= e($errors['date']) ?></div><?php endif; ?>
</div>
<div class="field<?= isset($errors['time']) ? ' has-error' : '' ?>">
<label for="time">Время приёма</label>
<input type="text" id="time" name="time" value="<?= e(old('time')) ?>" placeholder="ЧЧ:ММ">
<?php if (isset($errors['time'])): ?><div class="error-text"><?= e($errors['time']) ?></div><?php endif; ?>
</div>
</div>
<div class="field<?= isset($errors['payment_method']) ? ' has-error' : '' ?>">
<label for="payment_method">Способ оплаты</label>
<select id="payment_method" name="payment_method">
<option value="">Выберите способ оплаты</option>
<option value="Наличными"<?= old('payment_method') === 'Наличными' ? ' selected' : '' ?>>Наличными</option>
<option value="Картой в клинике"<?= old('payment_method') === 'Картой в клинике' ? ' selected' : '' ?>>Картой в клинике</option>
</select>
<?php if (isset($errors['payment_method'])): ?><div class="error-text"><?= e($errors['payment_method']) ?></div><?php endif; ?>
</div>
<button type="submit" class="btn btn-block">Записаться</button>
</form>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
