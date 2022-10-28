<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Gregwar\Captcha\CaptchaBuilder;


class UserController extends Controller
{
    public function login() {
        if(!empty(session('account'))) {
            return redirect("home");
        }
        $builder = new CaptchaBuilder;
        $builder->build();
        session(['phrase'=> $builder->getPhrase()]);
        return View('user/login', ['builder' => $builder]);
    }

    public function logout() {
        session(['account' => '']);
        return redirect("login");
    }

    public function changePassword() {
        return View('user/changePassword');
    }

    public function storePassword(Request $request) {
        $userInfo = session('account');
        $username = $userInfo['username'];
        $userModel = new User();
        $user = $userModel->where('username', $username)->first();
        if(empty($user)) {
            return redirect("user/change/password");
        }
        if(md5($request->get('old_password')) != $user->password) {
            $js = <<<JS
            <script>
            alert('原密码错误！');
            window.location = "/user/change/password";
            </script>
            JS;
            return $js;
        }
        if($request->get('password') != $request->get('confirm_password')) {
            $js = <<<JS
            <script>
            alert('新密码两次输入不一致！');
            window.location = "/user/change/password";
            </script>
            JS;
            return $js;
        }
        $userModel->where('username', $username)->update([
            'password' => md5($request->get('password')),
        ]);
        $js = <<<JS
            <script>
            alert('修改成功！');
            window.location = "/user/change/password";
            </script>
            JS;
        return $js;
    }

    public function image() {
        $builder = new CaptchaBuilder;
        $builder->build();
        session(['phrase'=> $builder->getPhrase()]);
        header('Content-type: image/jpeg');
        $builder->output();
    }
}
