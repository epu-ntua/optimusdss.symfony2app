<?php

/* OptimusOptimusBundle:Graph:actionPlan.html.twig */
class __TwigTemplate_8754fab550d263938ab53575e6775506b3ef0df1a1337a3132f23d8216cc6b47 extends Twig_Template
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
            'clase_ActionPlanData' => array($this, 'block_clase_ActionPlanData'),
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
    public function block_clase_ActionPlanData($context, array $blocks = array())
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
\t\t
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
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px; margin:0px; width:50px; border-right:1px solid #7f7f7f; text-align: right;\"><strong>Dates:</strong></p>
\t\t\t\t
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px 0 10px 10px; margin:0px; width:160px; border-right:1px solid #7f7f7f; text-align:center; background-color: #e8eef8;\">
\t\t\t\t\t<span id=\"startDate\">";
        // line 149
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "startDate", array()), "html", null, true);
        echo "</span> / 
\t\t\t\t\t<span id=\"endDate\">";
        // line 150
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "endDate", array()), "html", null, true);
        echo "</span>
\t\t\t\t</p>
\t\t\t\t<p style=\"background-color:#e8eef8; float:left; margin:0px; width:40px; height:35px; text-align:center; font-size:16px; line-height:2.3; border-right:1px solid #7f7f7f; cursor:pointer;\" onclick=\"displayCalendar();\">+</p>
\t\t\t\t
\t\t\t\t<!--<p style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 0px; margin:0px; width:160px; border-right:1px solid #7f7f7f; text-align:right;\"><strong>Time scope: </strong></p>
\t\t\t\t
\t\t\t\t";
        // line 156
        if (($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()) == "day")) {
            // line 157
            echo "\t\t\t\t\t";
            $context["stileDay"] = "background-color: #2e75b6; color:#fff;";
            // line 158
            echo "\t\t\t\t";
        } else {
            // line 159
            echo "\t\t\t\t\t";
            $context["stileDay"] = "background-color: #e8eef8; color:#44546a;";
            // line 160
            echo "\t\t\t\t";
        }
        // line 161
        echo "\t\t\t\t
\t\t\t\t";
        // line 162
        if (($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()) == "week")) {
            // line 163
            echo "\t\t\t\t\t";
            $context["stileWeek"] = "background-color: #2e75b6; color:#fff;";
            // line 164
            echo "\t\t\t\t";
        } else {
            // line 165
            echo "\t\t\t\t\t";
            $context["stileWeek"] = "background-color: #e8eef8; color:#44546a;";
            // line 166
            echo "\t\t\t\t";
        }
        // line 167
        echo "\t\t\t\t
\t\t\t\t";
        // line 168
        if (($this->getAttribute((isset($context["allData"]) ? $context["allData"] : null), "timeSelected", array()) == "month")) {
            // line 169
            echo "\t\t\t\t\t";
            $context["stileMonth"] = "background-color: #2e75b6; color:#fff; ";
            // line 170
            echo "\t\t\t\t";
        } else {
            // line 171
            echo "\t\t\t\t\t";
            $context["stileMonth"] = "background-color: #e8eef8; color:#44546a;";
            // line 172
            echo "\t\t\t\t";
        }
        echo "-->
\t\t\t\t
\t\t\t\t<!--<p id=\"selectDay\" style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 10px; margin:0px;  border-right:1px solid #7f7f7f; text-align:center; ";
        // line 174
        echo twig_escape_filter($this->env, (isset($context["stileDay"]) ? $context["stileDay"] : null), "html", null, true);
        echo " cursor:pointer;\" onclick=\"changeTo('day');\">day</p>-->
\t\t\t\t
\t\t\t\t<!--<p id=\"selectWeek\" style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 10px; margin:0px;  border-right:1px solid #7f7f7f; text-align:center; ";
        // line 176
        echo twig_escape_filter($this->env, (isset($context["stileWeek"]) ? $context["stileWeek"] : null), "html", null, true);
        echo " cursor:pointer;\" onclick=\"changeTo('week');\">week</p>-->
