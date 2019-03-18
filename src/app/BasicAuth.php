<?php
/**
 * Created by IntelliJ IDEA.
 * User: jibo
 * Date: 2019-01-01
 * Time: 20:51
 */

namespace app;

use DB\Jig;

class BasicAuth
{
    function get()
    {
        $jig = new Jig(RUNTIME . '/jig/',Jig::FORMAT_JSON);
        $user = new Jig\Mapper($jig,'users');
        $auth = new \Auth($user, array('id'=>'name', 'pw'=>'password'));
        if ($auth->basic()) {
            echo 'HTTP basic authentication';
        }
    }
}
