<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * appProdUrlGenerator
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appProdUrlGenerator extends Symfony\Component\Routing\Generator\UrlGenerator
{
    private static $declaredRoutes = array(
        'homepage' => array (  0 =>   array (    0 => 'from',    1 => 'to',    2 => 'timeSelected',  ),  1 =>   array (    '_controller' => 'Optimus\\OptimusBundle\\Controller\\HistoricController::indexAction',    'from' => '',    'to' => '',    'timeSelected' => '',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'timeSelected',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'to',    ),    2 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'from',    ),    3 =>     array (      0 => 'text',      1 => '/home',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'prediction' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Optimus\\OptimusBundle\\Controller\\PredictionController::indexAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/prediction',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'sensor' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Optimus\\OptimusBundle\\Controller\\SensorController::indexAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/sensor',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'actionPlan' => array (  0 =>   array (    0 => 'from',    1 => 'to',    2 => 'timeSelected',  ),  1 =>   array (    '_controller' => 'Optimus\\OptimusBundle\\Controller\\ActionPlanController::indexAction',    'from' => '',    'to' => '',    'timeSelected' => '',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'timeSelected',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'to',    ),    2 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'from',    ),    3 =>     array (      0 => 'text',      1 => '/action-plan',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'BloggerBlogBundle_homepage' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Blogger\\BlogBundle\\Controller\\PageController::indexAction',  ),  2 =>   array (    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'BloggerBlogBundle_about' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Blogger\\BlogBundle\\Controller\\PageController::aboutAction',  ),  2 =>   array (    '_method' => 'GET',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/about',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'BloggerBlogBundle_contact' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Blogger\\BlogBundle\\Controller\\PageController::contactAction',  ),  2 =>   array (    '_method' => 'GET|POST',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/contact',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
        'acme_hello_homepage' => array (  0 =>   array (    0 => 'name',  ),  1 =>   array (    '_controller' => 'Acme\\HelloBundle\\Controller\\DefaultController::indexAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'name',    ),    1 =>     array (      0 => 'text',      1 => '/hello',    ),  ),  4 =>   array (  ),  5 =>   array (  ),),
    );

    /**
     * Constructor.
     */
    public function __construct(RequestContext $context, LoggerInterface $logger = null)
    {
        $this->context = $context;
        $this->logger = $logger;
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if (!isset(self::$declaredRoutes[$name])) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        list($variables, $defaults, $requirements, $tokens, $hostTokens, $requiredSchemes) = self::$declaredRoutes[$name];

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }
}
