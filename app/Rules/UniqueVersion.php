<?php


namespace App\Rules;


use App\Models\ClientVersion;

class UniqueVersion extends BusinessRule
{
    private $version;
    private $clientId;

    /**
     * UniqueVersion constructor.
     * @param $version
     * @param $clientId
     */
    public function __construct($version, $clientId)
    {
        $this->version = $version;
        $this->clientId = $clientId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @return bool
     */
    public function passes()
    {
        return !ClientVersion::where('client_id', $this->clientId)->where('version', $this->version)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Такая версия уже существует';
    }
}