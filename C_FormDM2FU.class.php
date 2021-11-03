<?php

/**
 * diabetes_follow_up form
 * - This initial version simply copies the SOAP form but renames SOAP everywhere to 
 *   to reflect that this is a new form.  If this form is working correctly, then
 *   I plan to make gradual modifications to transform this form into a complete
 *   diabetes_follow_up form.  My hope is to be able to do everything I need to be
 *   able to do within OpenEMR forms.
 *
 * - General plan for this form:
 *   1. Prove the concept by simply renaming "SOAP" to "DM2FU" or another suitable name
 *      throughout the code of the SOAP form files
 *   2. Install and register the new DM2FU form on a AWS production system (after backing up
 *      the production system EC2 as an image).
 *   3. Gradually (one field at a time) incorporate some features of the following SOAPnote
 *      project form:
 *      https://www.soapnote.org/endocrine-metabolic/diabetes-management-clinic-2/
 *      - Develop and Test the form code on a test AWS system that has a web server
 *        prior to incorporating the changes into the new DM2FU under development.
 *   5. The above Consider creating my own SOAPnote project form to reflect what I would like
 *      in a Diabetes Mellitus Type 2 follow-up form.  Consider incorporating this into
 *      the evolving DM2 follow-up form.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @author    Ralf Lukner MD PhD <lukner@gmail.com>
 * @copyright Copyright (c) 2019 Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2021 Ralf Lukner MD PhD <lukner@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once($GLOBALS['fileroot'] . "/library/forms.inc");
require_once("FormDM2FU.class.php");

use OpenEMR\Common\Csrf\CsrfUtils;

class C_FormDM2FU extends Controller
{

    var $template_dir;

    function __construct($template_mod = "general")
    {
        parent::__construct();
        $this->template_mod = $template_mod;
        $this->template_dir = dirname(__FILE__) . "/templates/";
        $this->assign("FORM_ACTION", $GLOBALS['web_root']);
        $this->assign("DONT_SAVE_LINK", $GLOBALS['form_exit_url']);
        $this->assign("STYLE", $GLOBALS['style']);
        $this->assign("CSRF_TOKEN_FORM", CsrfUtils::collectCsrfToken());
    }

    function default_action()
    {
        $form = new FormDM2FU();
        $this->assign("data", $form);
        return $this->fetch($this->template_dir . $this->template_mod . "_new.html");
    }

    function view_action($form_id)
    {
        if (is_numeric($form_id)) {
            $form = new FormDM2FU($form_id);
        } else {
            $form = new FormDM2FU();
        }

        $dbconn = $GLOBALS['adodb']['db'];

        $this->assign("data", $form);

        return $this->fetch($this->template_dir . $this->template_mod . "_new.html");
    }

    function default_action_process()
    {
        if ($_POST['process'] != "true") {
            return;
        }

        $this->form = new FormDM2FU($_POST['id']);
        parent::populate_object($this->form);

        $this->form->persist();
        if ($GLOBALS['encounter'] == "") {
            $GLOBALS['encounter'] = date("Ymd");
        }

        if (empty($_POST['id'])) {
            addForm($GLOBALS['encounter'], "DM2 F/U", $this->form->id, "dm2fu", $GLOBALS['pid'], $_SESSION['userauthorized']);
            $_POST['process'] = "";
        }

        return;
    }
}
