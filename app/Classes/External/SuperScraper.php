<?php
/**
 * Project: PhpScraperTools
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace App\Classes\External;


class SuperScraper
{
    protected   $protocol   =   'http';
    protected   $domain = '';
    protected   $last_url =   '';
    protected   $proxy  =   NULL;

    protected   $curl_verbose   = false;
    protected   $cookie_name    =   'cookie.txt';
    protected   $response_info  =   null;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        // initialize cookie_name, cache_prefix, etc..
    }

    public function verboseOn()
    {
        $this->curl_verbose =   true;
    }

    public function verboseOff()
    {
        $this->curl_verbose =   false;
    }

    public function createURL($path, $post, $JSON)
    {
        return "$this->protocol://$this->domain$path";
    }

    public function getCurl($url, $post = NULL, $isAjax = false)
    {
        if($url[0] == '/')
            $url = $this->createURL($url, $post, $isAjax);

        $curl = curl_init($url);
        $this->curl_setopt($curl);

        if($this->curl_verbose) {
//            curl_setopt($curl, CURLOPT_STDERR, fopen('php://output', 'w+'));
            curl_setopt($curl, CURLOPT_VERBOSE, 1);
        }

        if($this->proxy)
        {
            $proxy  =  $this->proxy;
            curl_setopt($curl, CURLOPT_PROXY, $proxy[0]);
            curl_setopt($curl, CURLOPT_PROXYPORT, $proxy[1]);
            if(!empty($proxy[2]))
                curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy[2]);

            curl_setopt($curl, CURLOPT_PROXYTYPE, $proxy[3]);
        }

        if($post){
            $string = is_string($post) ? $post : http_build_query($post);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $string);
        }

        $headers = $this->getRequestHeaders($post, $isAjax);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $cookiefile = $this->getCookieFileName();
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiefile);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiefile);


        $response = curl_exec($curl);

        $this->response_info    =   curl_getinfo($curl);
        $this->last_url =   $url;

        curl_close($curl);
        return $response;
    }

    public function getInfo()
    {
        return $this->response_info;
    }

    public function Post($url, $data)
    {
        return $this->getCurl($url, $data);
    }

    public function PostAjax($url, $json_string)
    {
        return $this->getCurl($url, $json_string, TRUE);
    }

    public function Get($url)
    {
        return $this->getCurl($url, NULL);
    }

    /**
     * Override to disable follow location, adding new opts, etc..
     *
     * @param $ch
     */
    protected function curl_setopt($ch)
    {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    }

    public function getCookieFileName()
    {
        return $this->cookie_name;
    }

    public function setCookieFileName($cookie_name)
    {
        $this->cookie_name = $cookie_name;
    }

    /**
     *
     * @deprecated
     * @return string
     */
    public function getCookieFilePath()
    {
        return $this->getCookieFileName();
//        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->getCookieFileName();
    }
    
    public function getDefaultHeaders()
    {
        return array(
            'Origin: ' . $this->protocol . '://' . $this->domain,
//            'Accept-Encoding: gzip, deflate',
            'Accept-Language: en-US,en;q=0.8,es;q=0.6',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36',
            'Cache-Control: max-age=0',
            'Connection: keep-alive', 'Expect: '
        );
    }

    /**
     * @return array
     */
    public function getRequestHeaders($post = NULL, $isAjax = false)
    {
        $headers = $this->getDefaultHeaders();

        $is_payload =   is_string($post);

        if($isAjax)
        {
            $headers[]  =   'Accept: application/json, text/javascript, */*; q=0.01';
            $headers[]  =   'X-Requested-With: XMLHttpRequest';
            if($is_payload)
                $headers[]  =   'Content-Type: application/json';
        }else{
            $headers[]  =   'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
        }

        if($this->last_url)
            $headers[]  =   'Referer: ' . $this->last_url;

        return $headers;
    }

    public function setProxy($proxy, $port, $userpass = '', $type = CURLPROXY_HTTP)
    {
        $this->proxy = array($proxy, $port, $userpass, $type);
    }
}
