[h2]check_account_password[/h2]
Use this hook to provide additional checks or validations of the password given when
registering and account.
[h3]Arguments:[/h3]
[code=php]array(
	'password' => $password,	// The password to check
	'result' => array(
		'error' => false,
		'message' => ''
	)
)[/code]
[h3]Results:[/h3]
For a failed check set the [code]error[/code] member of the [code]result[/code]
array to [code]true[/code] and the [code]message[/code] to a short message
explaining why it failed.

Otherwise, leave it alone.
