<?php $this->layout('layout', ['title' => 'Main page']); ?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>Комментарии</h3></div>

                    <div class="card-body">
                        <?php echo flash()->display(); ?>

                        <? foreach ($comments as $comment) : ?>
                        <div class="media">
                            <?php if ($comment['avatar'] != null) : ?>
                                <img src="assets/img/<?= $comment['avatar']; ?>" alt="user_image" class="mr-3" width="64" height="64">
                            <?php else : ?>
                                <img src="assets/img/no-user.jpg" class="mr-3" alt="..." width="64" height="64">
                            <?php endif; ?>
                            <div class="media-body">
                                <h5 class="mt-0"><?= $comment['username'] ?></h5>
                                <span><small>
                                        <?
                                        $date = $comment['whenadded'];
                                        $date = strtotime($date);
                                        $date = date('d/m/y H:i:s', $date);
                                        echo $date;
                                        ?>
                                    </small></span>
                                <p><?= $comment['text'] ?> </p>
                            </div>
                        </div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if ($_SESSION['auth_logged_in']==true) : ?>
                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="card">
                        <div class="card-header"><h3>Оставить комментарий</h3></div>

                        <div class="card-body">
                            <form action="/store" method="post">
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Сообщение</label>
                                    <textarea name="text" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                    <?php if ($_SESSION['textError']==true) : ?>
                                        <span class="text-danger"><?= $_SESSION['textError'] ?></span>
                                    <?php endif; unset($_SESSION['textError']); ?>
                                </div>
                                <button type="submit" class="btn btn-success">Отправить</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info my-3">
                                Чтобы оставить комментарий, <a href="/login">авторизуйтесь</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>