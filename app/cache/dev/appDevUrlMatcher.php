<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * appDevUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appDevUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);
        $context = $this->context;
        $request = $this->request;

        if (0 === strpos($pathinfo, '/_')) {
            // _wdt
            if (0 === strpos($pathinfo, '/_wdt') && preg_match('#^/_wdt/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => '_wdt')), array (  '_controller' => 'web_profiler.controller.profiler:toolbarAction',));
            }

            if (0 === strpos($pathinfo, '/_profiler')) {
                // _profiler_home
                if (rtrim($pathinfo, '/') === '/_profiler') {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($pathinfo.'/', '_profiler_home');
                    }

                    return array (  '_controller' => 'web_profiler.controller.profiler:homeAction',  '_route' => '_profiler_home',);
                }

                if (0 === strpos($pathinfo, '/_profiler/search')) {
                    // _profiler_search
                    if ($pathinfo === '/_profiler/search') {
                        return array (  '_controller' => 'web_profiler.controller.profiler:searchAction',  '_route' => '_profiler_search',);
                    }

                    // _profiler_search_bar
                    if ($pathinfo === '/_profiler/search_bar') {
                        return array (  '_controller' => 'web_profiler.controller.profiler:searchBarAction',  '_route' => '_profiler_search_bar',);
                    }

                }

                // _profiler_purge
                if ($pathinfo === '/_profiler/purge') {
                    return array (  '_controller' => 'web_profiler.controller.profiler:purgeAction',  '_route' => '_profiler_purge',);
                }

                // _profiler_info
                if (0 === strpos($pathinfo, '/_profiler/info') && preg_match('#^/_profiler/info/(?P<about>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_info')), array (  '_controller' => 'web_profiler.controller.profiler:infoAction',));
                }

                // _profiler_phpinfo
                if ($pathinfo === '/_profiler/phpinfo') {
                    return array (  '_controller' => 'web_profiler.controller.profiler:phpinfoAction',  '_route' => '_profiler_phpinfo',);
                }

                // _profiler_search_results
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/search/results$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_search_results')), array (  '_controller' => 'web_profiler.controller.profiler:searchResultsAction',));
                }

                // _profiler
                if (preg_match('#^/_profiler/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler')), array (  '_controller' => 'web_profiler.controller.profiler:panelAction',));
                }

                // _profiler_router
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/router$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_router')), array (  '_controller' => 'web_profiler.controller.router:panelAction',));
                }

                // _profiler_exception
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/exception$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_exception')), array (  '_controller' => 'web_profiler.controller.exception:showAction',));
                }

                // _profiler_exception_css
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/exception\\.css$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_exception_css')), array (  '_controller' => 'web_profiler.controller.exception:cssAction',));
                }

            }

            if (0 === strpos($pathinfo, '/_configurator')) {
                // _configurator_home
                if (rtrim($pathinfo, '/') === '/_configurator') {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($pathinfo.'/', '_configurator_home');
                    }

                    return array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::checkAction',  '_route' => '_configurator_home',);
                }

                // _configurator_step
                if (0 === strpos($pathinfo, '/_configurator/step') && preg_match('#^/_configurator/step/(?P<index>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_configurator_step')), array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::stepAction',));
                }

                // _configurator_final
                if ($pathinfo === '/_configurator/final') {
                    return array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::finalAction',  '_route' => '_configurator_final',);
                }

            }

            // _twig_error_test
            if (0 === strpos($pathinfo, '/_error') && preg_match('#^/_error/(?P<code>\\d+)(?:\\.(?P<_format>[^/]++))?$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => '_twig_error_test')), array (  '_controller' => 'twig.controller.preview_error:previewErrorPageAction',  '_format' => 'html',));
            }

        }

        if (0 === strpos($pathinfo, '/log')) {
            if (0 === strpos($pathinfo, '/login')) {
                // login
                if ($pathinfo === '/login') {
                    return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\LoginController::loginAction',  '_route' => 'login',);
                }

                // login_check
                if ($pathinfo === '/login_check') {
                    return array('_route' => 'login_check');
                }

            }

            // logout
            if ($pathinfo === '/logout') {
                return array('_route' => 'logout');
            }

        }

        // homepage
        if (0 === strpos($pathinfo, '/home') && preg_match('#^/home(?:/(?P<from>[^/]++)(?:/(?P<to>[^/]++)(?:/(?P<timeSelected>[^/]++))?)?)?$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'homepage')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\HistoricController::indexAction',  'from' => '',  'to' => '',  'timeSelected' => '',));
        }

        // prediction
        if (0 === strpos($pathinfo, '/prediction') && preg_match('#^/prediction/(?P<idBuilding>[^/]++)(?:/(?P<from>[^/]++)(?:/(?P<to>[^/]++))?)?$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'prediction')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PredictionController::indexAction',  'from' => '',  'to' => '',));
        }

        // newPrediction
        if (0 === strpos($pathinfo, '/new-prediction') && preg_match('#^/new\\-prediction/(?P<idBuilding>[^/]++)/(?P<from>[^/]++)/(?P<to>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'newPrediction')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PredictionController::newPredictionAction',));
        }

        // sensor
        if ($pathinfo === '/sensor') {
            return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SensorController::indexAction',  '_route' => 'sensor',);
        }

        // actionPlan
        if (0 === strpos($pathinfo, '/action-plan') && preg_match('#^/action\\-plan/(?P<idBuilding>[^/]++)(?:/(?P<from>[^/]++)(?:/(?P<to>[^/]++)(?:/(?P<timeSelected>[^/]++))?)?)?$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'actionPlan')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVActionPlanController::indexAction',  'from' => '',  'to' => '',  'timeSelected' => '',));
        }

        // newCalculate
        if (0 === strpos($pathinfo, '/new-calculate') && preg_match('#^/new\\-calculate/(?P<idBuilding>[^/]++)/(?P<from>[^/]++)/(?P<to>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'newCalculate')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVActionPlanController::newCalculateAction',));
        }

        if (0 === strpos($pathinfo, '/change-st')) {
            // changePVStatusDay
            if (0 === strpos($pathinfo, '/change-status') && preg_match('#^/change\\-status/(?P<idOutputDay>[^/]++)$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_changePVStatusDay;
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'changePVStatusDay')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVActionPlanController::changeStatusDayAction',));
            }
            not_changePVStatusDay:

            // changePVStrategy
            if ($pathinfo === '/change-strategy') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_changePVStrategy;
                }

                return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVActionPlanController::changeStrategyAction',  '_route' => 'changePVStrategy',);
            }
            not_changePVStrategy:

        }

        // init
        if ($pathinfo === '/init') {
            return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\InitController::indexAction',  '_route' => 'init',);
        }

        if (0 === strpos($pathinfo, '/select-')) {
            // selectOptions
            if ($pathinfo === '/select-options') {
                return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\InitController::selectOptionsAction',  '_route' => 'selectOptions',);
            }

            // selectGraph
            if (0 === strpos($pathinfo, '/select-graph') && preg_match('#^/select\\-graph/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'selectGraph')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\InitController::selectGraphAction',));
            }

        }

        // adminBuilding
        if ($pathinfo === '/admin-buildings') {
            return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\BuildingController::indexAction',  '_route' => 'adminBuilding',);
        }

        if (0 === strpos($pathinfo, '/building-')) {
            // building_create
            if ($pathinfo === '/building-create') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_building_create;
                }

                return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\BuildingController::createAction',  '_route' => 'building_create',);
            }
            not_building_create:

            // building_delete
            if (0 === strpos($pathinfo, '/building-delete') && preg_match('#^/building\\-delete/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'building_delete')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\BuildingController::deleteAction',));
            }

            // building_save
            if (0 === strpos($pathinfo, '/building-save') && preg_match('#^/building\\-save/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_building_save;
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'building_save')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\BuildingController::saveAction',));
            }
            not_building_save:

        }

        // globalConfigBuilding
        if (0 === strpos($pathinfo, '/configuration-building') && preg_match('#^/configuration\\-building/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'globalConfigBuilding')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\BuildingController::globalConfigBuildingAction',));
        }

        // building_description
        if (0 === strpos($pathinfo, '/building-description') && preg_match('#^/building\\-description/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'building_description')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\BuildingController::descriptionAction',));
        }

        // adminPartitions
        if (0 === strpos($pathinfo, '/admin-partitions') && preg_match('#^/admin\\-partitions/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'adminPartitions')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PartitionController::indexAction',));
        }

        if (0 === strpos($pathinfo, '/partition-')) {
            // createPartition
            if ($pathinfo === '/partition-create') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_createPartition;
                }

                return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PartitionController::createAction',  '_route' => 'createPartition',);
            }
            not_createPartition:

            // deletePartition
            if ($pathinfo === '/partition-delete') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_deletePartition;
                }

                return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PartitionController::deleteAction',  '_route' => 'deletePartition',);
            }
            not_deletePartition:

        }

        // adminSensors
        if (0 === strpos($pathinfo, '/admin-sensors') && preg_match('#^/admin\\-sensors/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'adminSensors')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SensorController::getAdminSensorsAction',));
        }

        if (0 === strpos($pathinfo, '/sensor-')) {
            // sensor_create
            if (0 === strpos($pathinfo, '/sensor-create') && preg_match('#^/sensor\\-create/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_sensor_create;
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensor_create')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SensorController::createAction',));
            }
            not_sensor_create:

            // sensor_save
            if (0 === strpos($pathinfo, '/sensor-save') && preg_match('#^/sensor\\-save/(?P<idBuilding>[^/]++)/(?P<idSensor>[^/]++)$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_sensor_save;
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensor_save')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SensorController::saveAction',));
            }
            not_sensor_save:

            // sensor_delete
            if (0 === strpos($pathinfo, '/sensor-delete') && preg_match('#^/sensor\\-delete/(?P<idBuilding>[^/]++)/(?P<idSensor>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensor_delete')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SensorController::deleteAction',));
            }

        }

        if (0 === strpos($pathinfo, '/a')) {
            // adminActionPlans
            if (0 === strpos($pathinfo, '/admin-action-plans') && preg_match('#^/admin\\-action\\-plans/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'adminActionPlans')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\AdminActionPlanController::adminActionPlansAction',));
            }

            // actionPlan_mapping
            if (0 === strpos($pathinfo, '/actionPlan-mapping') && preg_match('#^/actionPlan\\-mapping/(?P<idBuilding>[^/]++)/(?P<idActionPlan>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'actionPlan_mapping')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\APSensorsController::mappingAction',));
            }

        }

        if (0 === strpos($pathinfo, '/sensorPartition-')) {
            // sensorPartition_create
            if (0 === strpos($pathinfo, '/sensorPartition-create') && preg_match('#^/sensorPartition\\-create/(?P<idBuilding>[^/]++)/(?P<idActionPlan>[^/]++)$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_sensorPartition_create;
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensorPartition_create')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\APSensorsController::addSensorPartitionAction',));
            }
            not_sensorPartition_create:

            // sensorPartition_delete
            if (0 === strpos($pathinfo, '/sensorPartition-delete') && preg_match('#^/sensorPartition\\-delete/(?P<idBuilding>[^/]++)/(?P<idActionPlan>[^/]++)/(?P<idSensorPartition>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensorPartition_delete')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\APSensorsController::deleteSensorPartitionAction',));
            }

            // sensorPartition_save
            if (0 === strpos($pathinfo, '/sensorPartition-save') && preg_match('#^/sensorPartition\\-save/(?P<idBuilding>[^/]++)/(?P<idActionPlan>[^/]++)/(?P<idSensorPartition>[^/]++)$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_sensorPartition_save;
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensorPartition_save')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\APSensorsController::saveSensorPartitionAction',));
            }
            not_sensorPartition_save:

        }

        // view_switchOnOff
        if (0 === strpos($pathinfo, '/view-switch-On-Off') && preg_match('#^/view\\-switch\\-On\\-Off/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'view_switchOnOff')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SwitchActionPlanController::indexAction',));
        }

        if (0 === strpos($pathinfo, '/change-st')) {
            // changeSwitchStatusDay
            if (0 === strpos($pathinfo, '/change-status') && preg_match('#^/change\\-status/(?P<idOutputDay>[^/]++)$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_changeSwitchStatusDay;
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'changeSwitchStatusDay')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SwitchActionPlanController::changeStatusDayAction',));
            }
            not_changeSwitchStatusDay:

            // changeSwitchStrategy
            if ($pathinfo === '/change-strategy') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_changeSwitchStrategy;
                }

                return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SwitchActionPlanController::changeStrategyAction',  '_route' => 'changeSwitchStrategy',);
            }
            not_changeSwitchStrategy:

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
