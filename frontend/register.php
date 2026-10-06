<?php
require_once __DIR__ . '/../backend/bootstrap.php';
$pageTitle = 'Регистрация';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
require __DIR__ . '/partials/header.php';
?>
<div class="card auth-card">
<h1>Регистрация</h1>
<form method="post" action="../backend/actions/register.php" class="form" novalidate>
<div class="field<?= isset($errors['login']) ? ' has-error' : '' ?>">
<label for="login">Логин</label>
<input type="text" id="login" name="login" value="<?= e(old('login')) ?>" placeholder="Латиница и цифры, минимум 6">
<?php if (isset($errors['login'])): ?><div class="error-text"><?= e($errors['login']) ?></div><?php endif; ?>
</div>
<div class="field<?= isset($errors['password']) ? ' has-error' : '' ?>">
<label for="password">Пароль</label>
<input type="password" id="password" name="password" placeholder="Минимум 6 символов">
<?php if (isset($errors['password'])): ?><div class="error-text"><?= e($errors['password']) ?></div><?php endif; ?>
</div>
<div class="field<?= isset($errors['full_name']) ? ' has-error' : '' ?>">
<label for="full_name">ФИО</label>
<input type="text" id="full_name" name="full_name" value="<?= e(old('full_name')) ?>" placeholder="Иванов Иван Иванович">
<?php if (isset($errors['full_name'])): ?><div class="error-text"><?= e($errors['full_name']) ?></div><?php endif; ?>
</div>
<div class="field<?= isset($errors['phone']) ? ' has-error' : '' ?>">
<label for="phone">Телефон</label>
<input type="text" id="phone" name="phone" value="<?= e(old('phone')) ?>" placeholder="+7(XXX)XXX-XX-XX">
<?php if (isset($errors['phone'])): ?><div class="error-text"><?= e($errors['phone']) ?></div><?php endif; ?>
</div>
<div class="field<?= isset($errors['email']) ? ' has-error' : '' ?>">
<label for="email">E-mail</label>
<input type="text" id="email" name="email" value="<?= e(old('email')) ?>" placeholder="mail@example.ru">
<?php if (isset($errors['email'])): ?><div class="error-text"><?= e($errors['email']) ?></div><?php endif; ?>
</div>
<button type="submit" class="btn btn-block">Создать пользователя</button>
</form>
<p class="alt-link">Уже зарегистрированы? <a href="../frontend/login.php">Вход</a></p>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
