<?php

/* OptimusOptimusBundle:Graph:graphPredictions.html.twig */
class __TwigTemplate_8f0fa3ddd60a3470c781285848f461acd9af65dda16eb4e9b249175d454f5e00 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        try {
            $this->parent = $this->env->loadTemplate("OptimusOptimusBundle:Layouts:layout.html.twig");
        } catch (Twig_Error_Loader $e) {
            $e->setTemplateFile($this->getTemplateName());
            $e->setTemplateLine(1);

            throw $e;
        }

        $this->blocks = array(
            'javascripts' => array($this, 'block_javascripts'),
            'clase_ForecastedData' => array($this, 'block_clase_ForecastedData'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "OptimusOptimusBundle:Layouts:layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_javascripts($context, array $blocks = array())
    {
        // line 4
        echo "\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/jquery-1.11.1.min.js"), "html", null, true);
        echo "\"></script>
\t<script  type=\"text/javascript\" src=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/jquery-ui-1.10.3.custom.min.js"), "html", null, true);
        echo "\"></script>
\t\t
\t<script type=\"text/javascript\" src=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.js"), "html", null, true);
        echo "\"></script>\t
\t<script type=\"text/javascript\" src=\"";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.navigate.js"), "html", null, true);
        echo "\"></script>
\t<script type=\"text/javascript\" src=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.time.js"), "html", null, true);
        echo "\"></script>
\t<script type=\"text/javascript\" src=\"";
        // line 10
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.symbol.js"), "html", null, true);
        echo "\"></script>
\t\t
\t<script type=\"text/javascript\" src=\"";
        // line 12
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.axislabelsV2.js"), "html", null, true);
        echo "\"></script>\t
\t
\t<script type=\"text/javascript\" src=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.pie.js"), "html", null, true);
        echo "\"></script>\t
\t<script type=\"text/javascript\" src=\"";
        // line 15
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.fillbetween.js"), "html", null, true);
        echo "\"></script>
\t<script type=\"text/javascript\" src=\"";
        // line 16
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.selection.js"), "html", null, true);
        echo "\"></script>
\t<script type=\"text/javascript\" src=\"";
        // line 17
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/optimus/js/flot/jquery.flot.dashes.js"), "html", null, true);
        echo "\"></script>
";
    }

    // line 20
    public function block_clase_ForecastedData($context, array $blocks = array())
    {
        echo "activo";
    }

    // line 23
    public function block_content($context, array $blocks = array())
    {
        // line 24
        echo "
<script type=\"text/javascript\">
\tvar timeSelected='";
        // line 26
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()), "html", null, true);
        echo "';
