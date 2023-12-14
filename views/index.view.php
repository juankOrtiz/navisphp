<?php require('partials/head.php') ?>
<?php require('partials/nav.php') ?>
<?php require('partials/banner.php') ?>

<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p>Hola, <?= $_SESSION['user']['email'] ?? 'Invitado' ?>. Bienvenido a la página inicio</p>
    </div>
</main>

<?php require('partials/footer.php') ?>