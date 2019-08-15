<?php
/**
 * Dom Parser
 */
namespace App\Html;

use App\Exceptions\NoDownloadLinkException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Parser
 * @package App\Html
 */
class Parser
{
    /**
     * Gets the token input.
     *
     * @param $html
     *
     * @return string
     */
    public static function getToken($html)
    {
        $parser = new Crawler($html);

        return $parser->filter("input[name=_token]")->attr('value');
    }

    /**
     * Gets the download link.
     *
     * @param $html
     * @return string
     * @throws NoDownloadLinkException
     */
    public static function getDownloadLink($html)
    {
        preg_match_all('/download-link="(.*)"/', $html, $matches);

        /*
        echo '***html***';
        echo $html;

        echo '***matches***';
        echo print_r($matches,true);

        echo '***link***';
        echo print_r($matches[1][0],true);

        die();
        */

        if(isset($matches[1][0]) === false) {
            throw new NoDownloadLinkException();
        }

        return LARACASTS_BASE_URL . $matches[1][0];
    }

    /**
     * Extracts the name of the episode.
     *
     * @param $html
     *
     * @param $path
     * @return string
     */
    public static function getNameOfEpisode($html, $path)
    {
        $parser = new Crawler($html);
        $t = $parser->filter("a[href='/".$path."'] h6")->text();

        return trim($t);
    }

    public static function getSeriesArray($html)
    {
        $parser = new Crawler($html);

        $seriesNodes = $parser->filter(".series-card");

        $series = $seriesNodes->each(function(Crawler $crawler) {
            $slug = str_replace('/series/', '', $crawler->filter('a.tw-block')->attr('href'));
            $episode_count = (int) $crawler->filter('.card-bottom .card-stats div div.tw-text-xs.tw-font-semibold')->text();

            return [
                'slug' => $slug,
                'episode_count' => $episode_count,
            ];
        });

        return $series;
    }
}
