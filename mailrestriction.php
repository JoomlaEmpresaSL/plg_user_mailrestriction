<?php
/*
 *      User Mail Restriction Plug-in
 *      @package User Mail Restriction Plug-in
 *      @subpackage Content
 *      @author José A. Cidre Bardelás
 *      @copyright Copyright (C) 2013-2017 José A. Cidre Bardelás and Joomla Empresa. All rights reserved
 *      @license GNU/GPL v3 or later
 *      
 *      Contact us at info@joomlaempresa.com (http://www.joomlaempresa.es)
 *      
 *      This file is part of User Mail Restriction Plug-in.
 *      
 *          User Mail Restriction Plug-in is free software: you can redistribute it and/or modify
 *          it under the terms of the GNU General Public License as published by
 *          the Free Software Foundation, either version 3 of the License, or
 *          (at your option) any later version.
 *      
 *          User Mail Restriction Plug-in is distributed in the hope that it will be useful,
 *          but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          GNU General Public License for more details.
 *      
 *          You should have received a copy of the GNU General Public License
 *          along with User Mail Restriction Plug-in.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgUserMailRestriction extends JPlugin {
	function onUserBeforeSave($user, $isnew, $new) {
		JFactory::getLanguage()->load('plg_user_mailrestriction', JPATH_ADMINISTRATOR);
		$app = JFactory::getApplication();

		// New user or admin
		if (!$isnew || $app->isAdmin())
		{
			return;
		}

		$domains = explode(',', str_replace(array("\r\n", "\r", "\n", " "), '', $this->params->get('domains')));
		$emails = explode(',', str_replace(array("\r\n", "\r", "\n", " "), '', $this->params->get('emails')));
		$allowDomains = $this->params->get('allow_domains');
		$email = trim($new['email']);
		list(,$domain) = explode('@', strtolower($email));

		if (in_array($email, $emails) || ($allowDomains && !in_array($domain, $domains)) || (!$allowDomains && in_array($domain, $domains)))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_USER_MAILRESTRICTION_DENY'), 'error');
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_users&view=registration'));
		}

		$usernames = explode(',', str_replace(array("\r\n", "\r", "\n", " "), '', $this->params->get('usernames')));
		$username = trim($new['username']);

		if(in_array($username, $usernames))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_USER_MAILRESTRICTION_DENY'), 'error');
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_users&view=registration'));
		}

		return true;
	}
}
