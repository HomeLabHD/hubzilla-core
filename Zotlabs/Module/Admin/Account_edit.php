<?php

namespace Zotlabs\Module\Admin;



class Account_edit {

	function post() {

		// Validate CSRF token
		//
		// We terminate with a 403 Forbidden status if the check fails.
		check_form_security_token_ForbiddenOnErr('admin_account_edit', 'security');

		$account_id = $_REQUEST['aid'];

		if(! $account_id)
			return;

		$pass1 = trim($_REQUEST['pass1']);
		$pass2 = trim($_REQUEST['pass2']);
		if($pass1 && $pass2 && ($pass1 === $pass2)) {
			$salt = random_string(32);
			$password_encoded = hash('whirlpool', $salt . $pass1);
			$r = q("update account set account_salt = '%s', account_password = '%s',
				account_password_changed = '%s' where account_id = %d",
				dbesc($salt),
				dbesc($password_encoded),
				dbesc(datetime_convert()),
				intval($account_id)
			);
			if($r)
				info( sprintf( t('Password changed for account %d.'), $account_id). EOL);

		}

		$service_class = trim($_REQUEST['service_class']);
		$account_level = 5;
		$account_language = trim($_REQUEST['account_language']);

		$r = q("update account set account_service_class = '%s', account_level = %d, account_language = '%s'
			where account_id = %d",
			dbesc($service_class),
			intval($account_level),
			dbesc($account_language),
			intval($account_id)
		);

		if($r)
			info( t('Account settings updated.') . EOL);

		goaway(z_root() . '/admin/accounts');
	}


	function get() {
		$account_id = intval(argv(2));

		$x = q("select * from account where account_id = %d limit 1",
			$account_id
		);

		if(! $x) {
			notice ( t('Account not found.') . EOL);
			return '';
		}

		$a = replace_macros(get_markup_template('admin_account_edit.tpl'), [
			'$security' => get_form_security_token('admin_account_edit'),
			'$account' => $x[0],
			'$title' => t('Account Edit'),
			'$pass1' => [ 'pass1', t('New Password'), ' ','' ],
			'$pass2' => [ 'pass2', t('New Password again'), ' ','' ],
			'$account_language' => [ 'account_language' , t('Account language (for emails)'), $x[0]['account_language'], '', language_list() ],
			'$service_class' => [ 'service_class', t('Service class'), $x[0]['account_service_class'], '' ],
			'$submit' => t('Submit'),
			]
		);

		return $a;


	}


}
