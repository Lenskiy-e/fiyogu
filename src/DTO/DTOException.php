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
     * @var bool
     */
    public bool $array = false;

    /**
     * DTOException constructor.
     * @param $message
     * @param int $code
     * @param bool $array
     */
    public function __construct($message, int $code)
    {
        if( is_array($message) ) {
            $this->array = true;
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