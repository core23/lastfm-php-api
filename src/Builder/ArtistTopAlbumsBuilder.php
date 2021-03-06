<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\LastFm\Builder;

final class ArtistTopAlbumsBuilder
{
    /**
     * @var array
     */
    private $query;

    private function __construct()
    {
        $this->query = [];
    }

    /**
     * @return ArtistTopAlbumsBuilder
     */
    public static function forArtist(string $artist): self
    {
        $builder = new static();

        $builder->query['artist'] = $artist;

        return $builder;
    }

    /**
     * @return ArtistTopAlbumsBuilder
     */
    public static function forMbid(string $mbid): self
    {
        $builder = new static();

        $builder->query['mbid'] = $mbid;

        return $builder;
    }

    /**
     * @return ArtistTopAlbumsBuilder
     */
    public function autocorrect(bool $autocorrect): self
    {
        $this->query['autocorrect'] =  $autocorrect ? 1 : 0;

        return $this;
    }

    /**
     * @return ArtistTopAlbumsBuilder
     */
    public function limit(int $limit): self
    {
        $this->query['limit'] = $limit;

        return $this;
    }

    /**
     * @return ArtistTopAlbumsBuilder
     */
    public function page(int $page): self
    {
        $this->query['page'] = $page;

        return $this;
    }

    public function getQuery(): array
    {
        return $this->query;
    }
}
