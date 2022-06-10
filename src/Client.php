<?php

declare(strict_types=1);

namespace Futuralight\SerankingApiSdk;

use CurlHandle;
use function json_encode, json_decode, sprintf;
use function curl_exec, curl_init, curl_setopt_array, curl_close;

/**
 * Client
 */
class Client
{
    /**
     * Api token
     */
    protected string $token;
    /**
     * Method url's
     */
    protected const URL_BALANCE = 'https://api4.seranking.com/account/balance';
    protected const URL_SITES = 'https://api4.seranking.com/sites';
    protected const URL_SITE_KEYWORDS = 'https://api4.seranking.com/sites/%s/keywords';
    protected const URL_SITE_STATISTICS = 'https://api4.seranking.com/sites/%s/stat';
    protected const URL_SITE_CHART = 'https://api4.seranking.com/sites/%s/chart';
    protected const URL_SITE_CHECK_DATES = 'https://api4.seranking.com/sites/%s/check-dates';
    protected const URL_SITE_RECHECK = 'https://api4.seranking.com/sites/%s/recheck';
    protected const URL_SITE_POSITIONS = 'https://api4.seranking.com/sites/%s/positions?';
    protected const URL_SYSTEM_SEARCH_ENGINES = 'https://api4.seranking.com/system/search-engines';
    protected const URL_SITES_SEARCH_ENGINES = 'https://api4.seranking.com/sites/%s/search-engines';
    protected const URL_SYSTEM_YANDEX_REGIONS = 'https://api4.seranking.com/system/yandex-regions';
    protected const URL_SYSTEM_VOLUME = 'https://api4.seranking.com/system/yandex-regions';
    protected const URL_KEYWORD_GROUPS = 'https://api4.seranking.com/keyword-groups';
    protected const URL_KEYWORD_GROUPS_KEYWORDS = 'https://api4.seranking.com/keyword-groups/%s/keywords';
    protected const URL_SITE_KEYWORDS_KEYWORD_ID = 'https://api4.seranking.com/sites/%s/keywords/%s';

    /**
     * __construct
     *
     * @param  string $token api token
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * initGetCurl
     *
     * @param  string $url
     * @return CurlHandle
     */
    protected function initGetCurl(string $url): CurlHandle|false
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => ["Authorization: Token {$this->token}"],
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
        ]);
        return $curl;
    }

    /**
     * initPostCurl
     *
     * @param  string $url
     * @param  array $postData
     * @return CurlHandle
     */
    protected function initPostCurl(string $url, array $postData): CurlHandle|false
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => ["Authorization: Token {$this->token}"],
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData)
        ]);
        return $curl;
    }

    /**
     * initDeleteCurl
     *
     * @param  string $url
     * @return CurlHandle
     */
    protected function initDeleteCurl(string $url): CurlHandle|false
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => ["Authorization: Token {$this->token}"],
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE'
        ]);
        return $curl;
    }

    /**
     * initPatchCurl
     *
     * @param  string $url
     * @param  array $patchData
     * @return CurlHandle
     */
    protected function initPatchCurl(string $url, array $patchData): CurlHandle|false
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => ["Authorization: Token {$this->token}"],
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => json_encode($patchData)
        ]);
        return $curl;
    }

    /**
     * closeCurlAndGetContent
     *
     * @param  CurlHandle $curl
     * @return array
     */
    protected function closeCurlAndGetContent(CurlHandle $curl): array
    {
        $content = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $content;
    }

    /**
     * getMethod GET HTTP method
     *
     * @param  string $url
     * @return array
     */
    protected function getMethod(string $url): array
    {
        return $this->closeCurlAndGetContent($this->initGetCurl($url));
    }

    /**
     * postMethod POST HTTP method
     *
     * @param  string $url
     * @param  array $fields
     * @return array
     */
    protected function postMethod(string $url, array $fields): array
    {
        return $this->closeCurlAndGetContent($this->initPostCurl($url, $fields));
    }

    /**
     * patchMethod PATCH HTTP method
     *
     * @param  string $url
     * @param  array $fields
     * @return array
     */
    protected function patchMethod(string $url, array $fields): array
    {
        return $this->closeCurlAndGetContent($this->initPatchCurl($url, $fields));
    }

    /**
     * deleteMethod DELETE HTTP method
     *
     * @param  string $url
     * @return array
     */
    protected function deleteMethod(string $url): array
    {
        return $this->closeCurlAndGetContent($this->initDeleteCurl($url));
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
        string $targetUrl = '',
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

    public function siteSearchEngines(string $siteId)
    {
        return $this->getMethod(sprintf(self::URL_SITES_SEARCH_ENGINES, $siteId));
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

    /**
     * sitePositions 
     *
     * @param  string $siteId
     * @param  array $params
     * @return void
     */
    public function sitePositions(string $siteId, array $params): array
    {
        return $this->getMethod(sprintf(self::URL_SITE_POSITIONS, $siteId) . http_build_query($params));
    }

    public function createKeywordsGroup(string $siteId, string $name)
    {
        return $this->postMethod(
            self::URL_KEYWORD_GROUPS,
            [
                'name' => $name,
                'site_id' => $siteId
            ]
        );
    }

    public function getKeywordGroup(string $siteId): array
    {
        return $this->getMethod(
            self::URL_KEYWORD_GROUPS . sprintf('/%s', $siteId),
        );
    }

    public function changeKeyword(string $siteId, string $keywordId, string $keyword, string $targetUrl): array
    {
        $data = [];
        if ($keyword) {
            $data['keyword'] = $keyword;
        }
        if ($targetUrl) {
            $data['target_url'] = $targetUrl;
        }
        return $this->patchMethod(
            sprintf(self::URL_SITE_KEYWORDS_KEYWORD_ID, $siteId, $keywordId),
            $data
        );
    }

    public function moveKeywordsToGroup(string $groupId, array $keywordsIds): array
    {
        return $this->postMethod(
            sprintf(self::URL_KEYWORD_GROUPS_KEYWORDS, $groupId),
            [
                'keywords_ids' => $keywordsIds
            ]
        );
    }

    public function deleteKeywords(string $siteId, array $keywordsIds): array
    {
        $paramsString = '?';
        foreach ($keywordsIds as $keywordId) {
            $paramsString .= "keywords_ids[]={$keywordId}&";
        }
        return $this->deleteMethod(sprintf(self::URL_SITE_KEYWORDS . $paramsString, $siteId));
    }
}
