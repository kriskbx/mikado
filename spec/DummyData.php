<?php

use Corcel\Database;
use kriskbx\mikado\Manager;

class DummyData
{
    public function __construct()
    {
        self::connect();
    }

    /**
     * Get stdClass dummy data.
     *
     * @param bool $formatAble
     *
     * @return stdClass
     */
    public function getStdClass($formatAble = false)
    {
        $object = new stdClass();
        $object->testProperty = 'test';
        $object->nullProperty = null;
        $object->arrayProperty = [
            'level1' => [
                'level2a' => null,
                'level2' => [
                    'level3' => 'value',
                    'level3b' => null,
                ],
            ],
        ];

        if ($formatAble) {
            $object = Manager::formatAble($object);
        }

        return $object;
    }

    /**
     * Get stdClass dummy data with array.
     *
     * @param bool|false $formatAble
     *
     * @return stdClass
     */
    public function getStdClassWithArray($formatAble = false)
    {
        $object = new stdClass();
        $object->array = [
            [
                'key' => 'value',
                'key2' => 'value2',
            ],
        ];

        if ($formatAble) {
            $object = Manager::formatAble($object);
        }

        return $object;
    }

    /**
     * Get WordPress dummy data.
     *
     * @param bool $formatAble
     *
     * @return object
     */
    public function getPost($formatAble = false)
    {
        $post = Post::published()->first();
        $post = self::fix($post);

        if ($formatAble) {
            $post = Manager::formatAble($post);
        }

        return $post;
    }

    /**
     * Get multiple WordPress data.
     *
     * @param bool|false $formatAble
     *
     * @return mixed
     */
    public function getMultiplePosts($formatAble = false)
    {
        $posts = Post::published()->get();

        foreach ($posts as $key => $post) {
            $posts[$key] = self::fix($post);

            if ($formatAble) {
                $posts[$key] = Manager::formatAble($posts[$key]);
            }
        }

        return $posts;
    }

    /**
     * The SQLite database stores datetime values including microseconds. WordPress does not.
     * So toArray() and toJson() will fail in the tests if we don't fix that.
     * Carbon wants a specific format and gets something else thus throws an Exception.
     *
     * @param object $object
     *
     * @return object
     */
    public function fix($object)
    {
        $reflection = new ReflectionObject($object);

        $attributeProperty = $reflection->getProperty('attributes');
        $attributeProperty->setAccessible(true);
        $attributes = $attributeProperty->getValue($object);

        $dateProperty = $reflection->getProperty('dates');
        $dateProperty->setAccessible(true);
        $dates = $dateProperty->getValue($object);

        foreach ($dates as $date) {
            $attributes[$date] = str_replace('.000000', '', $attributes[$date]);
        }

        $attributeProperty->setValue($object, $attributes);

        return $object;
    }

    /**
     * Connect to the dummy database.
     */
    public function connect()
    {
        Database::connect([
            'driver' => 'sqlite',
            'database' => __DIR__.'/dummy.sqlite',
            'prefix' => 'wp_',
        ]);
    }
}