\t
\t\$(function(){\t\t
\t\t\$(\"#datepicker\").hide();
\t\t
\t\tvar startDate;
\t\tvar endDate;
\t\t
\t\tvar selectCurrentWeek = function() {
\t\t\twindow.setTimeout(function () {
\t\t\t\t\$('#datepicker').find('.ui-datepicker-current-day a').addClass('ui-state-active')
\t\t\t}, 1);
\t\t}
\t\t
\t\t\$('#datepicker').datepicker( {
\t\t\t/*showWeek: true,
\t\t\tfirstDay: 1,*/
\t\t\tshowOtherMonths: true,
\t\t\tselectOtherMonths: true,
\t\t\tonSelect: function(dateText, inst) { 
\t\t\t\tvar date = \$(this).datepicker('getDate');
\t\t\t\t
\t\t\t\tconsole.log( date.getDate());
\t\t\t\tconsole.log( date.getDay());\t\t\t
\t\t\t\t
\t\t\t\tif(timeSelected=='day'){
\t\t\t\t\tstartDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
\t\t\t\t\tendDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
\t\t\t\t\tconsole.log( startDate);
\t\t\t\t\tconsole.log( endDate);
\t\t\t\t\t
\t\t\t\t}else if(timeSelected=='week'){
\t\t\t\t\tstartDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
\t\t\t\t\tendDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
\t\t\t\t}else if(timeSelected=='month'){
\t\t\t\t\tstartDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
\t\t\t\t\tendDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 30);
\t\t\t\t}
\t\t\t\t
\t\t\t\t//endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
\t\t\t\t\t\t
\t\t\t\tvar dateFormat = inst.settings.dateFormat || \$.datepicker._defaults.dateFormat;
\t\t\t\t\$('#startDate').text(\$.datepicker.formatDate(\"yy-mm-dd\" , startDate, inst.settings )); //dateFormat
\t\t\t\t\$('#endDate').text(\$.datepicker.formatDate( \"yy-mm-dd\", endDate, inst.settings ));
\t\t\t\t
\t\t\t\tselectCurrentWeek();
\t\t\t\t
\t\t\t\t//location.href='<?php //echo base_url(); ?>index.php/main_controller/index/'+\$('#startDate').html()+'/'+\$('#endDate').html()+'/'+timeSelected;
\t\t\t},
\t\t\tbeforeShowDay: function(date) {
\t\t\t\tvar cssClass = '';
\t\t\t\tif((date >= startDate && date <= endDate) || (date>=new Date('";
        // line 77
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "startDate", array()), "html", null, true);
        echo "') && date <=new Date('";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "endDate", array()), "html", null, true);
        echo "')))
\t\t\t\t\tcssClass = 'ui-datepicker-current-day';
\t\t\t\treturn [true, cssClass];
\t\t\t},
\t\t\tonChangeMonthYear: function(year, month, inst) {
\t\t\t\tselectCurrentWeek();
\t\t\t}
\t\t});
\t\t
\t\t\$(document).on('mousemove', '#datepicker .ui-datepicker-calendar tr', function() { \$(this).find('td a').addClass('ui-state-hover');    });
\t\t\$(document).on('mouseleave','#datepicker .ui-datepicker-calendar tr', function() { \$(this).find('td a').removeClass('ui-state-hover'); });
\t\t
\t\t//Gráfica Predictor 
\t\t";
        // line 90
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 91
            echo "\t\t\t";
            $context["lp_i"] = $this->getAttribute($context["loop"], "index0", array());
            echo "\t\t
\t\t\t\tvar plot= \$.plot(\$(\"#placeholder_";
            // line 92
            echo twig_escape_filter($this->env, (isset($context["lp_i"]) ? $context["lp_i"] : null), "html", null, true);
            echo "\"), [ \t\t
\t\t\t\t";
            // line 93
            $context["maxY"] = 0;
            // line 94
            echo "\t\t\t\t";
            $context["minY"] = 0;
            echo "\t\t\t\t
\t\t\t{data: [\t
\t\t\t
\t\t\t\t";
            // line 97
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["j"]) {
                // line 98
                echo "\t\t\t\t\t";
                $context["lp_j"] = $this->getAttribute($context["loop"], "index0", array());
                // line 99
                echo "\t\t\t\t\t
\t\t\t\t\t";
                // line 100
                if (((isset($context["lp_j"]) ? $context["lp_j"] : null) == 0)) {
                    echo " 
\t\t\t\t\t\t";
                    // line 101
                    $context["minY"] = $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array());
                    // line 102
                    echo "\t\t\t\t\t";
                } elseif (((isset($context["minY"]) ? $context["minY"] : null) > $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array()))) {
                    echo " 
\t\t\t\t\t\t";
                    // line 103
                    $context["minY"] = $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array());
                    // line 104
                    echo "\t\t\t\t\t";
                }
                // line 105
                echo "\t\t\t\t\t
\t\t\t\t\t";
                // line 106
                if (((isset($context["maxY"]) ? $context["maxY"] : null) < $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array()))) {
                    echo " 
\t\t\t\t\t\t";
                    // line 107
                    $context["maxY"] = $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array());
                    // line 108
                    echo "\t\t\t\t\t";
                }
                echo "\t\t\t
\t\t\t\t
\t\t\t\t\t";
                // line 110
                $context["date"] = twig_date_format_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "date", array()), "F j, Y H:i:s");
                // line 111
                echo "\t\t
\t\t\t\t
\t\t\t\t\t[new Date(\"";
                // line 113
                echo twig_escape_filter($this->env, (isset($context["date"]) ? $context["date"] : null), "html", null, true);
                echo "\"), ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "values", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "value", array()), "html", null, true);
                echo " ],\t
\t\t\t\t";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['j'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 114
            echo "\t\t\t\t 
\t\t\t\t], color:'";
            // line 115
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "color", array()), "html", null, true);
            echo "', lines:{show:true, lineWidth:0.5, fillColor:'#000000'},shadowSize: 0,\t\t\t
\t\t\t\t
\t\t\t\t";
            // line 117
            if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "type", array()) == "Panel")) {
                // line 118
                echo "\t\t\t\t\tpoints: { show: true, symbol:'cross' }\t
\t\t\t\t\t
\t\t\t\t";
            }
            // line 121
            echo "\t\t\t\t\t},
\t\t\t\t";
            // line 122
            $context["panYmin"] = (0.1 + ((0.1 / 100) * 100));
            // line 123
            echo "\t\t\t\t";
            $context["panYmax"] = ((isset($context["maxY"]) ? $context["maxY"] : null) + (((isset($context["maxY"]) ? $context["maxY"] : null) / 100) * 20));
            echo "\t\t\t
\t\t\t],
\t\t\t{
\t\t\t\tgrid: {
\t\t\t\t\tcanvasText:{show:true},
\t\t\t\t\thoverable: true,
\t\t\t\t\tclickable: true,
\t\t\t\t\tborderWidth: 1,
\t\t\t\t\tborderColor:\"#cacaca\",
\t\t\t\t\tminBorderMargin: 0.5,
\t\t\t\t\taxisMargin: 1,
\t\t\t\t\tbackgroundColor:'#fff'
\t\t\t\t},
\t\t\t\t\t\t\t
\t\t\t\txaxis:{\t\t\t\t\t
\t\t\t\t\tmode: \"time\", min: new Date(\"";
            // line 138
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "startDate", array()), "F j, Y H:i:s"), "html", null, true);
            echo "\"), max: new Date(\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "endDate", array()), "F j, Y H:i:s"), "html", null, true);
            echo "\"), tickLength: 5, font:{size: 11, family: \"sans-serif\", color:\"#626262\"}, 
