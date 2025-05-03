<?php

/**
 *   * Name: Notifications
 *   * Description: Shows all kind of notifications
 *   * Author: Mario Vavti
 */

namespace Zotlabs\Widget;

class Notifications {

	function widget($arr) {

		$channel = \App::get_channel();
		$notifications = [];

		if(local_channel()) {
			$notifications[] = [
				'type' => 'network',
				'icon' => 'grid-3x3',
				'severity' => 'secondary',
				'label' => t('Network'),
				'title' => t('Unseen network activity'),
				'viewall' => [
					'url' => 'network',
					'label' => t('Network stream')
				],
				'markall' => [
					'label' => t('Mark all notifications read')
				],
				'filter' => [
					'posts_label' => t('Show conversations only'),
					'name_label' => t('Filter by name or address')
				]
			];


			$notifications[] = [
				'type' => 'home',
				'icon' => 'house',
				'severity' => 'danger',
				'label' => t('Channel'),
				'title' => t('Unseen channel activity'),
				'viewall' => [
					'url' => 'channel/' . $channel['channel_address'],
					'label' => t('Channel stream')
				],
				'markall' => [
					'label' => t('Mark all notifications seen')
				],
				'filter' => [
					'posts_label' => t('Show conversations only'),
					'name_label' => t('Filter by name or address')
				]
			];

			$notifications[] = [
				'type' => 'dm',
				'icon' => 'envelope',
				'severity' => 'danger',
				'label' => t('Private'),
				'title' => t('Unseen private activity'),
				'viewall' => [
					'url' => 'network/?dm=1',
					'label' => t('Private stream')
				],
				'markall' => [
					'label' => t('Mark all read')
				],
				'filter' => [
					'posts_label' => t('Show conversations only'),
					'name_label' => t('Filter by name or address')
				]
			];

			$notifications[] = [
				'type' => 'all_events',
				'icon' => 'calendar-date',
				'severity' => 'secondary',
				'label' => t('Events'),
				'title' => t('New events notifications'),
				'viewall' => [
					'url' => 'cdav/calendar',
					'label' => t('View events')
				],
				'markall' => [
					'label' => t('Mark all events seen')
				]
			];

			$notifications[] = [
				'type' => 'intros',
				'icon' => 'people',
				'severity' => 'danger',
				'label' => t('New Connections'),
				'title' => t('New connections'),
				'viewall' => [
					'url' => 'connections',
					'label' => t('View all connections')
				]
			];

			$notifications[] = [
				'type' => 'files',
				'icon' => 'folder',
				'severity' => 'danger',
				'label' => t('Files'),
				'title' => t('New files'),
			];

			$notifications[] = [
				'type' => 'notify',
				'icon' => 'exclamation-circle',
				'severity' => 'danger',
				'label' => t('Notifications'),
				'title' => t('New notifications'),
				'viewall' => [
					'url' => 'notifications/system',
					'label' => t('View all notices')
				],
				'markall' => [
					'label' => t('Mark all notices seen')
				]
			];

			$notifications[] = [
				'type' => 'forums',
				'icon' => 'chat-quote',
				'severity' => 'secondary',
				'label' => t('Forums'),
				'title' => t('Forums'),
				'filter' => [
					'name_label' => t('Filter by name or address')
				]
			];
		}

		if(local_channel() && is_site_admin()) {
			$notifications[] = [
				'type' => 'register',
				'icon' => 'person-exclamation',
				'severity' => 'danger',
				'label' => t('Registrations'),
				'title' => t('New registrations notifications'),
			];
		}

		if(can_view_public_stream()) {
			$notifications[] = [
				'type' => 'pubs',
				'icon' => 'globe',
				'severity' => 'secondary',
				'label' => t('Public Stream'),
				'title' => t('New public stream notifications'),
				'viewall' => [
					'url' => 'pubstream',
					'label' => t('Public stream')
				],
				/*
				'markall' => [
					'label' => t('Mark all notifications seen')
				],
				*/
				'filter' => [
					'posts_label' => t('Show new posts only'),
					'name_label' => t('Filter by name or address')
				]
			];
		}

		$o = replace_macros(get_markup_template('notifications_widget.tpl'), [
			'$notifications' => $notifications,
			'$no_notifications' => t('Sorry, you have got no notifications at the moment'),
			'$loading' => t('Loading'),
			'$sys_only' => empty($arr['sys_only']) ? 0 : 1

		]);

		return $o;

	}
}

