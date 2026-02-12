<?php

namespace App\Controllers;

class Test extends BaseController
{
    public function hash()
    {
        echo password_hash('admin123', PASSWORD_DEFAULT);
    }
}