\t\t\t\t\t";
            // line 139
            if (((isset($context["lp_i"]) ? $context["lp_i"] : null) == 0)) {
                echo "  
\t\t\t\t\t\tposition:'top', 
\t\t\t\t\t";
            }
            // line 141
            echo "\t\t\t\t\t
\t\t\t\t\t";
            // line 142
            if ((((isset($context["lp_i"]) ? $context["lp_i"] : null) < (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array())) - 1)) && ((isset($context["lp_i"]) ? $context["lp_i"] : null) != 0))) {
                echo " \t\t\t\t\t
\t\t\t\t\t\tshow:false, 
\t\t\t\t\t";
            } else {
                // line 145
                echo "\t\t\t\t\t\tshow:true,
\t\t\t\t\t\ttimeformat: \"%a \\n %d.%m\",
\t\t\t\t\t\tlabelWidth: 50,
\t\t\t\t\t\tminTickSize: [1, \"hour\"], reserveSpace:true\t\t\t\t
\t\t\t\t\t";
            }
            // line 149
            echo " 
\t\t\t\t},
\t\t\t\tyaxis:{min:";
            // line 151
            echo twig_escape_filter($this->env, (isset($context["minY"]) ? $context["minY"] : null), "html", null, true);
            echo ", max:";
            echo twig_escape_filter($this->env, (isset($context["maxY"]) ? $context["maxY"] : null), "html", null, true);
            echo ", tickDecimals: 2,  panRange:[";
            echo twig_escape_filter($this->env, (isset($context["panYmin"]) ? $context["panYmin"] : null), "html", null, true);
            echo ",";
            echo twig_escape_filter($this->env, (isset($context["panYmax"]) ? $context["panYmax"] : null), "html", null, true);
            echo "], labelWidth: 50, labelHeight: 50, reserveSpace: true,  ticks:[";
            echo twig_escape_filter($this->env, (isset($context["minY"]) ? $context["minY"] : null), "html", null, true);
            echo ", ";
            echo twig_escape_filter($this->env, (isset($context["maxY"]) ? $context["maxY"] : null), "html", null, true);
            echo "], font:{size: 9, family: \"sans-serif\", color:\"#626262\"}},
\t\t\t\tpan:{interactive:true, cursor: \"move\", frameRate: 20}
\t\t\t});
\t\t\t
\t\t\tvar previousPoint = null;
\t\t\t\$(\"#placeholder_";
            // line 156
            echo twig_escape_filter($this->env, (isset($context["lp_i"]) ? $context["lp_i"] : null), "html", null, true);
            echo "\").bind(\"plothover\", function (event, pos, item) {
\t\t\t\tif (item) {\t\t\t\t\t
\t\t\t\t\tdocument.body.style.cursor = 'pointer';
\t\t\t\t\tif (previousPoint != item.dataIndex) {
\t\t\t\t\t\tpreviousPoint = item.dataIndex;

\t\t\t\t\t\t\$(\"#tooltip\").remove();
\t\t\t\t\t\tvar x = item.datapoint[0],
\t\t\t\t\t\t\ty = item.datapoint[1];
\t\t\t\t\t\tshowTooltip(item.pageX, item.pageY,y);
\t\t\t\t\t}
\t\t\t\t}else{
\t\t\t\t\tdocument.body.style.cursor = 'default';
\t\t\t\t\t\$(\"#tooltip\").remove();
\t\t\t\t\tpreviousPoint = null;
\t\t\t\t}
\t\t\t});
\t\t";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 174
        echo "\t
\t\t/*-----------------------------------------------------------------------------------*/
\t\t
\t\t//Gráfica Historica + Predictor
\t\t
\t\t";
        // line 179
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 180
            echo "\t\t\t";
            $context["lp_i"] = $this->getAttribute($context["loop"], "index0", array());
            echo "\t\t
\t\t\t\tvar plot= \$.plot(\$(\"#placeHistoricPredictor_";
            // line 181
            echo twig_escape_filter($this->env, (isset($context["lp_i"]) ? $context["lp_i"] : null), "html", null, true);
            echo "\"), [ \t\t
\t\t\t\t";
            // line 182
            $context["maxY"] = 0;
            // line 183
            echo "\t\t\t\t";
            $context["minY"] = 0;
            echo "\t\t\t\t
\t\t\t\t
\t\t\t
\t\t\t\t";
            // line 186
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["j"]) {
                // line 187
                echo "\t\t\t\t\t{\t\t\t\t\t\t
\t\t\t\t\t\tdata: [
\t\t\t\t\t\t";
                // line 189
                $context["lp_j"] = $this->getAttribute($context["loop"], "index0", array());
                // line 190
                echo "\t\t\t\t\t\t
\t\t\t\t\t\t";
                // line 191
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "values", array()));
                $context['loop'] = array(
                  'parent' => $context['_parent'],
                  'index0' => 0,
                  'index'  => 1,
                  'first'  => true,
                );
                if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                    $length = count($context['_seq']);
                    $context['loop']['revindex0'] = $length - 1;
                    $context['loop']['revindex'] = $length;
                    $context['loop']['length'] = $length;
                    $context['loop']['last'] = 1 === $length;
                }
                foreach ($context['_seq'] as $context["_key"] => $context["z"]) {
                    // line 192
                    echo "\t\t\t\t\t\t\t";
                    $context["lp_z"] = $this->getAttribute($context["loop"], "index0", array());
                    // line 193
                    echo "\t\t\t\t\t\t
\t\t\t\t\t\t\t";
                    // line 194
                    if (((isset($context["lp_z"]) ? $context["lp_z"] : null) == 0)) {
                        echo " 
\t\t\t\t\t\t\t\t";
                        // line 195
                        $context["minY"] = $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "values", array()), (isset($context["lp_z"]) ? $context["lp_z"] : null), array(), "array"), "value", array());
                        // line 196
                        echo "\t\t\t\t\t\t\t";
                    } elseif (((isset($context["minY"]) ? $context["minY"] : null) > $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "values", array()), (isset($context["lp_z"]) ? $context["lp_z"] : null), array(), "array"), "value", array()))) {
                        echo " 
\t\t\t\t\t\t\t\t";
                        // line 197
                        $context["minY"] = $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "values", array()), (isset($context["lp_z"]) ? $context["lp_z"] : null), array(), "array"), "value", array());
                        // line 198
                        echo "\t\t\t\t\t\t\t";
                    }
                    // line 199
                    echo "\t\t\t\t\t\t\t