\t\t\t\t
\t\t\t\t<!--<p id=\"selectMonth\" style=\"float:left; margin-right:0px; height:15px; padding:10px 10px 10px 10px; margin:0px;  border-right:1px solid #7f7f7f; text-align:center; ";
        // line 178
        echo twig_escape_filter($this->env, (isset($context["stileMonth"]) ? $context["stileMonth"] : null), "html", null, true);
        echo " cursor:pointer;\" onclick=\"changeTo('month');\">month</p>-->
\t\t\t\t
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px; margin:0px; width:160px; border-right:1px solid #7f7f7f;  text-align: right;\"><strong>Visualization:</strong></p>
\t\t\t\t
\t\t\t\t
\t\t\t</div>
\t\t\t<div id=\"datepicker\" style=\" margin-bottom:40px; float:left; background-color:#d0cece;\"></div>
\t\t\t<div id=\"indicators\" style=\"background-color:#d0cece; width:100%;  overflow:hidden; border-bottom:1px solid #7f7f7f;\">
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px; margin:0px; width:50px; border-right:1px solid #7f7f7f;  text-align: right;\"><strong>Profile:</strong></p>
\t\t\t\t
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px; margin:0px; width:150px; border-right:1px solid #7f7f7f;  text-align: right;\"></p>
\t\t\t\t
\t\t\t\t<p style=\"float:left; margin-right:0px; height:15px; padding:10px; margin:0px; width:201px; border-right:1px solid #7f7f7f;  text-align: right;\"><strong>Num. panels:</strong></p>
\t\t\t</div>
\t\t\t
\t\t\t<div id=\"contentGraficas\">
\t\t\t\t<!-- tabla de valores -->
\t\t\t\t<div class=\"contentTableResults\">
\t\t\t\t\t<table border=\"0\">
\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t<tr class=\"headerTemp\">
\t\t\t\t\t\t\t\t<th class=\"firstColumnHeaderTemp\" colspan=\"2\">Ext. Temp</th>
\t\t\t\t\t\t\t\t<th>12º C</th>
\t\t\t\t\t\t\t\t<th>13º C</th>
\t\t\t\t\t\t\t\t<th>14º C</th>
\t\t\t\t\t\t\t\t<th>15º C</th>
\t\t\t\t\t\t\t\t<th>16º C</th>
\t\t\t\t\t\t\t\t<th>17º C</th>
\t\t\t\t\t\t\t\t<th>18º C</th>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</thead>
\t\t\t\t\t\t<tbody>
\t\t\t\t\t\t\t<tr class=\"headerDays\">
\t\t\t\t\t\t\t\t<td colspan=\"2\"></td>
\t\t\t\t\t\t\t\t<td>Mon</td>
\t\t\t\t\t\t\t\t<td>Tue</td>
\t\t\t\t\t\t\t\t<td>Wed</td>
\t\t\t\t\t\t\t\t<td>Thu</td>
\t\t\t\t\t\t\t\t<td>Fri</td>
\t\t\t\t\t\t\t\t<td>Sat</td>
\t\t\t\t\t\t\t\t<td>Sunday</td>\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td colspan=\"2\">Production capacity (KW)</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td colspan=\"2\">Average consume (KW)</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td colspan=\"2\">Purchase energy price (€/KWh)</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"lastFirstBlock\" colspan=\"2\">Selling energy price (€/KWh)</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>
\t\t\t\t\t\t\t\t<td>25.000</td>\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t
\t\t\t\t\t\t<!--</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>
\t\t\t\t<div style=\"width:100%; font-size:11px;\" onclick=\"\$(this).hide();\">
\t\t\t\t\t<table style=\"width:100%; border-collapse: collapse; border:1px solid;\" border=\"0\">
\t\t\t\t\t\t<tbody>-->
\t\t\t\t\t\t";
        // line 267
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable(range(0, 23));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 268
            echo "\t\t\t\t\t\t\t<tr class=\"showDiv\">
