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
<?php
$filters = [
    'status' => trim($_GET['status'] ?? ''),
    'service' => trim($_GET['service'] ?? ''),
    'search' => trim($_GET['q'] ?? ''),
];
$perPage = 5;
$total = Application::countAll($filters);
$pages = max(1, (int)ceil($total / $perPage));
$page = min(max(1, (int)($_GET['page'] ?? 1)), $pages);
$applications = Application::all($filters, $page, $perPage);
$allStatuses = array_keys(Application::TRANSITIONS);
$services = Application::services();
$updatedId = (int)($_GET['updated'] ?? 0);
$adminPageUrl = function (int $targetPage, array $filters): string {
    $params = array_filter([
        'status' => $filters['status'],
        'service' => $filters['service'],
        'q' => $filters['search'],
        'page' => $targetPage,
    ]);
    return '../frontend/admin.php' . ($params ? '?' . http_build_query($params) : '');
};
?>
<form method="get" action="../frontend/admin.php" class="filter-form">
<div class="filter-row">
<select name="status" class="filter-input">
<option value="">Все статусы</option>
<?php foreach ($allStatuses as $option): ?>
<option value="<?= e($option) ?>"<?= $filters['status'] === $option ? ' selected' : '' ?>><?= e($option) ?></option>
<?php endforeach; ?>
</select>
<select name="service" class="filter-input">
<option value="">Все услуги</option>
<?php foreach ($services as $option): ?>
<option value="<?= e($option) ?>"<?= $filters['service'] === $option ? ' selected' : '' ?>><?= e($option) ?></option>
<?php endforeach; ?>
</select>
<input type="text" name="q" class="filter-input" placeholder="Кличка или ФИО владельца" value="<?= e($filters['search']) ?>">
<button type="submit" class="btn">Найти</button>
<a class="btn btn-outline" href="../frontend/admin.php">Сброс</a>
</div>
</form>
<p class="results-count">Найдено заявок: <?= $total ?></p>
<?php if (!$applications): ?>
<div class="card empty-state"><p>Заявок не найдено.</p></div>
<?php else: ?>
<div class="app-list">
<?php foreach ($applications as $application): ?>
<article class="card app-card admin-card<?= $updatedId === (int)$application['id'] ? ' card-updated' : '' ?>" data-status="<?= e(Application::statusSlug($application['status'])) ?>">
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
<?php if ($pages > 1): ?>
<nav class="pagination">
<?php if ($page > 1): ?>
<a class="page-link" href="<?= e($adminPageUrl($page - 1, $filters)) ?>">‹ Назад</a>
<?php endif; ?>
<span class="page-current">Стр. <?= $page ?> из <?= $pages ?></span>
<?php if ($page < $pages): ?>
<a class="page-link" href="<?= e($adminPageUrl($page + 1, $filters)) ?>">Вперёд ›</a>
<?php endif; ?>
</nav>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
