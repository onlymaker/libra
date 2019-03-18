<?php

namespace app;

use app\common\AppBase;
use db\SqlMapper;

class Admin extends AppBase
{
    const size = 20;

    function index()
    {
        header('location:admin/customer');
    }

    function customer(\Base $f3)
    {
        $status = $_GET['status'] ?? '';
        if (in_array($status, ['0', '1'], true)) {
            $filter = ['status=?', $status];
        } else {
            $filter = null;
        }
        $pageNo = $_GET['pageNo'] ?? 1;
        $pos = $pageNo - 1;
        $customer = new SqlMapper('customer');
        $page = $customer->paginate($pos, self::size, $filter, ['order' => 'status, ranking'], 0, false);
        $subset = $page['subset'];
        foreach ($subset as &$item) {
            $item['json'] = json_decode($item['data'], true);
        }
        $f3->set('pageNo', $pageNo);
        $f3->set('pageCount', $page['count']);
        $f3->set('data', $subset);
        $f3->set('status', $status);
        echo \Template::instance()->render('customer.html');
    }

    function setCustomerFinish(\Base $f3)
    {
        $id = $f3->get('GET.id');
        $customer = new SqlMapper('customer');
        $customer->load(['id=?', $id]);
        if (!$customer->dry()) {
            $customer['status'] = 1;
            $customer->save();
            echo 'success';
            return;
        }
        echo 'failure';
    }
}
