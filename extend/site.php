<?php

class Site
{

    static public function create($request)
    {
        Lock::lock();
        $request = $request->get();
        $real_id = $request['id'];
        $id = 'site-' . $real_id;
        $domain = $request['domain'];
        $username = $request['username'];
        $password = md5($request['password']);
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
{$domain} {
    tls $email
    root * core/www/{$id}
    file_server
}
EOF;
        file_put_contents("core/sites/{$id}.lae", $conf);

        console("Creating ftp account...");
        $account = json_decode(file_get_contents('ftp.json'), true);
        $account['sites'][$id] = [
            'id' => $real_id,
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

        console("Counting sites folder size...");

        $dirs = json_decode(file_get_contents('ftp.json'), true);

        $arr = [];

        foreach ($dirs['sites'] as $dir) {
            $arr['info'][] = [
                'id' => $dir['id'],
                'size' => getRealSize(getDirSize($dir['path']))
            ];
        }

        $output = ['status' => 1, 'data' => $arr];
        Lock::release();
        return json_encode($output);
    }
}