\t\t\t\t\t\t\t";
                    // line 200
                    if (((isset($context["maxY"]) ? $context["maxY"] : null) < $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "values", array()), (isset($context["lp_z"]) ? $context["lp_z"] : null), array(), "array"), "value", array()))) {
                        echo " 
\t\t\t\t\t\t\t\t";
                        // line 201
                        $context["maxY"] = $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "values", array()), (isset($context["lp_z"]) ? $context["lp_z"] : null), array(), "array"), "value", array());
                        // line 202
                        echo "\t\t\t\t\t\t\t";
                    }
                    echo "\t\t\t
\t\t\t\t\t\t
\t\t\t\t\t\t\t";
                    // line 204
                    $context["date"] = twig_date_format_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "values", array()), (isset($context["lp_z"]) ? $context["lp_z"] : null), array(), "array"), "date", array()), "F j, Y H:i:s");
                    // line 205
                    echo "\t\t\t
\t\t\t\t\t
\t\t\t\t\t\t\t[new Date(\"";
                    // line 207
                    echo twig_escape_filter($this->env, (isset($context["date"]) ? $context["date"] : null), "html", null, true);
                    echo "\"), ";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "values", array()), (isset($context["lp_z"]) ? $context["lp_z"] : null), array(), "array"), "value", array()), "html", null, true);
                    echo " ],
\t\t\t\t\t\t";
                    ++$context['loop']['index0'];
                    ++$context['loop']['index'];
                    $context['loop']['first'] = false;
                    if (isset($context['loop']['length'])) {
                        --$context['loop']['revindex0'];
                        --$context['loop']['revindex'];
                        $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['z'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 209
                echo "\t\t\t\t\t\t
\t\t\t\t\t\t], color:'";
                // line 210
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "color", array()), "html", null, true);
                echo "', lines:{show:true, lineWidth:0.5, fillColor:'#FFFFFF'},shadowSize: 0,
\t\t\t\t\t\t
\t\t\t\t\t\t";
                // line 212
                if (($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "Sensor", array()), (isset($context["lp_j"]) ? $context["lp_j"] : null), array(), "array"), "type", array()) == "Panel")) {
                    // line 213
                    echo "\t\t\t\t\t\t\tdashes: { show: true }\t\t\t\t\t\t\t
\t\t\t\t\t\t";
                }
                // line 215
                echo "\t\t\t\t\t},
\t\t\t\t";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['j'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 216
            echo "\t\t\t\t
\t\t\t\t
\t\t\t\t";
            // line 218
            $context["panYmin"] = (0.1 + ((0.1 / 100) * 100));
            // line 219
            echo "\t\t\t\t";
            $context["panYmax"] = ((isset($context["maxY"]) ? $context["maxY"] : null) + (((isset($context["maxY"]) ? $context["maxY"] : null) / 100) * 20));
            echo "\t\t\t
