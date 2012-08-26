<?php
/**
 * default template file for HelloWorlds view of HelloWorld component
 *
 * @version		$Id: default.php 59 2010-11-27 14:17:52Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
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

<h1>Beschreibung</h1>
<p>
Die Joomla!-Komponente <b>THM Quickpage</b> ist ein ReWrite der MND Komponente
<b>MND Beitragsliste</b> für die Joomla!-Version 1.6.
Hierbei wurden alle Kernfunktionen der Komponente übernommen oder erweitert.
</p>
<p>
Ziel ist es, Professoren, Mitarbeitern und Instituten eine einfache und schnelle
Möglichkeit bereit zu stellen, um sich innerhalb der THM-Webseite zu
präsentieren und/oder eigene Beiträge zu veröffentlichen.
</p>
<p>
Zum Umfang der Komponente gehört ebenfalls das Modul <b>THM Quickpages Linklist</b>,
das den eingestellten Content listenartig anzeigen kann. Es fungiert als
Navigation und Inhaltsverzeichnis für eine Quickpage.<br/>
Grundlegend für die Komponente sowie das Modul ist zudem die Library <b>THM Quickpages Library</b>,
welche unbedingt installiert sein muss.
</p>

<h2>Andere Abhängigkeiten</h2>
<p>
Die Komponente THM Quickpages setzt voraus, dass die Komponente THM Groups
installiert ist.
Technisch ist die Abhängigkeit durch einige Parameter und
insbesondere den Tabellen vom THM Groups bedingt.
</p>
<p>
Optional kann außerdem die Komponente THM Repository eingebunden werden.
THM Quickpages ist darauf ausgelegt,
die Funktionalität dieser Komponente auch für einzelne Quickpages zur Verfügung zu stellen,
indem entsprechende Kategorien angelegt werden (Siehe nachfolgende Beschreibung).
Beide Komponente können prinzipiell aber auch unabhängig betrieben werden.
</p>

<h2>Komponente THM Quickpages</h2>
<p>
Um die Quickpage nutzen zu können, muss diese über das Nutzer-Profil
(Zur Verfügung gestellt von THM Groups) freigeschaltet werden.
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
    die Schreib-Lese-Rechte darauf zugewiesen werden. Dies geschieht indem man die Eigenschaften der
    Kategorien bearbeitet und die Zugriffsrechte zuweist.
</p>

<h3>Funktionsweise</h3>
<p>
Der Benutzer kann im Frontend Beiträge anlegen.
Jeder Beitrag stellt eine Unterseite einer Quickpage dar.
Die Beiträge werden in einer Kategorie zusammengefasst,
die genau einer Quickpage zugeordnet wird.
Jeder Benutzer und jede Gruppe der THM Groups kann genau eine eigene Quickpage erhalten,
sofern diese in deren Profil entsprechend aktiviert ist.
</p>
<p>
Die Kategorien werden dynamisch beim Aufruf der Frontend-Administration der Quickpages angelegt,
sofern diese noch nicht existieren.
Alle diese Kategorien sind Sub-Kategorien der Quickpages-Wurzelkategorie.
</p>
<p>
Falls die Komponente THM Repository installiert ist,
werden auf ähnliche Weise dynamisch Kategorien für diese Komponente erzeugt.
Diese werden als Sub-Kategorien der Quickpages-Repository-Wurzelkategorie angelegt.
</p>

<h2>Modul THM Quickpages Linklist</h2>
<p>
Das Modul THM Quickpages Linklist listet alle Beiträge einer Quickpage auf und verlinkt auf diese.
Die Links werden so sortiert, wie sie in der Quickpage-Administration im Frontend eingestellt wurden.
</p>

<h2>Hinweis</h2>
<p>
    Falls die Komponente THM Repository erst installiert wird,
	nachdem bereits Quickpages angelegt wurden,
	so muss Schritt 2 und 3 der Backend-Konfiguration unbedingt nachgeholt werden.
</p>


