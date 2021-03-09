<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Flash;

/**
 * Menu controller
 *
 * PHP version 7.0
 */

class Menu extends Authenticated
{
    /**
     * Before filter - called before each action method
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * Show the profile
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Menu/index.html', [
            'user' => $this->user
        ]);
    }

}