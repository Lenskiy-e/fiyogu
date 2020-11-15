<?php
declare(strict_types=1);

namespace App\Mailer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Twig\Environment;

/**
 * Class Mailer
 * @package App\Mailer
 */
abstract class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    protected \Swift_Mailer $mailer;
    /**
     * @var Environment
     */
    protected Environment $twig;
    /**
     * @var string
     */
    protected string $from;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $twig
     * @param ContainerInterface $container
     */
    public function __construct
    (
     \Swift_Mailer $mailer,
     Environment $twig,
     ContainerInterface $container
    )
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $container->getParameter('admin_email');
    }
}