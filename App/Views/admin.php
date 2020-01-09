<?php $this->layout('layout', ['title' => 'Admin panel']);d($comments); ?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>Админ панель</h3></div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Аватар</th>
                                    <th>Имя</th>
                                    <th>Дата</th>
                                    <th>Комментарий</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($comments as $comment) : ?>
                                <tr>
                                    <td>
                                        <?php if ($comment['avatar'] != NULL) : ?>
                                            <img src="assets/img/<?= $comment['avatar'] ?>" alt="" class="img-fluid" width="64" height="64">
                                        <?php else : ?>
                                            <img src="assets/img/no-user.jpg" alt="" class="img-fluid" width="64" height="64">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $comment['username'] ?></td>
                                    <td>
                                        <?php
                                            $date = $comment['whenadded'];
                                            $date = strtotime($date);
                                            $date = date('d/m/Y H:i:s', $date);
                                            echo $date;
                                        ?>
                                    </td>
                                    <td><?= $comment['text'] ?></td>
                                    <td>
                                        <?php if ($comment['is_public']==1) : ?>
                                            <a href="/admin/disallowComment?id=<?= $comment['id'] ?>" class="btn btn-warning">Запретить</a>
                                        <?php else : ?>
                                            <a href="/admin/allowComment?id=<?= $comment['id'] ?>" class="btn btn-success">Разрешить</a>
                                        <?php endif; ?>
                                        <a href="/admin/deleteComment?id=<?= $comment['id'] ?>" onclick="return confirm('are you sure?')" class="btn btn-danger">Удалить</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

