<?php $this->layout('layout', ['title'=>'Login']) ?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Login</div>

                    <div class="card-body">
                        <form method="POST" action="/login-handler">

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" class="form-control" name="email"  autocomplete="email">
                                    <?php
                                    if (isset($_SESSION['authLog_emailError']))
                                    {
                                        echo "<span class='text-danger'>". $_SESSION['authLog_emailError'] . "</span>";
                                    }
                                    unset($_SESSION['authLog_emailError']);
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password"  autocomplete="current-password">
                                    <?php
                                    if (isset($_SESSION['authLog_passwordError']))
                                    {
                                        echo "<span class='text-danger'>". $_SESSION['authLog_passwordError'] . "</span>";
                                    }
                                    unset($_SESSION['authLog_passwordError']);
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">

                                        <label class="form-check-label" for="remember">
                                            Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Login
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