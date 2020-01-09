<?php $this->layout('layout', ['title' => 'User Profile']); ?>
<main class="py-4">
      <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>Профиль пользователя</h3></div>

                    <div class="card-body">
                        <?php if ($_SESSION['authProf_profileEditSuccess']==true) : ?>
                            <div class="alert alert-success" role="alert">
                                Профиль успешно обновлен
                            </div>
                        <?php endif; unset($_SESSION['authProf_profileEditSuccess']); ?>

                        <form action="/profile/edit" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Name</label>
                                        <input type="text" class="form-control" name="username" id="exampleFormControlInput1" value="<?= $user_username ?>">
                                        <?php if ($_SESSION['authProf_usernameError']==true) : ?>
                                            <span class="text-danger"><?= $_SESSION['authProf_usernameError'] ?></span>
                                        <?php endif; unset($_SESSION['authProf_usernameError']); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Email</label>
                                        <input type="email" class="form-control " name="email" id="exampleFormControlInput1" value="<?= $user_email ?>">
                                        <?php if ($_SESSION['authProf_emailError']==true) : ?>
                                            <span class="text-danger"><?= $_SESSION['authProf_emailError'] ?></span>
                                        <?php endif; unset($_SESSION['authProf_emailError']); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Аватар</label>
                                        <input type="file" class="form-control" name="image" id="exampleFormControlInput1">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <?php if (is_null($user_avatar) || empty($user_avatar) ) : ?>
                                        <img src="assets/img/no-user.jpg" alt="" class="img-fluid">
                                    <?php else : ?>
                                        <img src="assets/img/<?= $user_avatar ?>" class="img-fluid">
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-warning">Edit profile</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12" style="margin-top: 20px;">
                <div class="card">
                    <div class="card-header"><h3>Безопасность</h3></div>

                    <div class="card-body">
                        <?php if ($_SESSION['auth_passwordEditSuccess'] == true) :  ?>
                            <div class="alert alert-success" role="alert">
                                Password has been changed
                            </div>
                        <?php endif; unset($_SESSION['auth_passwordEditSuccess']) ?>

                        <form action="/profile/password" method="post">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Current password</label>
                                        <input type="password" name="old_password" class="form-control" id="exampleFormControlInput1">
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">New password</label>
                                        <input type="password" name="password" class="form-control" id="exampleFormControlInput1">
                                        <?php if ($_SESSION['auth_passwordEditError']==true) : ?>
                                        <span class="text-danger"><? echo $_SESSION['auth_passwordEditError']; unset($_SESSION['auth_passwordEditError']); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Password confirmation</label>
                                        <input type="password" name="password_confirmation" class="form-control" id="exampleFormControlInput1">
                                    </div>

                                    <button class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>
</main>