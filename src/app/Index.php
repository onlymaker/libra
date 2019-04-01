<?php

namespace app;

use app\common\AppBase;
use db\SqlMapper;

class Index extends AppBase
{
    const CODE = 'W0399';

    function get()
    {
        echo \Template::instance()->render('index.html');
    }

    function next(\Base $f3)
    {
        $email = $f3->get('GET.email');
        $f3->set('email', $email);
        echo \Template::instance()->render('invite.html');
    }

    function status(\Base $f3)
    {
        $email = $f3->get('GET.email');
        $customer = new SqlMapper('customer');
        $customer->load(['email=?', $email]);
        if ($customer->dry()) {
            header("location:{$f3->BASE}/");
        } else {
            $f3->set('customer', $customer);
            $f3->set('prev', $customer->count(['ranking<? and status=0', $customer['ranking']]));
            $f3->set('next', $customer->count(['ranking>?', $customer['ranking']]));
            $f3->set('email', $email);
            echo \Template::instance()->render('status.html');
        }
    }

    function checkCode(\Base $f3)
    {
        $email = $f3->get('POST.email');
        $code = $f3->get('POST.code');
        if (strcasecmp(self::CODE, $code) === 0) {
            $customer = new SqlMapper('customer');
            $customer->maxRanking = 'max(ranking)';
            $customer->load();
            $ranking = ($customer->dry()) ? 1 : $customer['maxRanking'] + 1;
            $customer->reset();
            $customer['email'] = $email;
            $customer['ranking'] = $ranking;
            $customer['code'] = $code;
            $customer['data'] = json_encode([]);
            $customer->save();
            echo 'success';
        } else {
            echo 'reject';
        }
    }

    function checkEmail(\Base $f3)
    {
        $email = $f3->get('POST.email');
        setcookie('email', $email, 0, '/');
        $customer = new SqlMapper('customer');
        $customer->load(['email=?', $email]);
        if (!$customer->dry()) {
            echo 'success';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !checkdnsrr(array_pop(explode('@', $email)), 'MX')) {
            echo 'reject';
        } else {
            echo 'next';
        }
    }

    function beforeRoute(\Base $f3)
    {
        // No need to login for application
    }
}
