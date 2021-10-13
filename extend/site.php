<?php

class Site
{

    static public function create($request)
    {
        Lock::lock();
        $request = $request->get();
        $id = 'site-' . $request['id'];
        $domain = $request['domain'];
        $username = $request['username'];
        $password = $request['password'];
        $email = $request['email'];

        $path = "core/www/{$id}";

        console("Creating site {$id} at {$path}...");

        if (!file_exists($path)) {
            mkdir($path);
        }

        console("Coping default folder...");

        exec("cp -R core/default/* {$path}");

        console("Creating lae.html...");

        file_put_contents($path . '/lae.html', $id);

        console("Creating caddy file...");
        $conf = <<<EOF
http://{$domain}:8080 {
#    tls $email
    root * core/www/{$id}
    file_server browse
}
EOF;
        file_put_contents("core/sites/{$id}.lae", $conf);

        console("Creating ftp account...");
        $account = json_decode(file_get_contents('ftp.json'), true);
        $account['sites'][$id] = [
            'username' => $username,
            'password' => $password,
            'path' => $path
        ];
        file_put_contents('ftp.json', json_encode($account));

        console("Created site {$id} at {$path}.");

        console("Reloading Caddy...");
        exec('./caddy reload');

        Lock::release();

        return json_encode([
            'status' => 1,
            'id' => $id,
        ]);
    }

    static public function delete($id)
    {
        Lock::lock();

        $id = 'site-' . $id;
        $path = "core/www/{$id}";

        $account = json_decode(file_get_contents('ftp.json'), true);
        unset($account['sites'][$id]);
        unlink("core/sites/{$id}.lae");
        file_put_contents('ftp.json', json_encode($account));
        exec("rm -rf {$path}");

        console("Reloading Caddy...");
        exec('./caddy reload');

        Lock::release();

        return json_encode([
            'status' => 1,
            'id' => $id,
        ]);
    }

    static public function count()
    {
        Lock::lock();
    }
}
