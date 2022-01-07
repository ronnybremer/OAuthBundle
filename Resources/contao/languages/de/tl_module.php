<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

$array = &$GLOBALS['TL_LANG']['tl_module']['c4g_oauth']['fields'];
$array['type'] = array('Loginanbieter', 'Hier den Anbieter auswählen, welcher für den Login genutzt werden soll.');
$array['type_oidc'] = 'OpenID Connect';
$array['btn_name'] = array('Beschriftung des Login Buttons', 'Hier kann ein Buttontext für die Anmeldung hinterlegt werden.');
$array['oauth_reg_groups'] = array('Mitgliedergruppen', 'Hier können Sie das Mitglied einer oder mehreren Gruppen zuweisen.');

$array['memberMapping'] = array("Abbildung OAuth Daten auf Contao Mitglieder", "Hier können Contao Mitgliederfelder mit den Daten vom OAuth-Server verknüpft werden. Die Daten werden dann intern am Contao-Mitglied gespeichert.");
$array['contaoField'] = array("Contao Mitgliederfeld");
$array['oauthField'] = array("OAuth Bezeichnung");