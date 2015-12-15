<?php namespace Arcanedev\LaravelAuth\Traits;

/**
 * Trait     Slugable
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  string  slug
 */
trait Slugable
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Slugify the given value.
     *
     * @param  string  $value
     *
     * @return string
     */
    protected function slugify($value)
    {
        return str_slug($value, config('laravel-auth.slug-separator', '.'));
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if slug is the same as the given value.
     *
     * @param  string $value
     *
     * @return bool
     */
    public function checkSlug($value)
    {
        return $this->slug === $this->slugify($value);
    }
}
