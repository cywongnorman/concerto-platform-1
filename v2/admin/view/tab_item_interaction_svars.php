<?php
/*
  Concerto Testing Platform,
  Web based adaptive testing platform utilizing R language for computing purposes.

  Copyright (C) 2011  Psychometrics Centre, Cambridge University

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<?php
if (!isset($ini)) {
    require_once '../../model/Ini.php';
    $ini = new Ini();
}
$user = User::get_logged_user();
if ($user == null)
    die(Language::string(85));

$names = isset($_POST['names']) ? $_POST['names'] : array();
$sources = isset($_POST['sources']) ? $_POST['sources'] : array();

$i = 0;
for ($i = 0; $i < count($names); $i++) {
    $name = $names[$i];
    $source = $sources[$i];

    if ($name == "")
        continue;
    ?>

    <tr>
        <td class="ui-widget-content ui-corner-all ui-state-highlight"><b><?= $name ?></b></td>
        <td class="ui-widget-content ui-corner-all"><?= $source ?></td>
    </tr>

    <?php
}
if (!isset($name) || $name == "") {
    ?>
    <tr><td align="center" colspan="2"><?= Language::string(9) ?></td></tr>
<?php } ?>