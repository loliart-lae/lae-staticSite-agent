<?php

use Workerman\Protocols\Http\Response;

class Exec
{
    static public function route($request)
    {
        switch ($request->path()) {
            case '/site/create':
                return Site::create($request);
                break;

            case '/site/delete':
                return Site::delete($request->get('id'));
                break;

            case '/site/count':
                return Site::count();
                break;

            case '/sleep':
                return sleep(600);

                break;
            default:
                return 404;
                break;
        }
    }
}
