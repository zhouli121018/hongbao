<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* display/export/options_quick_export.twig */
class __TwigTemplate_5403af2eb80f3c38b880472e89cfa758d0c1ba6a648232f97f982bd4ba7957ab extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<div class=\"exportoptions\" id=\"output_quick_export\">
    <h3>";
        // line 2
        echo _gettext("Output:");
        echo "</h3>
    <ul>
        <li>
            <input type=\"checkbox\" name=\"quick_export_onserver\" value=\"saveit\"
                id=\"checkbox_quick_dump_onserver\"";
        // line 6
        echo ((($context["export_is_checked"] ?? null)) ? (" checked") : (""));
        echo ">
            <label for=\"checkbox_quick_dump_onserver\">
                ";
        // line 8
        echo sprintf(_gettext("Save on server in the directory <strong>%s</strong>"), twig_escape_filter($this->env, ($context["save_dir"] ?? null)));
        echo "
            </label>
        </li>
        <li>
            <input type=\"checkbox\" name=\"quick_export_onserver_overwrite\"
                value=\"saveitover\" id=\"checkbox_quick_dump_onserver_overwrite\"";
        // line 14
        echo ((($context["export_overwrite_is_checked"] ?? null)) ? (" checked") : (""));
        echo ">
            <label for=\"checkbox_quick_dump_onserver_overwrite\">
                ";
        // line 16
        echo _gettext("Overwrite existing file(s)");
        // line 17
        echo "            </label>
        </li>
    </ul>
</div>
";
    }

    public function getTemplateName()
    {
        return "display/export/options_quick_export.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  60 => 17,  58 => 16,  53 => 14,  45 => 8,  40 => 6,  33 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "display/export/options_quick_export.twig", "/data/wwwroot/default/phpMyAdmin/templates/display/export/options_quick_export.twig");
    }
}
