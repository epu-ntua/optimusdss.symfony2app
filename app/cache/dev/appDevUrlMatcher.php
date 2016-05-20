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

        if (0 === strpos($pathinfo, '/action-plan')) {
            // actionPlan
            if (preg_match('#^/action\\-plan/(?P<idBuilding>[^/]++)(?:/(?P<idAPType>[^/]++)(?:/(?P<from>[^/]++)(?:/(?P<to>[^/]++)(?:/(?P<timeSelected>[^/]++))?)?)?)?$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'actionPlan')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVActionPlanController::indexAction',  'idAPType' => '',  'from' => '',  'to' => '',  'timeSelected' => '',));
            }

            // actionPlan_PVMaintenance
            if (0 === strpos($pathinfo, '/action-plan-pvm') && preg_match('#^/action\\-plan\\-pvm/(?P<idBuilding>[^/]++)(?:/(?P<idAPType>[^/]++)(?:/(?P<from>[^/]++)(?:/(?P<to>[^/]++)(?:/(?P<timeSelected>[^/]++))?)?)?)?$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'actionPlan_PVMaintenance')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVMActionPlanController::indexAction',  'idAPType' => '',  'from' => '',  'to' => '',  'timeSelected' => '',));
            }

        }

        // changePVMStatusDay
        if (0 === strpos($pathinfo, '/change-status-pvm') && preg_match('#^/change\\-status\\-pvm/(?P<idOutputDay>[^/]++)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_changePVMStatusDay;
            }

            return $this->mergeDefaults(array_replace($matches, array('_route' => 'changePVMStatusDay')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVMActionPlanController::changeStatusDayAction',));
        }
        not_changePVMStatusDay:

        if (0 === strpos($pathinfo, '/new-calculate-')) {
            if (0 === strpos($pathinfo, '/new-calculate-pv')) {
                // newCalculatePVM
                if (0 === strpos($pathinfo, '/new-calculate-pvm') && preg_match('#^/new\\-calculate\\-pvm/(?P<idBuilding>[^/]++)/(?P<idAPType>[^/]++)/(?P<from>[^/]++)/(?P<to>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'newCalculatePVM')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVMActionPlanController::newCalculatePVMAction',));
                }

                // newCalculatePV
                if (preg_match('#^/new\\-calculate\\-pv/(?P<idBuilding>[^/]++)/(?P<idAPType>[^/]++)/(?P<from>[^/]++)/(?P<to>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'newCalculatePV')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVActionPlanController::newCalculatePVAction',));
                }

            }

            // newCalculateES
            if (0 === strpos($pathinfo, '/new-calculate-es') && preg_match('#^/new\\-calculate\\-es/(?P<idBuilding>[^/]++)/(?P<idAPType>[^/]++)/(?P<from>[^/]++)/(?P<to>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'newCalculateES')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\EnergySourceActionPlanController::newCalculateESAction',));
            }

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

            if (0 === strpos($pathinfo, '/change-strategy')) {
                // changePVStrategy
                if ($pathinfo === '/change-strategy') {
                    if ($this->context->getMethod() != 'POST') {
                        $allow[] = 'POST';
                        goto not_changePVStrategy;
                    }

                    return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVActionPlanController::changeStrategyAction',  '_route' => 'changePVStrategy',);
                }
                not_changePVStrategy:

                // changePVStrategyWeek
                if ($pathinfo === '/change-strategy-week') {
                    if ($this->context->getMethod() != 'POST') {
                        $allow[] = 'POST';
                        goto not_changePVStrategyWeek;
                    }

                    return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\PVActionPlanController::changeStrategyWeekAction',  '_route' => 'changePVStrategyWeek',);
                }
                not_changePVStrategyWeek:

            }

        }

        // init
        if ($pathinfo === '/init') {
            return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\InitController::indexAction',  '_route' => 'init',);
        }

        // cityDashboard
        if ($pathinfo === '/city-dashboard') {
            return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\InitController::cityDashboardAction',  '_route' => 'cityDashboard',);
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

        // buildingDashboard
        if (0 === strpos($pathinfo, '/building-dashboard') && preg_match('#^/building\\-dashboard/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'buildingDashboard')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\InitController::buildingDashboardAction',));
        }

        // listActionPlans
        if (0 === strpos($pathinfo, '/list-action-plans') && preg_match('#^/list\\-action\\-plans/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'listActionPlans')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\InitController::listActionPlansAction',));
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

        if (0 === strpos($pathinfo, '/sensorRTime-')) {
            // sensorRTime_save
            if (0 === strpos($pathinfo, '/sensorRTime-save') && preg_match('#^/sensorRTime\\-save/(?P<idBuilding>[^/]++)/(?P<idSensorRTime>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensorRTime_save')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\BuildingController::saveSensorRTimeAction',));
            }

            // sensorRTime_create
            if (0 === strpos($pathinfo, '/sensorRTime-create') && preg_match('#^/sensorRTime\\-create/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensorRTime_create')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\BuildingController::addSensorRTimeAction',));
            }

            // sensorRTime_delete
            if (0 === strpos($pathinfo, '/sensorRTime-delete') && preg_match('#^/sensorRTime\\-delete/(?P<idBuilding>[^/]++)/(?P<idSensorRTime>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensorRTime_delete')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\BuildingController::deleteSensorRTimeAction',));
            }

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

            // sensor_checkStatus
            if (0 === strpos($pathinfo, '/sensor-check-status') && preg_match('#^/sensor\\-check\\-status/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensor_checkStatus')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SensorController::checkStatusSensorsAction',));
            }

        }

        if (0 === strpos($pathinfo, '/a')) {
            // adminActionPlans
            if (0 === strpos($pathinfo, '/admin-action-plans') && preg_match('#^/admin\\-action\\-plans/(?P<idBuilding>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'adminActionPlans')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\AdminActionPlanController::adminActionPlansAction',  'type' => '',));
            }

            if (0 === strpos($pathinfo, '/actionPlan-')) {
                // actionPlan_mapping
                if (0 === strpos($pathinfo, '/actionPlan-mapping') && preg_match('#^/actionPlan\\-mapping/(?P<idBuilding>[^/]++)/(?P<idActionPlan>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'actionPlan_mapping')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\APSensorsController::mappingAction',));
                }

                // actionPlan_save
                if (0 === strpos($pathinfo, '/actionPlan-save') && preg_match('#^/actionPlan\\-save/(?P<idBuilding>[^/]++)/(?P<idActionPlan>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'actionPlan_save')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\AdminActionPlanController::saveAction',));
                }

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
            if (0 === strpos($pathinfo, '/sensorPartition-delete') && preg_match('#^/sensorPartition\\-delete/(?P<idBuilding>[^/]++)/(?P<idActionPlan>[^/]++)/(?P<idSensorPartition>[^/]++)/(?P<orderSensor>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensorPartition_delete')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\APSensorsController::deleteSensorPartitionAction',));
            }

            // sensorPartition_save
            if (0 === strpos($pathinfo, '/sensorPartition-save') && preg_match('#^/sensorPartition\\-save/(?P<idBuilding>[^/]++)/(?P<idActionPlan>[^/]++)/(?P<idSensorPartition>[^/]++)/(?P<orderSensor>[^/]++)$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_sensorPartition_save;
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'sensorPartition_save')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\APSensorsController::saveSensorPartitionAction',));
            }
            not_sensorPartition_save:

        }

        // view_switchOnOff
        if (0 === strpos($pathinfo, '/view-switch-On-Off') && preg_match('#^/view\\-switch\\-On\\-Off/(?P<idBuilding>[^/]++)(?:/(?P<idAPType>[^/]++)(?:/(?P<from>[^/]++)(?:/(?P<to>[^/]++)(?:/(?P<timeSelected>[^/]++))?)?)?)?$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'view_switchOnOff')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SwitchActionPlanController::indexAction',  'idAPType' => '',  'from' => '',  'to' => '',  'timeSelected' => '',));
        }

        if (0 === strpos($pathinfo, '/change-st')) {
            // changeSwitchStatusDay
            if (0 === strpos($pathinfo, '/change-status-switch') && preg_match('#^/change\\-status\\-switch/(?P<idOutputDay>[^/]++)$#s', $pathinfo, $matches)) {
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

        if (0 === strpos($pathinfo, '/se')) {
            if (0 === strpos($pathinfo, '/semantic-framework/get_data_for_')) {
                // semanticframeworkget_data_for_model
                if (0 === strpos($pathinfo, '/semantic-framework/get_data_for_model') && preg_match('#^/semantic\\-framework/get_data_for_model/(?P<predictedparameter>[^/]++)/(?P<date>[^/]++)/(?P<window>[^/]++)/(?P<sensors>[^/]++)$#s', $pathinfo, $matches)) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_semanticframeworkget_data_for_model;
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'semanticframeworkget_data_for_model')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SemanticFrameworkController::get_data_for_modelAction',));
                }
                not_semanticframeworkget_data_for_model:

                // semanticframeworkget_data_for_forecasting
                if (0 === strpos($pathinfo, '/semantic-framework/get_data_for_forecasting') && preg_match('#^/semantic\\-framework/get_data_for_forecasting/(?P<predictedparameter>[^/]++)/(?P<date>[^/]++)/(?P<window>[^/]++)/(?P<sensors>[^/]++)$#s', $pathinfo, $matches)) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_semanticframeworkget_data_for_forecasting;
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'semanticframeworkget_data_for_forecasting')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SemanticFrameworkController::get_data_for_forecastingAction',));
                }
                not_semanticframeworkget_data_for_forecasting:

            }

            // getSetPointPlan
            if (0 === strpos($pathinfo, '/set_point') && preg_match('#^/set_point/(?P<idBuilding>[^/]++)/(?P<idAPType>[^/]++)(?:/(?P<start_date>[^/]++))?$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'getSetPointPlan')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SetPointPlanController::set_pointAction',  'start_date' => '',));
            }

        }

        // newCalculateSPM
        if (0 === strpos($pathinfo, '/new_set_point') && preg_match('#^/new_set_point/(?P<idBuilding>[^/]++)/(?P<idAPType>[^/]++)/(?P<from>[^/]++)/(?P<to>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'newCalculateSPM')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SetPointPlanController::newCalculateSetPointManagementAction',));
        }

        // changeSPMStatusDay
        if (0 === strpos($pathinfo, '/change-status-spm') && preg_match('#^/change\\-status\\-spm/(?P<idOutputDay>[^/]++)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_changeSPMStatusDay;
            }

            return $this->mergeDefaults(array_replace($matches, array('_route' => 'changeSPMStatusDay')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\SetPointPlanController::changeStatusDayAction',));
        }
        not_changeSPMStatusDay:

        // getOccupancyPlan
        if (0 === strpos($pathinfo, '/occupancy') && preg_match('#^/occupancy/(?P<idBuilding>[^/]++)/(?P<idAPType>[^/]++)(?:/(?P<start_date>[^/]++)(?:/(?P<constraints>[^/]++))?)?$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'getOccupancyPlan')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\OccupancyController::occupancyAction',  'start_date' => '',  'constraints' => '',));
        }

        // changeOStatusDay
        if (0 === strpos($pathinfo, '/change-status-occupancy') && preg_match('#^/change\\-status\\-occupancy/(?P<idOutputDay>[^/]++)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_changeOStatusDay;
            }

            return $this->mergeDefaults(array_replace($matches, array('_route' => 'changeOStatusDay')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\OccupancyController::changeStatusDayAction',));
        }
        not_changeOStatusDay:

        // energySourcePlan
        if (0 === strpos($pathinfo, '/energy_flows') && preg_match('#^/energy_flows/(?P<idBuilding>[^/]++)/(?P<idAPType>[^/]++)(?:/(?P<from>[^/]++)(?:/(?P<to>[^/]++))?)?$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'energySourcePlan')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\EnergySourceActionPlanController::plan_indexAction',  'from' => '',  'to' => '',));
        }

        // changeEconomizerStatusDay
        if (0 === strpos($pathinfo, '/change-status-economizer') && preg_match('#^/change\\-status\\-economizer/(?P<idOutputDay>[^/]++)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_changeEconomizerStatusDay;
            }

            return $this->mergeDefaults(array_replace($matches, array('_route' => 'changeEconomizerStatusDay')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\EconomizerActionPlanController::changeStatusDayAction',));
        }
        not_changeEconomizerStatusDay:

        // newCalculateEconomizer
        if (0 === strpos($pathinfo, '/new-calculate-economizer') && preg_match('#^/new\\-calculate\\-economizer/(?P<idBuilding>[^/]++)/(?P<idAPType>[^/]++)/(?P<from>[^/]++)/(?P<to>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'newCalculateEconomizer')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\EconomizerActionPlanController::newCalculateEconomizerAction',));
        }

        // economizerPlan
        if (0 === strpos($pathinfo, '/economizer') && preg_match('#^/economizer/(?P<idBuilding>[^/]++)/(?P<idAPType>[^/]++)(?:/(?P<from>[^/]++)(?:/(?P<to>[^/]++)(?:/(?P<method>[^/]++))?)?)?$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'economizerPlan')), array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\EconomizerActionPlanController::indexAction',  'from' => '',  'to' => '',  'method' => '',));
        }

        // changeEconomizerInternalDay
        if ($pathinfo === '/change-internal-economizer') {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_changeEconomizerInternalDay;
            }

            return array (  '_controller' => 'Optimus\\OptimusBundle\\Controller\\EconomizerActionPlanController::changeInternalDayAction',  '_route' => 'changeEconomizerInternalDay',);
        }
        not_changeEconomizerInternalDay:

        // tableReports
        if (0 === strpos($pathinfo, '/table-reports') && preg_match('#^/table\\-reports/(?P<idBuilding>[^/]++)/(?P<insertDB>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'tableReports')), array (  '_controller' => 'OptimusOptimusBundle:WeeklyReports:tableReports',));
        }

        // openFormReport
        if (0 === strpos($pathinfo, '/open-form-report') && preg_match('#^/open\\-form\\-report/(?P<idBuilding>[^/]++)/(?P<idWeeklyReport>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'openFormReport')), array (  '_controller' => 'OptimusOptimusBundle:WeeklyReports:openFormReport',));
        }

        // saveDataFormReport
        if (0 === strpos($pathinfo, '/save-data-form-report') && preg_match('#^/save\\-data\\-form\\-report/(?P<idBuilding>[^/]++)/(?P<idWeeklyReport>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'saveDataFormReport')), array (  '_controller' => 'OptimusOptimusBundle:WeeklyReports:saveDataFormReport',));
        }

        // root
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'root');
            }

            return array (  '_controller' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController::urlRedirectAction',  'path' => '/init',  'permanent' => true,  '_route' => 'root',);
        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