\t\t\t],
\t\t\t{
\t\t\t\tgrid: {
\t\t\t\t\tcanvasText:{show:true},
\t\t\t\t\thoverable: true,
\t\t\t\t\tclickable: true,
\t\t\t\t\tborderWidth: 1,
\t\t\t\t\tborderColor:\"#cacaca\",
\t\t\t\t\tminBorderMargin: 0.5,
\t\t\t\t\taxisMargin: 1,
\t\t\t\t\tbackgroundColor:'#fff'
\t\t\t\t},
\t\t\t\t\t\t\t
\t\t\t\txaxis:{\t\t\t\t\t
\t\t\t\t\tmode: \"time\", min: new Date(\"";
            // line 234
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "startDate", array()), "F j, Y H:i:s"), "html", null, true);
            echo "\"), max: new Date(\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["lp_i"]) ? $context["lp_i"] : null), array(), "array"), "endDate", array()), "F j, Y H:i:s"), "html", null, true);
            echo "\"), tickLength: 5, font:{size: 11, family: \"sans-serif\", color:\"#626262\"}, 
\t\t\t\t\t";
            // line 235
            if (((isset($context["lp_i"]) ? $context["lp_i"] : null) == 0)) {
                echo "  
\t\t\t\t\t\tposition:'top', 
\t\t\t\t\t";
            }
            // line 237
            echo "\t\t\t\t\t
