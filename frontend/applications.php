<?php
require_once __DIR__ . '/../backend/bootstrap.php';
$pageTitle = 'Мои записи';

if (!auth()->check()) {
    flash('error', 'Войдите в систему, чтобы просматривать записи.');
    redirect('../frontend/login.php');
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$applications = Application::forUser(auth()->id());
require __DIR__ . '/partials/header.php';
?>
<h1 class="page-title">Мои записи</h1>
<?php if (isset($errors['text'])): ?>
<div class="flash flash-error"><?= e($errors['text']) ?></div>
<?php endif; ?>
<?php if (!$applications): ?>
<div class="card empty-state">
<p>У вас пока нет записей.</p>
<a class="btn" href="../frontend/application_create.php">Записаться на приём</a>
</div>
<?php else: ?>
<div class="app-list">
<?php foreach ($applications as $application): ?>
<?php $review = Review::forApplication((int)$application['id']); ?>
<article class="card app-card">
<div class="app-card-head">
<h2><?= e($application['pet_name']) ?></h2>
<span class="status status-<?= e(Application::statusSlug($application['status'])) ?>"><?= e($application['status']) ?></span>
</div>
<dl class="app-details">
<div class="detail"><dt>Вид животного</dt><dd><?= e($application['species']) ?></dd></div>
<div class="detail"><dt>Услуга</dt><dd><?= e($application['service']) ?></dd></div>
<div class="detail"><dt>Дата</dt><dd><?= e($application['date']) ?></dd></div>
<div class="detail"><dt>Время</dt><dd><?= e($application['time']) ?></dd></div>
<div class="detail"><dt>Оплата</dt><dd><?= e($application['payment_method']) ?></dd></div>
</dl>
<?php if ($review): ?>
<div class="review">
<div class="review-label">Ваш отзыв</div>
<p><?= e($review['text']) ?></p>
</div>
<?php else: ?>
<form method="post" action="../backend/actions/review_create.php" class="form review-form">
<input type="hidden" name="application_id" value="<?= (int)$application['id'] ?>">
<div class="field">
<label for="review-<?= (int)$application['id'] ?>">Отзыв о работе клиники</label>
<textarea id="review-<?= (int)$application['id'] ?>" name="text" rows="3" placeholder="Расскажите о вашем опыте"></textarea>
</div>
<button type="submit" class="btn btn-small">Оставить отзыв</button>
</form>
<?php endif; ?>
</article>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
