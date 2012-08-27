<?php


/**
 * @version     v1.2.0
 * @category    Joomla library
 * @package	    THM_Groups
 * @subpackage  com_thm_quickpages.admin
 * @author	    Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link		www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// Load tooltip behavior
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld'); ?>" method="post" name="adminForm">
	<table class="adminlist">
		<thead></thead>
		<tfoot></tfoot>
		<tbody></tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<h1>Beschreibung und Abhängigkeiten</h1>
<p>
Dieser Teil von <b>THM Groups</b> ersetzt die ehemalige Komponente <b>THM Quickpages</b>. Sofern Sie schon
<b>THM Quickpages</b> benutzt haben und die erstellen Seiten weiterhin genutzt werden sollen, wird empfohlen,
die Komponente <b>THM Quickpages</b> auf die Version 1.2.0 (oder höher) zu aktualisieren und erst anschließend
zu deinstallieren. Dies soll verhindern, dass die notwendigen Daten zur weiterbenutzung der Seiten verlohren gehen.
</p>
<p>
Zum Umfang dieser Funktion gehört ebenfalls das Modul <b>THM Quickpages Linklist</b> (ab Version 1.2.0),
das den eingestellten Content listenartig anzeigen kann. Es fungiert als Navigation und Inhaltsverzeichnis für
eine Quickpage.<br/>
Grundlegend für die Komponente sowie das Modul ist zudem die Library <b>THM Quickpages Library</b> (ab Version 1.2.0),
welche <b>unbedingt installiert sein muss</b>.<br/>
Sofern diese zugehörigen Erweiterungen für die Komponente <b>THM Quickpages</b> bereits installiert waren,
sollten Sie diese aktualisiern, um einen reibungslosen Verlauf zu ermöglichen.
</p>
<p>
Optional kann außerdem die Komponente THM Repository eingebunden werden.
Dieser Teil wurde darauf ausgelegt,
die Funktionalität dieser Komponente auch für einzelne Quickpages zur Verfügung zu stellen,
indem entsprechende Kategorien angelegt werden (Siehe nachfolgende Beschreibung).
Beide Komponente können prinzipiell aber auch unabhängig betrieben werden.
</p>

<h2>Quickpages</h2>
<p>
Um die Quickpage nutzen zu können, muss diese über das Nutzer-Profil von <b>THM Groups</b> freigeschaltet werden.
</p>
<p>
Die Pflege der Quickpage wird dem Benutzer überlassen und passiert vollständig
im Frontend. Hierzu dient eine Administrationsoberfläche, die der Administration
der Komponente <i>Content</i> im Backend nachempfunden wurde. Der Benutzer kann
folgende Operationen durchführen um seinen Content zu verwalten:
</p>
<ul>
    <li>Anlegen</li>
    <li>Bearbeiten</li>
    <li>Sortieren</li>
    <li>Un-/Veröffentlichen</li>
    <li>Löschen</li>
    <li>Wiederherstellen</li>
</ul>

<h3>Backend-Konfiguration</h3>
<p>
Bitte vergessen Sie nicht, die Konfiguration Root-Kategorie der Quickpages im
Optionsmenü (Siehe oben rechts auf dieser Seite):
</p>
<p>
    <ol>
        <li>
            Erstellen Sie eine neue Kategorie (z.B. <i>Quickpages</i>) über den
            Category-Manager, unter der die Quickpages geführt werden. Wir
            empfehlen dafür eine Root-Kategorie (ohne Parent-Kategorie).<br/>
            <a href="index.php?option=com_categories&task=category.add&extension=com_content">
                Neue Kategorie hinzufügen
            </a>
            <br /><br />
        </li>
        <li>
			<b>Falls die Komponente THM Repository installiert ist:</b><br/>
            Erstellen Sie eine neue Kategorie (z.B. <i>Quickpage-Repositories</i>)
			über den Category-Manager, unter der die Repositories f&uuml;r
			die einzelnen Quickpages geführt werden.
			Wir empfehlen auch dafür eine Root-Kategorie (ohne Parent-Kategorie).<br/>
			<b>Wichtig:</b> Die Kategorie muss für Komponente THM Repository angelegt werden,
			benutzen Sie bitte folgenden Link:<br/>
            <a href="index.php?option=com_categories&task=category.add&extension=com_thm_repository">
                Neue Kategorie hinzufügen
            </a>
            <br /><br />
        </li>
        <li>
			Öffnen Sie das Optionsmenü (Siehe oben rechts auf dieser Seite) und
			legen Sie die zuvor angelegten Kategorien im Reiter "Wurzelkategorien" entsprechend fest.
        </li>
    </ol>
</p>
<p>
Im Anschluss an die Erstellung und Konfiguration der Kategorien muss nun einer bestimmten Gruppe
die Schreib-Lese-Rechte darauf zugewiesen werden.
</p>

<h2>Modul THM Quickpages Linklist</h2>
<p>
Das Modul THM Quickpages Linklist listet alle Beiträge einer Quickpage auf und verlinkt auf diese.
Die Links werden so sortiert, wie sie in der Quickpage-Administration im Frontend eingestellt wurden.
</p>

<h2>Hinweise</h2>
<p>
Falls die Komponente THM Repository erst installiert wird,
nachdem bereits Quickpages angelegt wurden,
so muss Schritt 2 und 3 der Backend-Konfiguration unbedingt nachgeholt werden.
</p>
<p>
Zur weiteren Verwendung existierender Quickpages
sollten Sie zuerst die Komponete <b>THM Quickpages</b> auf die Version 1.2.0 (oder höher)
aktualisieren und erst anschließend diese deinstallieren, da sonst wichtige Daten verlohren gehen können.
Mit der Umstellung der Quickpages über <b>THM Groups</b> ist es nötig, die zugehörigen Erweiterungen
<b>THM Quickpages Linklist</b> (sofern installiert) und <b>THM Quickpages Library</b> auf die Version 1.2.0 (oder höher)
zu aktualisieren.
</p>


