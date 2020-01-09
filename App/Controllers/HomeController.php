<?php
namespace App\Controllers;
use App\Components\QueryBuilder;
use Delight\Auth\Auth;
use Intervention\Image\ImageManager;
use League\Plates\Engine;
use PDO;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class HomeController
{
    private $engine;
    private $pdo;
    private $qb;
    private $auth;

    public function __construct(Engine $engine, PDO $pdo, QueryBuilder $qb, Auth $auth)
    {
        $this->engine = $engine;
        $this->pdo = $pdo;
        $this->qb = $qb;
        $this->auth = $auth;
    }

    public function index()
    {
        $comments = $this->qb->selectInnerJoin();
        echo $this->engine->render('index', ['comments' => $comments]);
    }

    public function store()
    {
        if (v::alnum()->length(3,200)->validate($_POST['text']))
        {
            $data = [
                'name' => $_SESSION['auth_username'],
                'text' => $_POST['text'],
                'user_id' => $_SESSION['auth_user_id']
            ];
            $this->qb->store('comments', $data);
            flash()->message('Комментарий успешно добавлен', 'success');
            goto sony;
        }
        else
        {
            $_SESSION['textError'] = 'Write text 3-200 chars with digits and letters';
            goto sony;
        }


        die;
        sony:
        header("Location: /");
        exit;
    }

    public function test()
    {
        echo 'test';
    }

    public function profile()
    {
        if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        $user_id = $this->auth->getUserId();
        $user_email = $this->auth->getEmail();
        $user_username = $this->auth->getUsername();
        $user_avatar = $this->qb->getAvatar('users', $user_id);

        echo $this->engine->render('profile', ['user_id'=>$user_id, 'user_email'=>$user_email, 'user_username'=>$user_username, 'user_avatar'=>$user_avatar['avatar']]);
    }

    public function profileEdit()
    {
        // Данные текущего пользователя
        $user = $this->qb->selectOne('users', $_SESSION['auth_user_id']);


        // Валидация пользователя и почты
        // Проверка существующей почты
        if (true)
        {
            $usernameV = v::noWhitespace()->alnum()->length(3,20)->setName('Username');
            $emailV = v::email()->setName('Email');
            try {
                $usernameV->assert($_POST['username']);
            } catch(NestedValidationException $exception) {
                $errorUsername = $exception->findMessages([
                    'noWhitespace' => '{{name}} cannot contain spaces',
                    'alnum' => '{{name}} must contain only letters and digits',
                    'length' => '{{name}} must be between 3 and 20 chars',
                ]);
            }
            try {
                $emailV->assert($_POST['email']);
            } catch(NestedValidationException $exception) {
                $errorEmail = $exception->findMessages([
                    'email' => '{{name}} invalid'
                ]);
            }

            if (v::arrayType()->notEmpty()->validate($errorUsername))
            {
                if (v::notEmpty()->validate($errorUsername['noWhitespace']))
                {
                    $_SESSION['authProf_usernameError'] = $errorUsername['noWhitespace'];
                }
                if (v::notEmpty()->validate($errorUsername['alnum']))
                {
                    $_SESSION['authProf_usernameError'] = $errorUsername['alnum'];
                }
                if (v::notEmpty()->validate($errorUsername['length']))
                {
                    $_SESSION['authProf_usernameError'] = $errorUsername['length'];
                }
            }
            if (v::arrayType()->notEmpty()->validate($errorEmail))
            {
                if (v::notEmpty()->validate($errorEmail['email']))
                {
                    $_SESSION['authProf_emailError'];
                }
            }
            if (v::arrayType()->notEmpty()->validate($errorUsername) || v::arrayType()->notEmpty()->validate($errorUsername) )
            {
                goto sony;
            }

            $newEmail = $this->qb->selectOneByEmail('users', strtolower($_POST['email']));
            if ($newEmail && !v::equals($user['email'])->validate(strtolower($_POST['email'])) )
            {
                $_SESSION['authProf_emailError'] = 'Email already exists';
                goto sony;
            }
        }

        // Обновление данных
        if (true)
        {
            $avatar = $_FILES['image'];
            if ($avatar['size'] == 0)
            {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email']
                ];
                $this->qb->update('users', $data, $_SESSION['auth_user_id']);
                $_SESSION['auth_email'] = $_POST['email'];
                $_SESSION['auth_username'] = $_POST['username'];
                $_SESSION['authProf_profileEditSuccess'] = 'Profile edit success';
                goto sony;
            }
            elseif($avatar['size'] != 0)
            {
                $newAvatar = $this->getImage($_FILES['image']);
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'avatar' => $newAvatar
                ];
                $this->qb->update('users', $data, $_SESSION['auth_user_id']);
                $_SESSION['auth_email'] = $_POST['email'];
                $_SESSION['auth_username'] = $_POST['username'];
                $_SESSION['authProf_profileEditSuccess'] = 'Profile edit success';
                // Удаление старого аватара если есть
                if (!is_null($user['avatar']))
                {
                    unlink('assets/img/' . $user['avatar']);
                }
                goto sony;
            }
        }



        die;
        sony:
        header('Location: /profile');
        exit;
    }

    public function profilePasswordEdit()
    {
        $passwordV = v::alnum()->noWhitespace()->length(6,20)->equals($_POST['password_confirmation'])->setName('Password');
        try {
            $passwordV->assert($_POST['password']);
        } catch(NestedValidationException $exception) {
            $errorPassword = $exception->findMessages([
                'alnum' => '{{name}} must contain only letters and digits',
                'noWhitespace' => '{{name}} cannot contain spaces',
                'length' => '{{name}} must be between 6 and 20 chars',
                'equals' => 'Passwords don\'t match'
            ]);
        }
        if (true)
        {
            if (v::arrayType()->notEmpty()->validate($errorPassword))
            {
                if (v::notEmpty()->validate($errorPassword['alnum']))
                {
                    $_SESSION['auth_passwordEditError'] = $errorPassword['alnum'];
                }
                if (v::notEmpty()->validate($errorPassword['noWhitespace']))
                {
                    $_SESSION['auth_passwordEditError'] = $errorPassword['noWhitespace'];
                }
                if (v::notEmpty()->validate($errorPassword['length']))
                {
                    $_SESSION['auth_passwordEditError'] = $errorPassword['length'];
                }
                if (v::notEmpty()->validate($errorPassword['equals']))
                {
                    $_SESSION['auth_passwordEditError'] = $errorPassword['equals'];
                }
            }
            if (v::notEmpty()->validate($errorPassword))
            {
                goto sony;
            }
        }

        try {
            $this->auth->changePassword($_POST['old_password'], $_POST['password']);
            $_SESSION['auth_passwordEditSuccess'] = 'Password has been changed';
            goto sony;
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            die('Not logged in');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password(s)');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }

        die;
        sony:
        header('Location: /profile');
        exit;
    }

    public function getImage($image)
    {
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() .'.'. $extension;
        move_uploaded_file($image['tmp_name'], 'assets/img/' . $fileName);
        return $fileName;
    }
}


































