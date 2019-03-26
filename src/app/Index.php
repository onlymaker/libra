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

    function next()
    {
        echo \Template::instance()->render('invite.html');
    }

    function status()
    {
        echo \Template::instance()->render('status.html');
    }

    function checkCode(\Base $f3)
    {
        $code = $f3->get('POST.code');
        if (strcasecmp(self::CODE, $code) === 0) {
            $customer = new SqlMapper('customer');
            $customer->maxRanking = 'max(ranking)';
            $customer->load();
            $ranking = ($customer->dry()) ? 1 : $customer['maxRanking'] + 1;
            $customer->reset();
            $customer['email'] = $f3->get('COOKIE.email');
            $customer['ranking'] = $ranking;
            $customer['code'] = $code;
            $customer['data'] = json_encode([]);
            $customer->save();
            $this->setCustomer($customer);
            echo 'success';
        } else {
            echo 'reject';
        }
    }

    function checkEmail(\Base $f3)
    {
        $email = $f3->get('POST.email');
        $customer = new SqlMapper('customer');
        $customer->load(['email=?', $email]);
        if (!$customer->dry()) {
            $this->setCustomer($customer);
            echo 'success';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !checkdnsrr(array_pop(explode('@', $email)), 'MX')) {
            setcookie('email', null, time() - 3600, '/');
            echo 'reject';
        } else {
            setcookie('email', $email, 0, '/');
            echo 'next';
        }
    }

    function setCustomer(SqlMapper $customer)
    {
        $fields = $customer->cast();
        foreach ($fields as $key => $value) {
            setcookie($key, $value, 0, '/');
        }
        $ranking = $fields['ranking'];
        setcookie('prev', $customer->count(['ranking<=? and status=0', $ranking]), 0, '/');
        setcookie('next', $customer->count(['ranking>?', $ranking]), 0, '/');
    }

    function beforeRoute(\Base $f3)
    {
        // No need to login for application
    }
}
