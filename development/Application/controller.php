<?php

namespace ClickBlocks\MVC;

require_once(__DIR__ . '/connect.php');

use ClickBlocks\Core;
use ClickBlocks\Utils;
use ClickBlocks\Web;


class Controller extends MVC
{
    private static $pages = array(
        //'backend' => array('/' => 'PageUsers',
        'backend' => array('/' => 'PageMapQueue',
            '/login' => 'PageLogin',
            '/forgot' => 'PageForgot',
            '/users' => 'PageUsers',
            '/users/add' => 'PageAddUser',
            '/customers' => 'PageCustomers',
            '/customers/edit' => 'PageCustomerEdit',
            '/map/queue' => 'PageMapQueue',
            '/map/search' => 'PageMapSearch',
            '/map/compare' => 'PageMapCompare',
        ),
        'frontend' => array()
    );

    public function __construct()
    {
        parent::__construct();
        Web\XHTMLParser::$controls['CUSTOMDROPDOWNBOX'] = 1;
        Web\XHTMLParser::$controls['POPUP'] = 1;
    }

    public function getPage()
    {
        $config = $this->reg->config;
        if ($config->cms == '/' . $this->uri->path[0]) // Backend
        {
            unset($this->uri->path[0]);
            $path = $this->uri->getPath();
            $page = self::$pages['backend'][$path];
            if ($page) {
                $page = 'ClickBlocks\MVC\Backend\\' . $page;
                return new $page();
            }
        } else // Frontend
        {
            Web\JS::goURL('/admin/map/queue');#	  }
        }
        header("HTTP/1.0 404 Not Found");
        readfile(Core\IO::dir('application') . '/errors/404.html');
        exit;
    }

    public function getPagesss()
    {
        $page = self::$pages[$this->uri->getPath()];

        if ($page) {
            $page = 'ClickBlocks\MVC\\' . $page;
            return new $page();
        }
        header("HTTP/1.0 404 Not Found");
        readfile(Core\IO::dir('application') . '/errors/404.html');
        exit;
    }
}

foo(new Controller())->execute();

?>