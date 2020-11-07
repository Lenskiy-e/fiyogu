<?php
declare(strict_types=1);
namespace App\DTO;

use Throwable;

/**
 * Class DTOException
 * @package App\DTO
 */
class DTOException extends \Exception
{
    /**
     * DTOException constructor.
     * @param $message
     * @param int $code
     * @param bool $array
     */
    public function __construct($message, int $code, bool $array = false)
    {
        if($array) {
            $message = json_encode($message);
        }
        parent::__construct($message,$code);
    }

    /**
     * @return mixed
     */
    public function getArrayErrors()
    {
        return json_decode($this->message, true);
    }
}