\t\t\t\t\t";
            // line 238
            if ((((isset($context["lp_i"]) ? $context["lp_i"] : null) < (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array())) - 1)) && ((isset($context["lp_i"]) ? $context["lp_i"] : null) != 0))) {
                echo " \t\t\t\t\t
\t\t\t\t\t\tshow:false, 
\t\t\t\t\t";
            } else {
                // line 241
                echo "\t\t\t\t\t\tshow:true,
\t\t\t\t\t\ttimeformat: \"%a \\n %d.%m\",
\t\t\t\t\t\tlabelWidth: 50,
\t\t\t\t\t\tminTickSize: [1, \"hour\"], reserveSpace:true\t\t\t\t
\t\t\t\t\t";
            }
            // line 245
            echo " 
\t\t\t\t},
\t\t\t\tyaxis:{min:";
            // line 247
            echo twig_escape_filter($this->env, (isset($context["minY"]) ? $context["minY"] : null), "html", null, true);
            echo ", max:";
            echo twig_escape_filter($this->env, (isset($context["maxY"]) ? $context["maxY"] : null), "html", null, true);
            echo ", tickDecimals: 2,  panRange:[";
            echo twig_escape_filter($this->env, (isset($context["panYmin"]) ? $context["panYmin"] : null), "html", null, true);
            echo ",";
            echo twig_escape_filter($this->env, (isset($context["panYmax"]) ? $context["panYmax"] : null), "html", null, true);
            echo "], labelWidth: 50, labelHeight: 50, reserveSpace: true,  ticks:[";
            echo twig_escape_filter($this->env, (isset($context["minY"]) ? $context["minY"] : null), "html", null, true);
            echo ", ";
            echo twig_escape_filter($this->env, (isset($context["maxY"]) ? $context["maxY"] : null), "html", null, true);
            echo "], font:{size: 9, family: \"sans-serif\", color:\"#626262\"}},
\t\t\t\tpan:{interactive:true, cursor: \"move\", frameRate: 20}
\t\t\t});
\t\t\t
\t\t\tvar previousPoint = null;
\t\t\t\$(\"#placeHistoricPredictor_";
            // line 252
            echo twig_escape_filter($this->env, (isset($context["lp_i"]) ? $context["lp_i"] : null), "html", null, true);
            echo "\").bind(\"plothover\", function (event, pos, item) {
\t\t\t\tif (item) {\t\t\t\t\t
\t\t\t\t\tdocument.body.style.cursor = 'pointer';
\t\t\t\t\tif (previousPoint != item.dataIndex) {
\t\t\t\t\t\tpreviousPoint = item.dataIndex;

\t\t\t\t\t\t\$(\"#tooltip\").remove();
\t\t\t\t\t\tvar x = item.datapoint[0],
\t\t\t\t\t\t\ty = item.datapoint[1];
\t\t\t\t\t\tshowTooltip(item.pageX, item.pageY,y);
\t\t\t\t\t}
\t\t\t\t}else{
\t\t\t\t\tdocument.body.style.cursor = 'default';
\t\t\t\t\t\$(\"#tooltip\").remove();
\t\t\t\t\tpreviousPoint = null;
\t\t\t\t}
\t\t\t});
\t\t";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 270
        echo "\t\t
\t\t/*-----------------------------------------------------------------------------------*/
\t\t
\t\t//Función compartida por todas las gráficas
\t\tfunction showTooltip(x, y, contents)
\t\t{
\t\t\t\$('<div id=\"tooltip\" style=\"font-size:10px;\">' + contents + '</div>').css( {
\t\t\t\tposition: 'absolute',
\t\t\t\tdisplay: 'none',
\t\t\t\ttop: y-20,
\t\t\t\tleft: x + 10,
\t\t\t\tborder: '1px solid #595959',
\t\t\t\tpadding: '2px',\t\t\t\t
\t\t\t\t'background-color': '#DADADA',
\t\t\t\topacity: 0.80
\t\t\t}).appendTo(\"body\").fadeIn(200);
\t\t}
\t\t
\t\t
\t});
\t
\t//Show/hide el calendario
\tfunction displayCalendar()
\t{
\t\t\$(\"#datepicker\").slideToggle();
\t}
\t
\t//Cambia el formato del tiempo: dia / semana / mes
\tfunction changeTo(time)
\t{
\t\tif(time=='day'){
\t\t\t\$(\"#selectDay\").css({'background-color':'#2e75b6', 'color':'#fff' });
\t\t\t\$(\"#selectWeek\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\t\$(\"#selectMonth\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\ttimeSelected='day';
\t\t}else if(time=='week'){ 
\t\t\t\$(\"#selectDay\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\t\$(\"#selectWeek\").css({'background-color':'#2e75b6', 'color':'#fff' });
\t\t\t\$(\"#selectMonth\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\ttimeSelected='week';
\t\t}else if(time=='month'){
\t\t\t\$(\"#selectDay\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\t\$(\"#selectWeek\").css({'background-color':'#e8eef8', 'color':'#44546a' });
\t\t\t\$(\"#selectMonth\").css({'background-color':'#2e75b6', 'color':'#fff' });
\t\t\ttimeSelected='month';
\t\t}
\t}

\t
</script>
    <div id=\"centerRight\">
\t\t\t<div id=\"indicators\" style=\"background-color:#d0cece; width:100%;  overflow:hidden; border-bottom:1px solid #7f7f7f; border-top:1px solid #7f7f7f;\">
\t\t\t\t<p style=\"width:847px; border-right:1px solid #7f7f7f; float:left; margin-right:0px; height:15px; padding:10px 0 10px 10px; margin:0px;\"><strong>Indicators</strong></p>
\t\t\t\t<p style=\"background-color:#e8eef8; float:left; margin:0px; width:40px; height:35px; text-align:center; font-size:16px; line-height:2.3;\">+</p>
\t\t\t</div>\t
\t\t\t
\t\t\t<div id=\"indicators\" style=\"background-color:#d0cece; width:100%;  overflow:hidden; border-bottom:1px solid #7f7f7f;\">
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px 0 10px 10px; margin:0px; width:60px; border-right:1px solid #7f7f7f;\"><strong>Dates</strong></p>
\t\t\t\t
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px 0 10px 10px; margin:0px; width:160px; border-right:1px solid #7f7f7f; text-align:center; background-color: #e8eef8;\">
\t\t\t\t\t<span id=\"startDate\">";
        // line 330
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "startDate", array()), "html", null, true);
        echo "</span> / 
\t\t\t\t\t<span id=\"endDate\">";
        // line 331
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "endDate", array()), "html", null, true);
        echo "</span>
\t\t\t\t</p>
\t\t\t\t<p style=\"background-color:#e8eef8; float:left; margin:0px; width:40px; height:35px; text-align:center; font-size:16px; line-height:2.3; border-right:1px solid #7f7f7f; cursor:pointer;\" onclick=\"displayCalendar();\">+</p>
\t\t\t\t
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 0px; margin:0px; width:160px; border-right:1px solid #7f7f7f; text-align:right;\"><strong>Time scope: </strong></p>
\t\t\t\t
\t\t\t\t";
        // line 337
        if (($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()) == "day")) {
            // line 338
            echo "\t\t\t\t\t";
            $context["stileDay"] = "background-color: #2e75b6; color:#fff;";
            // line 339
            echo "\t\t\t\t";
        } else {
            // line 340
            echo "\t\t\t\t\t";
            $context["stileDay"] = "background-color: #e8eef8; color:#44546a;";
            // line 341
            echo "\t\t\t\t";
        }
        // line 342
        echo "\t\t\t\t
\t\t\t\t";
        // line 343
        if (($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()) == "week")) {
            // line 344
            echo "\t\t\t\t\t";
            $context["stileWeek"] = "background-color: #2e75b6; color:#fff;";
            // line 345
            echo "\t\t\t\t";
        } else {
            // line 346
            echo "\t\t\t\t\t";
            $context["stileWeek"] = "background-color: #e8eef8; color:#44546a;";
            // line 347
            echo "\t\t\t\t";
        }
        // line 348
        echo "\t\t\t\t
\t\t\t\t";
        // line 349
        if (($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()) == "month")) {
            // line 350
            echo "\t\t\t\t\t";
            $context["stileMonth"] = "background-color: #2e75b6; color:#fff; ";
            // line 351
            echo "\t\t\t\t";
        } else {
            // line 352
            echo "\t\t\t\t\t";
            $context["stileMonth"] = "background-color: #e8eef8; color:#44546a;";
            // line 353
            echo "\t\t\t\t";
        }
        // line 354
        echo "\t\t\t\t
\t\t\t\t<p id=\"selectDay\" style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 10px; margin:0px;  border-right:1px solid #7f7f7f; text-align:center; ";
        // line 355
        echo twig_escape_filter($this->env, (isset($context["stileDay"]) ? $context["stileDay"] : null), "html", null, true);
        echo " cursor:pointer;\" onclick=\"changeTo('day');\">day</p>
