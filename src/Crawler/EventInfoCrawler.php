<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\LastFm\Crawler;

use Symfony\Component\DomCrawler\Crawler;

final class EventInfoCrawler extends AbstractCrawler
{
    /**
     * Get all event information.
     *
     * @param int $id
     *
     * @return array|null
     */
    public function getEventInfo(int $id): ? array
    {
        $node = $this->crawlEvent($id);

        if (null === $node) {
            return null;
        }

        $timeNode = $node->filter('.qa-event-date');

        return array(
            'title'       => $this->parseString($node->filter('h1.header-title')),
            'image'       => $this->parseImage($node->filter('.event-poster-preview')),
            'festival'    => $node->filter('.namespace--events_festival_overview')->count() > 0,
            'startDate'   => $this->parseDate($timeNode->filter('[itemprop="startDate"]')),
            'endDate'     => $this->parseDate($timeNode->filter('[itemprop="endDate"]')),
            'description' => $this->parseString($node->filter('.qa-event-description'), true),
            'website'     => $this->parseString($node->filter('.qa-event-link a')),
            'url'         => $this->parseUrl($node->filter('link[rel="canonical"]')),
            'eventId'     => $id,
            'venue'       => $this->readVenues($node),
            'bands'       => $this->readBands($node),
        );
    }

    /**
     * @param Crawler $node
     *
     * @return array
     */
    private function readBands(Crawler $node): array
    {
        $bandNode = $node->filter('.grid-items');

        return $bandNode->filter('.grid-items-item')->each(function (Crawler $node): array {
            return array(
                'image' => $this->parseImage($node->filter('.grid-items-cover-image-image img')),
                'name'  => $this->parseString($node->filter('.grid-items-item-main-text')),
                'url'   => $this->parseUrl($node->filter('.grid-items-item-main-text a')),
            );
        });
    }

    /**
     * @param Crawler $node
     *
     * @return array
     */
    private function readVenues(Crawler $node): array
    {
        $venueNode   = $node->filter('.event-detail');
        $addressNode = $venueNode->filter('.event-detail-address');

        return array(
            'name'      => $this->parseString($venueNode->filter('[itemprop="name"]')),
            'telephone' => $this->parseString($venueNode->filter('.event-detail-tel span')),
            'url'       => $this->parseUrl($venueNode->filter('.event-detail-web a')),
            'address'   => array(
                'streetAddress'   => $this->parseString($addressNode->filter('[itemprop="streetAddress"]')),
                'addressLocality' => $this->parseString($addressNode->filter('[itemprop="addressLocality"]')),
                'postalCode'      => $this->parseString($addressNode->filter('[itemprop="postalCode"]')),
                'addressCountry'  => $this->parseString($addressNode->filter('[itemprop="addressCountry"]')),
            ),
        );
    }

    /**
     * @param int $id
     *
     * @return Crawler|null
     */
    private function crawlEvent(int $id): ? Crawler
    {
        $url = 'http://www.last.fm/de/event/'.$id;

        return $this->crawl($url);
    }
}
