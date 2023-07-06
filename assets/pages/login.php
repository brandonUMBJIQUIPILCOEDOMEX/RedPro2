<div class="login">
    <div class="col-sm-12 col-md-4 bg-white border rounded p-4 shadow-sm">
        <form method="post" action="assets/php/actions.php?login">
            <div class="d-flex justify-content-center">

                <img class="mb-4" src="assets/images/pictogram.png" alt="" height="45">
            </div>
            <h1 class="h5 mb-3 fw-normal">Login</h1>

            <div class="form-floating">
                <input type="text" name="username_email" value="<?= showFormData('username_email') ?>" class="form-control rounded-0" placeholder="username/email">
                <label for="floatingInput">usuario/correo</label>
            </div>
            <?= showError('username_email') ?>
            <div class="form-floating mt-1">
                <input type="password" name="password" class="form-control rounded-0" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">contraseña</label>
            </div>
            <?= showError('password') ?>
            <?= showError('checkuser') ?>


            <div class="mt-3 d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" type="submit">Login</button>
                <a href="?signup" class="text-decoration-none">Crea una cuenta</a>


            </div>
            <a href="?forgotpassword&newfp" class="text-decoration-none">Olvidaste tu contraseña?</a>
            </form>

        <!-- Botones de inicio de sesión con Google y Facebook -->
        <div>
            <a href="#" class="btn btn-outline-danger">
                <i class="fab fa-google"></i> Iniciar sesión con Google
            </a>
            <a href="#" class="btn btn-outline-primary">
                <i class="fab fa-facebook"></i> Iniciar sesión con Facebook
            </a>
        </div>
        
    </div>
</div>







<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">