\t\t\t\t
\t\t\t\t<p id=\"selectWeek\" style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 10px; margin:0px;  border-right:1px solid #7f7f7f; text-align:center; ";
        // line 357
        echo twig_escape_filter($this->env, (isset($context["stileWeek"]) ? $context["stileWeek"] : null), "html", null, true);
        echo " cursor:pointer;\" onclick=\"changeTo('week');\">week</p>
\t\t\t\t
\t\t\t\t<p id=\"selectMonth\" style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 10px; margin:0px;  border-right:1px solid #7f7f7f; text-align:center; ";
        // line 359
        echo twig_escape_filter($this->env, (isset($context["stileMonth"]) ? $context["stileMonth"] : null), "html", null, true);
        echo " cursor:pointer;\" onclick=\"changeTo('month');\">month</p>
\t\t\t\t
\t\t\t\t
\t\t\t</div>
\t\t\t
\t\t\t<div id=\"contentGraficas\" style=\"overflow:hidden; float:left; padding-top:25px; background-color:#f2f2f2;\">
\t\t\t\t
\t\t\t\t<div id=\"datepicker\" style=\" margin-bottom:40px; float:left; background-color:#d0cece;\"></div>
\t\t
\t\t\t\t<!-- Gráficas predictor -->
\t\t\t\t";
        // line 369
        $context["mitad"] = (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array())) / 2);
        // line 370
        echo "\t\t\t\t
\t\t\t\t";
        // line 371
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["sensor"]) {
            // line 372
            echo "\t\t\t\t\t
\t\t\t\t\t";
            // line 373
            $context["loop_i"] = $this->getAttribute($context["loop"], "index0", array());
            // line 374
            echo "\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t";
            // line 376
            if (((isset($context["loop_i"]) ? $context["loop_i"] : null) < (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array())) - 1))) {
                // line 377
                echo "\t\t\t\t\t\t";
                $context["stilo"] = " height:60px;";
                // line 378
                echo "\t\t\t\t\t";
            } else {
                // line 379
                echo "\t\t\t\t\t\t";
                $context["stilo"] = "min-height:60px;";
                // line 380
                echo "\t\t\t\t\t";
            }
            // line 381
            echo "\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t";
            // line 383
            if ((((isset($context["loop_i"]) ? $context["loop_i"] : null) == 0) || ((isset($context["loop_i"]) ? $context["loop_i"] : null) == (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array())) - 1)))) {
                // line 384
                echo "\t\t\t\t\t\t";
                $context["stilo2"] = "width:500px;";
                echo "  
\t\t\t\t\t";
            } else {
                // line 386
                echo "\t\t\t\t\t\t";
                $context["stilo2"] = "width:475px;";
                echo " 
\t\t\t\t\t";
            }
            // line 388
            echo "\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t<div style=\"float:left; ";
            // line 390
            echo twig_escape_filter($this->env, (isset($context["stilo"]) ? $context["stilo"] : null), "html", null, true);
            echo "\">
\t\t\t\t\t\t<p style=\"font-size:11px; font-weight:bold; text-transform:capitalize; float:left; width:100px; margin-right:10px;\">";
            // line 391
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "allSensors", array()), (isset($context["loop_i"]) ? $context["loop_i"] : null), array(), "array"), "name", array()), "html", null, true);
            echo "</p>
\t\t\t\t\t\t
\t\t\t\t\t\t<div id=\"placeholder_";
            // line 393
            echo twig_escape_filter($this->env, (isset($context["loop_i"]) ? $context["loop_i"] : null), "html", null, true);
            echo "\" style=\" height:100px; float:left; top:-20px; ";
            echo twig_escape_filter($this->env, (isset($context["stilo2"]) ? $context["stilo2"] : null), "html", null, true);
            echo "\">\t</div>
\t\t\t\t\t</div>\t\t\t\t\t\t\t\t\t\t
\t\t\t\t";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sensor'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 396
        echo "\t\t\t\t
\t\t\t\t<!-- **************************************** -->
\t\t\t\t
\t\t\t\t<!-- Gráfica Historic + Predictor -->
\t\t\t\t
\t\t\t\t";
        // line 401
        $context["mitad"] = (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array())) / 2);
        // line 402
        echo "\t\t\t\t
