<?php $this->layout('layout', ['title'=>'Registering']) ?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Register</div>

                    <div class="card-body">
                        <?php echo flash()->display(); ?>
                        <form method="POST" action="/register-handler">

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="username">
                                    <?php
                                    if (isset($_SESSION['authReg_usernameError']))
                                    {
                                        echo "<span class='text-danger'>" . $_SESSION['authReg_usernameError'] . "</span>";
                                    }
                                    unset($_SESSION['authReg_usernameError']);
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" >
                                    <?php
                                    if (isset($_SESSION['authReg_emailError']))
                                    {
                                        echo "<span class='text-danger'>". $_SESSION['authReg_emailError'] . "</span>";
                                    }
                                    unset($_SESSION['authReg_emailError']);
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control " name="password"  autocomplete="new-password">
                                    <?php
                                    if (isset($_SESSION['authReg_passwordError']))
                                    {
                                        echo "<span class='text-danger'>". $_SESSION['authReg_passwordError'] . "</span>";
                                    }
                                    unset($_SESSION['authReg_passwordError']);
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>
                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="confirmPassword"  autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Register
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>