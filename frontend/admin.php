<?php
require_once __DIR__ . '/../backend/bootstrap.php';
$pageTitle = 'Панель администратора';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    if (auth()->attemptAdmin($login, $password)) {
        flash('success', 'Вы вошли как администратор.');
        redirect('../frontend/admin.php');
    }
    $_SESSION['errors'] = ['admin' => 'Неверный логин или пароль администратора'];
    keep_old(['login']);
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
require __DIR__ . '/partials/header.php';
?>
<?php if (!auth()->isAdmin()): ?>
<div class="card auth-card">
<h1>Вход для администратора</h1>
<form method="post" action="../frontend/admin.php" class="form" novalidate>
<div class="field<?= isset($errors['admin']) ? ' has-error' : '' ?>">
<label for="login">Логин</label>
<input type="text" id="login" name="login" value="<?= e(old('login')) ?>">
<?php if (isset($errors['admin'])): ?><div class="error-text"><?= e($errors['admin']) ?></div><?php endif; ?>
</div>
<div class="field">
<label for="password">Пароль</label>
<input type="password" id="password" name="password">
</div>
<button type="submit" class="btn btn-block">Войти</button>
</form>
</div>
<?php else: ?>
<h1 class="page-title">Панель администратора</h1>
<?php $applications = Application::all(); ?>
<?php if (!$applications): ?>
<div class="card empty-state"><p>Заявок пока нет.</p></div>
<?php else: ?>
<div class="app-list">
<?php foreach ($applications as $application): ?>
<article class="card app-card admin-card" data-status="<?= e(Application::statusSlug($application['status'])) ?>">
<div class="app-card-head">
<h2><?= e($application['pet_name']) ?></h2>
<span class="status status-<?= e(Application::statusSlug($application['status'])) ?>"><?= e($application['status']) ?></span>
</div>
<dl class="app-details">
<div class="detail"><dt>Владелец</dt><dd><?= e($application['full_name']) ?> (<?= e($application['login']) ?>)</dd></div>
<div class="detail"><dt>Вид животного</dt><dd><?= e($application['species']) ?></dd></div>
<div class="detail"><dt>Услуга</dt><dd><?= e($application['service']) ?></dd></div>
<div class="detail"><dt>Дата и время</dt><dd><?= e($application['date']) ?> <?= e($application['time']) ?></dd></div>
<div class="detail"><dt>Оплата</dt><dd><?= e($application['payment_method']) ?></dd></div>
</dl>
<?php $nextStatuses = Application::TRANSITIONS[$application['status']] ?? []; ?>
<?php if ($nextStatuses): ?>
<div class="admin-actions">
<?php foreach ($nextStatuses as $nextStatus): ?>
<form method="post" action="../backend/actions/admin_status.php" class="inline-form">
<input type="hidden" name="application_id" value="<?= (int)$application['id'] ?>">
<input type="hidden" name="status" value="<?= e($nextStatus) ?>">
<button type="submit" class="btn btn-small <?= $nextStatus === Application::STATUS_CANCELLED ? 'btn-danger' : '' ?>"><?= e($nextStatus) ?></button>
</form>
<?php endforeach; ?>
</div>
<?php else: ?>
<p class="no-actions">Заявка закрыта</p>
<?php endif; ?>
</article>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
