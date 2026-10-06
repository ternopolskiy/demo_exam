<?php
require_once __DIR__ . '/backend/bootstrap.php';
$depth = 0;
$pageTitle = 'Хвост и Лапы — запись к ветеринару';
require __DIR__ . '/frontend/partials/header.php';
?>
<section class="slider" id="slider">
<div class="slider-track">
<div class="slide active"><img src="frontend/assets/img/1.jpg" alt="Ветеринарная клиника «Хвост и Лапы»"></div>
<div class="slide"><img src="frontend/assets/img/2.jpg" alt="Ветеринарная клиника «Хвост и Лапы»"></div>
<div class="slide"><img src="frontend/assets/img/3.jpg" alt="Ветеринарная клиника «Хвост и Лапы»"></div>
<div class="slide"><img src="frontend/assets/img/4.jpg" alt="Ветеринарная клиника «Хвост и Лапы»"></div>
</div>
<button type="button" class="slider-btn slider-prev" aria-label="Предыдущее изображение">‹</button>
<button type="button" class="slider-btn slider-next" aria-label="Следующее изображение">›</button>
<div class="slider-dots"></div>
</section>
<section class="hero">
<h1>Ветеринарная клиника «Хвост и Лапы»</h1>
<p>Онлайн-запись на приём: выберите услугу, дату и время — мы подтвердим заявку.</p>
<div class="hero-actions">
<?php if (auth()->isAdmin()): ?>
<a class="btn" href="frontend/admin.php">Панель администратора</a>
<?php elseif (auth()->check()): ?>
<a class="btn" href="frontend/application_create.php">Записаться на приём</a>
<a class="btn btn-outline" href="frontend/applications.php">Мои записи</a>
<?php else: ?>
<a class="btn" href="frontend/register.php">Регистрация</a>
<a class="btn btn-outline" href="frontend/login.php">Вход</a>
<?php endif; ?>
</div>
</section>
<?php require __DIR__ . '/frontend/partials/footer.php'; ?>
