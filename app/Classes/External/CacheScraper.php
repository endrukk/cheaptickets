<?php
/**
 * Project: PhpScraperTools
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace App\Classes\External;


class CacheScraper extends SuperScraper
{
    protected   $use_cache      = false;
    protected   $cache_dir      =   '';
    protected   $cache_prefix   =   'scrape';

    public function cacheOn()
    {
        $this->use_cache    =   true;
    }

    public function cacheOff()
    {
        $this->use_cache    =   false;
    }

    public function setCachePrefix($prefix)
    {
        $this->cache_prefix =   $prefix;
    }

    public function setCacheDir($cache_dir)
    {
        $this->cache_dir = rtrim($cache_dir, '/') . '/';
    }

    public function getCacheDir()
    {
        if(!$this->cache_dir)
            $this->setCacheDir(sys_get_temp_dir() . DIRECTORY_SEPARATOR);

        return $this->cache_dir;
    }

    public function cacheFilename($url, $post, $JSON)
    {
        return md5($url . print_r($post, true)) . ($JSON ? '.json ' : '.html');
    }

    public function cache_get($url, $post = NULL, $JSON = false, $disable_cache = false)
    {
        if(!$this->use_cache || $disable_cache) return $this->getCurl($url, $post, $JSON);

        $cachefile = $this->getCacheDir() . $this->cache_prefix . "-" . $this->cacheFilename($url, $post, $JSON);
        if(!file_exists($cachefile))
        {
            $content = $this->getCurl($url, $post, $JSON);
            if($content)
                file_put_contents($cachefile, $content);
        }else {
            if($this->curl_verbose)
            {
                echo "Cache: $url " . print_r($post, true), "\n";
            }
            $content = file_get_contents($cachefile);
        }

        return $content;
    }
}