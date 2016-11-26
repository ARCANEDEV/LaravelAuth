<?php namespace Arcanedev\LaravelAuth\Models\Traits;

/**
 * Class     Activatable
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  bool  is_active
 *
 * @method    bool  save(array $options = [])
 */
trait Activatable
{
    /* ------------------------------------------------------------------------------------------------
     |  CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Activate the model.
     *
     * @param  bool  $save
     *
     * @return bool
     */
    public function activate($save = true)
    {
        return $this->switchActive(true, $save);
    }

    /**
     * Deactivate the model.
     *
     * @param  bool  $save
     *
     * @return bool
     */
    public function deactivate($save = true)
    {
        return $this->switchActive(false, $save);
    }

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
        $this->is_active = boolval($active);

        return $save ? $this->save() : false;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if the model is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }
}
