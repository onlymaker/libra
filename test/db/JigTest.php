<?php
/**
 * Created by IntelliJ IDEA.
 * User: jibo
 * Date: 2019-01-01
 * Time: 20:45
 */

namespace test\db;

use DB\Jig;
use PHPUnit\Framework\TestCase;

class JigTest extends TestCase
{
    function testUser()
    {
        $db = new Jig(RUNTIME . '/jig/',Jig::FORMAT_JSON);
        $user = new Jig\Mapper($db,'users');
        $user->load(['@name=?', 'user']);
        if ($user->dry()) {
            $user['name'] = 'user';
            $user['password'] = '123456';
            $user->save();
        }
        $this->assertNotTrue($user->dry());
        print_r($user->cast());
    }
}
