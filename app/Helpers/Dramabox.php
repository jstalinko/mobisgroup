<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class Dramabox
{
    public static function replaceEnv(string $search, string $replace): void
    {
        $env_path = base_path('.env');

        if (File::exists($env_path)) {
            $env_content = File::get($env_path);

            if (strpos($env_content, $search) !== false) {
                $env_content = preg_replace("/^{$search}.*$/m", $replace, $env_content);
            } else {
                $env_content .= "\n{$replace}\n";
            }

            File::put($env_path, $env_content);
        } else {
            throw new \Exception('File .env tidak ditemukan.');
        }
    }

    public static function getEnv(string $key): ?string
    {
        $env_path = base_path('.env');

        if (File::exists($env_path)) {
            $env_content = File::get($env_path);

            if (preg_match("/^{$key}=(.*)$/m", $env_content, $matches)) {
                return str_replace("\r", "", $matches[1]);
            }
        }

        return null;
    }

    public static function getProxies()
    {
        $filePath = app_path('Helpers/Proxies');
        if (file_exists($filePath)) {
            try {
                $proxies = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $selectProxy = explode(":", $proxies[array_rand($proxies)]);
                $ip = $selectProxy[0];
                $port = $selectProxy[1];
                $username = $selectProxy[2];
                $password = $selectProxy[3];
                return "http://$username:$password@$ip:$port";
            } catch (\Throwable $th) {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getTokens()
    {
        $filePath = app_path('Helpers/Tokens');
        if (file_exists($filePath)) {
            $tokens = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            return $tokens[array_rand($tokens)];
        } else {
            return null;
        }
    }
}
