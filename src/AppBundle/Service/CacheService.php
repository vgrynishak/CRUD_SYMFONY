<?php


namespace AppBundle\Service;


use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheService
{
    private $env;
    const CACHER_LONG_KEY = 'long';
    const CACHER_SHORT_KEY = 'short';

    private static $cachers = [
        self::CACHER_LONG_KEY  => null, //не стирается при пересборке, т.к. лежит в папке uploads
        self::CACHER_SHORT_KEY => null,
    ];

    const FILE_CACHE_DIR           =  APP_DIR . '/app/cache/cached_data';
    const FILE_CACHE_DIR_LONG_TERM =  __DIR__ . '/..' . '/web/uploads/cached_data';

    public function __construct($env)
    {
        echo self::FILE_CACHE_DIR;
        $this->env = $env;
        self::$cachers[self::CACHER_SHORT_KEY] = new FilesystemAdapter($this->env, 3600 * 24 * 7, self::FILE_CACHE_DIR);
        self::$cachers[self::CACHER_LONG_KEY]  = new FilesystemAdapter($this->env, 3600 * 24 * 7, self::FILE_CACHE_DIR_LONG_TERM);
    }


    /** @return FilesystemAdapter */
    private function getCacher($key)
    {
        return self::$cachers[$key];
    }

    public function find($key, callable $callback = null, $expiresAfter = '7 day')
    {
        return $this->findWithCacher($this->getCacher(self::CACHER_SHORT_KEY), $key, $callback, $expiresAfter);
    }

    public function findInLongTerm($key, callable $callback = null, $expiresAfter = '7 day')
    {
        return $this->findWithCacher($this->getCacher(self::CACHER_LONG_KEY), $key, $callback, $expiresAfter);
    }

    private function findWithCacher(FilesystemAdapter $cache, $key, callable $callback = null, $expiresAfter = '7 day')
    {
        $result = $cache->getItem($key);

        if ($result->isHit()) {
            return $result->get();
        }

        if ($callback) {
            if ($callbackResult = $callback()) {
                $result->set($callbackResult);
                $cache->commit();
                $result->expiresAfter(\DateInterval::createFromDateString($expiresAfter));
                $cache->save($result);

                return $callbackResult;
            }
        }

        return null;
    }

    public function save($key, $value, $expiresAfter = '7 day')
    {
        return $this->saveWithCacher($this->getCacher(self::CACHER_SHORT_KEY), $key, $value, $expiresAfter);
    }

    public function saveToLongTerm($key, $value, $expiresAfter = '7 day')
    {
        return $this->saveWithCacher($this->getCacher(self::CACHER_LONG_KEY), $key, $value, $expiresAfter);
    }


    private function saveWithCacher(FilesystemAdapter $cache, $key, $value, $expiresAfter)
    {
        $result = $cache->getItem($key);
        $result->set($value);
        $cache->commit();
        $result->expiresAfter(\DateInterval::createFromDateString($expiresAfter));
        $cache->save($result);
        return $value;
    }

    public function clear($key)
    {
        if (!is_array($key)) {
            $key = [$key];
        }
        $this->getCacher(self::CACHER_SHORT_KEY)->deleteItems($key);
        $this->getCacher(self::CACHER_LONG_KEY)->deleteItems($key);
    }

    public function incCounter($key, $expiresAfter = '1 hour')
    {
        $cache = $this->getCacher(self::CACHER_SHORT_KEY);
        $result = $cache->getItem($key);

        if ($result->isHit()) {
            $result->set($result->get() + 1);

        } else {
            $result->set(1);
            $result->expiresAfter(\DateInterval::createFromDateString($expiresAfter));
        }

        $cache->save($result);

        return $result->get();
    }
}