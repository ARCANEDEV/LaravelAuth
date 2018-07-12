<?php namespace Arcanedev\LaravelAuth\Models\Traits;

/**
 * Class     Activatable
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  bool                 is_active
 * @property  \Carbon\Carbon|null  activated_at
 *
 * @method    bool  save(array $options = [])
 */
trait Activatable
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the `is_active` attribute.
     *
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        return $this->isActive();
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Activate/deactivate the model.
     *
     * @param  bool  $active
     * @param  bool  $save
     *
     * @return bool
     */
    protected function switchActive($active, $save = true)
    {
        $this->forceFill(['activated_at' => boolval($active) ? now() : null]);

        return $save ? $this->save() : false;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the model is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return ! is_null($this->activated_at);
    }
}
