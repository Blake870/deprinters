<?php

namespace Swissup\SeoCore\Model;

use Cocur\Slugify\Slugify;

class Slug
{
    /**
     * Slugify
     * @var null
     */
    protected $instance = null;

    /**
     * @param  string $string
     * @return string
     */
    public function slugify($string)
    {
        $instance = $this->getInstance();

        return $instance ? $instance->slugify($string) : $this->fallback($string);
    }

    /**
     * Get slugify instance.
     *
     * @return Slugify|null
     */
    protected function getInstance()
    {
        if (class_exists(Slugify::class) && !$this->instance) {
            $this->instance = new Slugify();
        }

        return $this->instance;
    }

    /**
     * Fallback when slugify is not installed.
     *
     * @param  string $string
     * @return string
     */
    protected function fallback($string)
    {
        // remove leading and trailing spaces
        $string = trim($string);
        // source - https://stackoverflow.com/questions/11330480/strip-php-variable-replace-white-spaces-with-dashes
        // Lower case everything
        $string = strtolower($string);
        // decode html entities to utf8
        $string = html_entity_decode($string);
        // Remove & and .
        $string = preg_replace("/[&.'+]/", " ", $string);
        // Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        // Convert whitespaces, underscore and slash to dash
        $string = preg_replace("/[\s_\/]/", "-", $string);

        return $string;
    }
}