\t\t\t\t\t\t\t\t";
            // line 269
            if (($context["i"] == 0)) {
                echo "<td class=\"firstCellShowDiv\" rowspan=\"24\">Daily Energy Cost</td>
\t\t\t\t\t\t\t\t";
            }
            // line 271
            echo "\t\t\t\t\t\t\t\t<td class=\"secondCellShowDiv\" style=\"\">";
            echo twig_escape_filter($this->env, $context["i"], "html", null, true);
            echo ":00 - ";
            echo twig_escape_filter($this->env, ($context["i"] + 1), "html", null, true);
            echo ":00 h</td>
\t\t\t\t\t\t\t\t<td>1</td>
\t\t\t\t\t\t\t\t<td>1</td>
\t\t\t\t\t\t\t\t<td>1</td>
\t\t\t\t\t\t\t\t<td>1</td>
\t\t\t\t\t\t\t\t<td>1</td>
\t\t\t\t\t\t\t\t<td>1</td>
\t\t\t\t\t\t\t\t<td>1</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 281
        echo "\t\t\t\t\t<!--\t</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>
\t\t\t\t<div style=\"width:100%; font-size:11px;\">
\t\t\t\t\t<table style=\"width:100%;\">
\t\t\t\t\t\t<tbody>\t-->\t\t\t\t\t\t
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"scheduleStart\" colspan=\"2\">Schedule start</td>
\t\t\t\t\t\t\t\t<td class=\"value\"></td>
\t\t\t\t\t\t\t\t<td class=\"value\"></td>
\t\t\t\t\t\t\t\t<td class=\"value\"></td>
\t\t\t\t\t\t\t\t<td class=\"value\"></td>
\t\t\t\t\t\t\t\t<td class=\"value\">13:00</td>
\t\t\t\t\t\t\t\t<td class=\"value\">8:00</td>
\t\t\t\t\t\t\t\t<td class=\"value\">8:00</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"scheduleEnd\" colspan=\"2\">Schedule end</td>
\t\t\t\t\t\t\t\t<td class=\"value\"></td>
\t\t\t\t\t\t\t\t<td class=\"value\"></td>
\t\t\t\t\t\t\t\t<td class=\"value\"></td>
\t\t\t\t\t\t\t\t<td class=\"value\"></td>
\t\t\t\t\t\t\t\t<td class=\"value\">18:00</td>
\t\t\t\t\t\t\t\t<td class=\"value\">18:00</td>
\t\t\t\t\t\t\t\t<td class=\"value\">18:00</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"value\" colspan=\"2\">Daily accumulated gain</td>
\t\t\t\t\t\t\t\t<td class=\"value\">0€</td>
\t\t\t\t\t\t\t\t<td class=\"value\">0€</td>
\t\t\t\t\t\t\t\t<td class=\"value\">0€</td>
\t\t\t\t\t\t\t\t<td class=\"value\">0€</td>
\t\t\t\t\t\t\t\t<td class=\"value\">0€</td>
\t\t\t\t\t\t\t\t<td class=\"value\">0€</td>
\t\t\t\t\t\t\t\t<td class=\"value\">0€</td>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<td class=\"value\" colspan=\"2\">Total week accumulated gain</td>
\t\t\t\t\t\t\t\t<td colspan=\"7\" class=\"finalValue\">240 €</td>\t\t\t\t\t\t
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</tbody>
\t\t\t\t\t</table>
\t\t\t\t</div>
\t\t\t</div>\t\t\t
\t</div>  



";
    }

    public function getTemplateName()
    {
        return "OptimusOptimusBundle:Graph:actionPlan.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  432 => 281,  413 => 271,  408 => 269,  405 => 268,  401 => 267,  309 => 178,  304 => 176,  299 => 174,  293 => 172,  290 => 171,  287 => 170,  284 => 169,  282 => 168,  279 => 167,  276 => 166,  273 => 165,  270 => 164,  267 => 163,  265 => 162,  262 => 161,  259 => 160,  256 => 159,  253 => 158,  250 => 157,  248 => 156,  239 => 150,  235 => 149,  158 => 77,  104 => 26,  100 => 24,  97 => 23,  91 => 20,  85 => 17,  81 => 16,  77 => 15,  73 => 14,  68 => 12,  63 => 10,  59 => 9,  55 => 8,  51 => 7,  46 => 5,  41 => 4,  38 => 3,  11 => 1,);
    }
}
