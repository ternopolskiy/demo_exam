<?php
require_once __DIR__ . '/../backend/bootstrap.php';
$pageTitle = 'Вход';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
require __DIR__ . '/partials/header.php';
?>
<div class="card auth-card">
<h1>Вход</h1>
<form method="post" action="../backend/actions/login.php" class="form" novalidate>
<div class="field<?= isset($errors['login']) ? ' has-error' : '' ?>">
<label for="login">Логин</label>
<input type="text" id="login" name="login" value="<?= e(old('login')) ?>">
<?php if (isset($errors['login'])): ?><div class="error-text"><?= e($errors['login']) ?></div><?php endif; ?>
</div>
<div class="field<?= isset($errors['password']) ? ' has-error' : '' ?>">
<label for="password">Пароль</label>
<input type="password" id="password" name="password">
<?php if (isset($errors['password'])): ?><div class="error-text"><?= e($errors['password']) ?></div><?php endif; ?>
</div>
<button type="submit" class="btn btn-block">Войти</button>
</form>
<p class="alt-link">Еще не зарегистрированы? <a href="../frontend/register.php">Регистрация</a></p>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
