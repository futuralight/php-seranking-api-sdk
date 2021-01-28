<?php

declare(strict_types=1);

namespace Futuralight\SerankingApiSdk;

class Client
{
    protected $token;
    protected const URL_BALANCE = 'https://api4.seranking.com/account/balance';
    protected const URL_SITES = 'https://api4.seranking.com/sites';
    protected const URL_SITE_KEYWORDS = 'https://api4.seranking.com/sites/%s/keywords';
    protected const URL_SITE_STATISTICS = 'https://api4.seranking.com/sites/%s/stat';
    protected const URL_SITE_CHART = 'https://api4.seranking.com/sites/%s/chart';
    protected const URL_SITE_CHECK_DATES = 'https://api4.seranking.com/sites/%s/check-dates';
    protected const URL_SITE_RECHECK = 'https://api4.seranking.com/sites/%s/recheck';

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    protected function initGetCurl(string $url)
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => ["Authorization: Token {$this->token}"],
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
        ]);
        return $curl;
    }

    protected function initPostCurl(string $url, array $postData)
    {
        $curl = curl_init($url); //task_id 171333640
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => ["Authorization: Token {$this->token}"],
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData)
        ]);
        return $curl;
    }

    protected function closeCurlAndGetContent($curl): array
    {
        $content = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $content;
    }

    protected function getMethod(string $url)
    {
        return $this->closeCurlAndGetContent($this->initGetCurl($url));
    }

    protected function postMethod(string $url, array $fields)
    {
        return $this->closeCurlAndGetContent($this->initPostCurl($url, $fields));
    }

    public function balance()
    {
        return $this->getMethod(self::URL_BALANCE);
    }

    public function sites()
    {
        return $this->getMethod(self::URL_SITES);
    }

    public function siteKeywords(string $siteId)
    {
        return $this->getMethod(sprintf(self::URL_SITE_KEYWORDS, $siteId));
    }

    public function addKeyword(
        string $siteId,
        string $keyword,
        string $groupId,
        string $targetUrl,
        bool $isStrict = false
    ) {
        return $this->postMethod(
            sprintf(self::URL_SITE_KEYWORDS, $siteId),
            [[
                'keyword' => $keyword,
                'group_id' => $groupId,
                'target_url' => $targetUrl,
                'is_strict' => $isStrict ? 1 : 0
            ]]
        );
    }

    public function addKeywords(string $siteId, array $keywords)
    {
        return $this->postMethod(
            sprintf(self::URL_SITE_KEYWORDS, $siteId),
            $keywords
        );
    }

    public function siteStatistics(string $siteId)
    {
        return $this->getMethod(sprintf(self::URL_SITE_STATISTICS, $siteId));
    }

    public function siteChart(string $siteId)
    {
        return $this->getMethod(sprintf(self::URL_SITE_CHART, $siteId));
    }

    public function siteCheckDates(string $siteId)
    {
        return $this->getMethod(sprintf(self::URL_SITE_CHECK_DATES, $siteId));
    }

    public function siteRecheck(string $siteId, array $keywords = [])
    {
        return $this->postMethod(
            sprintf(self::URL_SITE_RECHECK, $siteId),
            [
                'keywords' => $keywords
            ]
        );
    }
}