\t\t\t\t";
        // line 403
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["sensor"]) {
            // line 404
            echo "\t\t\t\t\t
\t\t\t\t\t";
            // line 405
            $context["loop_i"] = $this->getAttribute($context["loop"], "index0", array());
            // line 406
            echo "\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t";
            // line 408
            if (((isset($context["loop_i"]) ? $context["loop_i"] : null) < (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array())) - 1))) {
                // line 409
                echo "\t\t\t\t\t\t";
                $context["stilo"] = " height:60px;";
                // line 410
                echo "\t\t\t\t\t";
            } else {
                // line 411
                echo "\t\t\t\t\t\t";
                $context["stilo"] = "min-height:100px;";
                echo " 
\t\t\t\t\t";
            }
            // line 413
            echo "\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t";
            // line 415
            if ((((isset($context["loop_i"]) ? $context["loop_i"] : null) == 0) || ((isset($context["loop_i"]) ? $context["loop_i"] : null) == (twig_length_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array())) - 1)))) {
                // line 416
                echo "\t\t\t\t\t\t";
                $context["stilo2"] = "width:500px;";
                echo "  
\t\t\t\t\t";
            } else {
                // line 418
                echo "\t\t\t\t\t\t";
                $context["stilo2"] = "width:475px;";
                echo " 
\t\t\t\t\t";
            }
            // line 420
            echo "\t\t\t\t\t
\t\t\t\t\t
\t\t\t\t\t<div style=\"float:left; ";
            // line 422
            echo twig_escape_filter($this->env, (isset($context["stilo"]) ? $context["stilo"] : null), "html", null, true);
            echo "\">
\t\t\t\t\t\t<p style=\"font-size:11px; font-weight:bold; text-transform:capitalize; float:left; width:100px; margin-right:10px;\">";
            // line 423
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "dataJoint", array()), (isset($context["loop_i"]) ? $context["loop_i"] : null), array(), "array"), "name", array()), "html", null, true);
            echo "</p>
\t\t\t\t\t\t
\t\t\t\t\t\t<div id=\"placeHistoricPredictor_";
            // line 425
            echo twig_escape_filter($this->env, (isset($context["loop_i"]) ? $context["loop_i"] : null), "html", null, true);
            echo "\" style=\" height:100px; float:left; top:-20px; ";
            echo twig_escape_filter($this->env, (isset($context["stilo2"]) ? $context["stilo2"] : null), "html", null, true);
            echo "\">\t</div>
\t\t\t\t\t</div>\t\t\t\t\t\t\t\t\t\t
\t\t\t\t";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sensor'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 428
        echo "\t\t\t\t
\t\t\t</div>\t\t\t
\t</div>  



";
    }

    public function getTemplateName()
    {
        return "OptimusOptimusBundle:Graph:graphPredictions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1038 => 428,  1019 => 425,  1014 => 423,  1010 => 422,  1006 => 420,  1000 => 418,  994 => 416,  992 => 415,  988 => 413,  982 => 411,  979 => 410,  976 => 409,  974 => 408,  970 => 406,  968 => 405,  965 => 404,  948 => 403,  945 => 402,  943 => 401,  936 => 396,  917 => 393,  912 => 391,  908 => 390,  904 => 388,  898 => 386,  892 => 384,  890 => 383,  886 => 381,  883 => 380,  880 => 379,  877 => 378,  874 => 377,  872 => 376,  868 => 374,  866 => 373,  863 => 372,  846 => 371,  843 => 370,  841 => 369,  828 => 359,  823 => 357,  818 => 355,  815 => 354,  812 => 353,  809 => 352,  806 => 351,  803 => 350,  801 => 349,  798 => 348,  795 => 347,  792 => 346,  789 => 345,  786 => 344,  784 => 343,  781 => 342,  778 => 341,  775 => 340,  772 => 339,  769 => 338,  767 => 337,  758 => 331,  754 => 330,  692 => 270,  660 => 252,  642 => 247,  638 => 245,  631 => 241,  625 => 238,  622 => 237,  616 => 235,  610 => 234,  591 => 219,  589 => 218,  585 => 216,  570 => 215,  566 => 213,  564 => 212,  559 => 210,  556 => 209,  538 => 207,  534 => 205,  532 => 204,  526 => 202,  524 => 201,  520 => 200,  517 => 199,  514 => 198,  512 => 197,  507 => 196,  505 => 195,  501 => 194,  498 => 193,  495 => 192,  478 => 191,  475 => 190,  473 => 189,  469 => 187,  452 => 186,  445 => 183,  443 => 182,  439 => 181,  434 => 180,  417 => 179,  410 => 174,  378 => 156,  360 => 151,  356 => 149,  349 => 145,  343 => 142,  340 => 141,  334 => 139,  328 => 138,  309 => 123,  307 => 122,  304 => 121,  299 => 118,  297 => 117,  292 => 115,  289 => 114,  271 => 113,  267 => 111,  265 => 110,  259 => 108,  257 => 107,  253 => 106,  250 => 105,  247 => 104,  245 => 103,  240 => 102,  238 => 101,  234 => 100,  231 => 99,  228 => 98,  211 => 97,  204 => 94,  202 => 93,  198 => 92,  193 => 91,  176 => 90,  158 => 77,  104 => 26,  100 => 24,  97 => 23,  91 => 20,  85 => 17,  81 => 16,  77 => 15,  73 => 14,  68 => 12,  63 => 10,  59 => 9,  55 => 8,  51 => 7,  46 => 5,  41 => 4,  38 => 3,  11 => 1,);
    }
}
