<?php
/*
  Concerto Platform - Online Adaptive Testing Platform
  Copyright (C) 2011-2012, The Psychometrics Centre, Cambridge University

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; version 2
  of the License, and not any of the later versions.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if (!isset($ini)) {
    require_once'../../Ini.php';
    $ini = new Ini();
}
$logged_user = User::get_logged_user();
if ($logged_user == null) {
    echo "<script>location.reload();</script>";
    die(Language::string(278));
}
?>
<script>
    $(function(){
        Methods.iniTooltips();
    })
</script>
<?php
$vals = $_POST['value'];
if (array_key_exists('oid', $_POST) && $_POST['oid'] != 0) {
    $section = TestSection::from_mysql_id($_POST['oid']);
    $vals = $section->get_values();
}

// 0 - html
// 1 - params_count
// 2 - returns_count
// vars

$description = Language::string(485);

$qti = QTIAssessmentItem::from_mysql_id($vals[0]);
if ($qti != null) {
    $validation = $qti->validate();
    $description.=" " . Language::string(486) . ":<hr/>" . $qti->get_description();
}
?>

<div class="divSectionSummary sortableHandle">
    <table class="fullWidth tableSectionHeader">
        <tr>
            <!--<td class="tdSectionColumnIcon"></td>-->
            <td class="ui-widget-header tdSectionColumnCounter" id="tooltipSectionDetail_<?= $_POST['counter'] ?>" title=""><?= $_POST['counter'] ?></td>
            <td class="tdSectionColumnIcon"><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= DS_TestSectionType::get_description_by_id(13) ?>"></span></td>
            <td class="tdSectionColumnIcon"><span id="spanExpandDetail_<?= $_POST['counter'] ?>" class="spanExpandDetail spanIcon ui-icon ui-icon-folder-<?= $_POST['detail'] == 1 ? "open" : "collapsed" ?> tooltip" title="<?= Language::string(390) ?>" onclick="Test.uiToggleDetails(<?= $_POST['counter'] ?>)"></span></td>
            <td class="tdSectionColumnType"><?= DS_TestSectionType::get_name_by_id(13) ?></td>
            <td class="tdSectionColumnAction">
                <table class="fullWidth">
                    <tr>
                        <td>
                            <span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= htmlspecialchars(Template::strip_html($description), ENT_QUOTES) ?>"></span>
                        </td>
                        <?php if (isset($vals[0]) && $vals[0] != 0) { ?>
                            <td>
                                <span class="spanIcon ui-icon ui-icon-extlink tooltip" title="<?= Language::string(522) ?>" onclick="Test.uiGoToRelatedObject(<?= $_POST['counter'] ?>,<?= $vals[0] ?>)"></span>
                            </td>
                        <?php } ?>
                        <td class="fullWidth">
                            <select id="selectQTIAssessmentItem_<?= $_POST['counter'] ?>" class="fullWidth ui-widget-content ui-corner-all fullWidth" onchange="Test.uiRefreshSectionContent(<?= $_POST['type'] ?>, <?= $_POST['counter'] ?>, Test.getSectionValues(Test.sectionDivToObject($('#divSection_<?= $_POST['counter'] ?>'))))">
                                <option value="0">&lt;<?= Language::string(73) ?>&gt;</option>
                                <?php
                                $sql = $logged_user->mysql_list_rights_filter("QTIAssessmentItem", "`name` ASC");
                                $z = mysql_query($sql);
                                while ($r = mysql_fetch_array($z)) {
                                    $t = QTIAssessmentItem::from_mysql_id($r[0]);
                                    ?>
                                    <option value="<?= $t->id ?>" <?= ($vals[0] == $t->id ? "selected" : "") ?>><?= $t->name ?> ( <?= $t->get_system_data() ?> )</option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="tdSectionColumnIcon"><span class="spanIcon ui-icon ui-icon-newwin tooltip" title="<?= Language::string(511) ?>" onclick="Test.duplicateSection(<?= $_POST['counter'] ?>)"></span></td>
            <td class="tdSectionColumnIcon"><span class="spanIcon ui-icon ui-icon-script tooltip" title="<?= Language::string(447) ?>" onclick="Test.convertToLowerLevel(<?= $_POST['counter'] ?>)"></span></td>
            <td class="tdSectionColumnEnd"><input type="checkbox" id="chkEndSection_<?= $_POST['counter'] ?>" class="chkEndSection tooltip" <?= $_POST['end'] == 1 ? "checked" : "" ?> title="<?= Language::string(369) ?>" /></td>
            <td class="tdSectionColumnIcon"><span class="spanIcon tooltip ui-icon ui-icon-trash" onclick="Test.uiRemoveSection(<?= $_POST['counter'] ?>)" title="<?= Language::string(59) ?>"></span></td>
            <td class="tdSectionColumnIcon"><span class="spanIcon tooltip ui-icon ui-icon-plus" onclick="Test.uiAddLogicSection(0,<?= $_POST['counter'] ?>)" title="<?= Language::string(60) ?>"></span></td>
        </tr>
    </table>
</div>
<div class="divSectionDetail <?= $_POST['detail'] == 1 || $_POST['oid'] == 0 ? "" : "notVisible" ?>">
    <?php
    if ($vals[0] != 0 && $qti != null) {
        ?>
        <table class="fullWidth">
            <tr>
                <td style="width:100%;" valign="top" align="center">
                    <fieldset class="ui-widget-content">
                        <legend class="" align="center"><b><?= Language::string(113) ?>:</b></legend>
                        <div>
                            <table>
                                <?php
                                $outputs = $qti->get_outputs();

                                for ($i = 0; $i < count($outputs); $i++) {
                                    $ret = $outputs[$i]["name"];

                                    for ($j = 0; $j < count($outputs) * 2; $j = $j + 2) {
                                        if (isset($vals[1 + $j]) && isset($vals[1 + 1 + $j]) && $vals[1 + $j] == $outputs[$i]["name"] && $vals[1 + $j] != "") {
                                            $ret = $vals[1 + $j + 1];
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="tdSectionColumnIcon"><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= Language::string(217) ?>: <b><?= $outputs[$i]["type"] ?></b>"></span></td>
                                        <td><?= $outputs[$i]["name"] ?></td>
                                        <td class="noWrap tdVarPointer"><b>->></b></td>
                                        <td class="tdVarPointer"><input referenced="<?= $outputs[$i]["name"] ?>" onchange="Test.uiSetVarNameChanged($(this))" type="text" class="ui-state-focus comboboxSetVars comboboxVars controlValue<?= $_POST['counter'] ?>_rets ui-widget-content ui-corner-all fullWidth" value="<?= htmlspecialchars($ret, ENT_QUOTES) ?>" /></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table> 
                        </div>
                        <div class="notVisible">
                            <?php
                            for ($i = 0; $i < count($outputs); $i++) {
                                ?>
                                <input class="inputReturnVar" type="hidden" value="<?= $outputs[$i]['name'] ?>" />
                                <?php
                            }
                            ?>
                        </div>
                    </fieldset>
                </td>
            </tr>
        </table>
        <?php
    }
    ?>
</div>