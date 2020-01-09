<?php
namespace App\Controllers;
use App\Components\QueryBuilder;
use Delight\Auth\Auth;
use League\Plates\Engine;
use PDO;
use SimpleMail;
use Valitron\Validator;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class AuthController
{
    private $engine;
    private $pdo;
    private $qb;
    private $auth;
    private $v;


    public function __construct(Engine $engine, PDO $pdo, QueryBuilder $qb, Auth $auth, Validator $v)
    {
        $this->engine = $engine;
        $this->pdo = $pdo;
        $this->qb = $qb;
        $this->auth = $auth;
        $this->v = $v;
    }

    public function check()
    {
        d($_SESSION, $_COOKIE);
        if ($this->auth->isLoggedIn()) {
            echo 'User is signed in';
        }
    else {
            echo 'User is not signed in yet';
        }
    }

    public function check2()
    {
        if ($this->auth->isRemembered()) {
            echo 'User did not sign in but was logged in through their long-lived cookie';
        }
        else {
            echo 'User signed in manually';
        }
    }
    
    public function login()
    {
        echo $this->engine->render('login');
    }

    public function loginHandler()
    {
        $emailV = v::notEmpty()->email()->setName('Email');
        $passwordV = v::notEmpty()->setName('Password');

        try {
            $emailV->assert($_POST['email']);
        } catch(NestedValidationException $exception) {
            $errorEmail = $exception->findMessages([
                'notEmpty' => 'Type the email',
                'email' => '{{name}} must be email type'
            ]);
        }
        try {
            $passwordV->assert($_POST['password']);
        } catch(NestedValidationException $exception) {
            $errorPassword = $exception->findMessages([
                'notEmpty' => 'Type the password'
            ]);
        }

        if (true)
        {
            if (v::arrayType()->notEmpty()->validate($errorEmail))
            {
                if (v::notEmpty()->validate($errorEmail['email']))
                {
                    $_SESSION['authLog_emailError'] = $errorEmail['email'];
                }
                elseif (v::notEmpty()->validate($errorEmail['notEmpty']))
                {
                    $_SESSION['authLog_emailError'] = $errorEmail['notEmpty'];
                }
            }
            if (v::arrayType()->notEmpty()->validate($errorPassword))
            {
                if (v::notEmpty()->validate($errorPassword['notEmpty']))
                {
                    $_SESSION['authLog_passwordError'] = $errorPassword['notEmpty'];
                }
            }
            if (v::notEmpty()->validate($errorEmail) || v::notEmpty()->validate($errorPassword))
            {
                goto sony;
            }
        }

        if ($_POST['remember'] == 1) {
            $rememberDuration = (int) (60 * 60 * 24);
        }
        else {
            $rememberDuration = null;
        }

        try {
            $this->auth->login($_POST['email'], $_POST['password'], $rememberDuration);
            header('Location: /');
            exit;
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Wrong email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Wrong password');
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            die('Email not verified');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }

        die;
        sony:
        header("Location: /login ");
        exit;
    }

    public function register()
    {
        echo $this->engine->render('register');
    }

    public function registerHandler()
    {
        $usernameV = v::alnum()->length(3, 20)->noWhitespace()->setName('Username');
        $emailV = v::email()->setName('Email');
        $passwordV = v::alnum()->equals($_POST['confirmPassword'])->length(6, 20)->setName('Password');

        try {
            $usernameV->assert($_POST['username']);
        } catch (NestedValidationException $exception) {
            $errorUsername = $exception->findMessages([
                'alnum' => '{{name}} must be letters and digits (a-z, 0-9)',
                'noWhitespace' => '{{name}} must not contain spaces',
                'length' => '{{name}} length must be between 3-20 chars'
            ]);
        }
        try {
            $emailV->assert($_POST['email']);
        } catch (NestedValidationException $exception) {
            $errorEmail = $exception->findMessages([
                'email' => '{{name}} must be email type'
            ]);
        }
        try {
            $passwordV->assert($_POST['password']);
        } catch (NestedValidationException $exception) {
            $errorPassword = $exception->findMessages([
                'equals' => 'Not identical passwords',
                'length' => '{{name}} length must be between 6-20 chars'
            ]);
        }

        if (true)
        {
            if (v::arrayType()->notEmpty()->validate($errorUsername))
            {
                if (v::notEmpty()->validate($errorUsername['noWhitespace']))
                {
                    $_SESSION['authReg_usernameError'] = $errorUsername['noWhitespace'];
                }
                elseif (v::notEmpty()->validate($errorUsername['alnum']) )
                {
                    $_SESSION['authReg_usernameError'] = $errorUsername['alnum'];
                }
                elseif (v::notEmpty()->validate($errorUsername['length']) )
                {
                    $_SESSION['authReg_usernameError'] = $errorUsername['length'];
                }
            }
            if (v::arrayType()->notEmpty()->validate($errorEmail) )
            {
                $_SESSION['authReg_emailError'] = $errorEmail['email'];
            }
            if (v::arrayType()->notEmpty()->validate($errorPassword) )
            {
                if (v::notEmpty()->validate($errorPassword['equals']) )
                {
                    $_SESSION['authReg_passwordError'] = $errorPassword['equals'];
                }
                elseif (v::notEmpty()->validate($errorPassword['length']) )
                {
                    $_SESSION['authReg_passwordError'] = $errorPassword['length'];
                }
            }
            if ( v::notEmpty()->validate($errorUsername) || v::notEmpty()->validate($errorPassword) || v::notEmpty()->validate($errorEmail) )
            {
                goto sony;
            }
        }

        //Проверка на дубликат e-mail
        if (isset($_POST['email']))
        {
            $user = $this->qb->selectOneByEmail('users', $_POST['email']);
            if (v::arrayType()->validate($user))
            {
                $_SESSION['authReg_emailError'] = 'Email already exists';
                goto sony;
            }
        }

        //Регистрация нового пользователя
        try {
            $userId = $this->auth->register(strtolower($_POST['email']), $_POST['password'], $_POST['username'], function ($selector, $token) {

                $url = 'http://level3/verify_email?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);

                SimpleMail::make()
                ->setTo($_POST['email'], $_POST['username'])
                ->setSubject('Register confirmation')
                ->setMessage("To confirm your email please go to the <a href='$url'>Link</a> or copy the link below <br>".$url)
                ->setHtml()
                ->send();

                // echo 'Send ' . $selector . ' and ' . $token . ' to the user (e.g. via email)';
            });
            echo "<br>We have signed up a new user with the ID " . $userId. "<br>Check your email to confirm";
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('User already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }

        die;
        sony:
        header("Location: /register");
        exit;
    }

    public function emailVerification()
    {
        try {
            $this->auth->confirmEmail($_GET['selector'], $_GET['token']);
            echo 'Email address has been verified';
            echo "<br> Go to login page <a href='/login'>Login</a> ";
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            die('Invalid token');
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            die('Token expired');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('Email address already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    public function logout()
    {
        $this->auth->logOut();
        header('Location: /');
        exit;
    }
}