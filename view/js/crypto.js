async function sodium_encrypt(element) {
	if (!window.sodium) {
		window.sodium = await SodiumPlus.auto();
	}

	if (typeof tinyMCE !== typeof undefined) {
		tinyMCE.triggerSave(false,true);
	}

	let message = $(element).val();

	if (!message) {
		return false;
	}

	let password = prompt(aStr['passphrase']);

	if (!password) {
		return false;
	}

	let hint = bin2hex(prompt(aStr['passhint']));

	let salt = await sodium.randombytes_buf(16);
	let nonce = await sodium.randombytes_buf(24);

	let key = await sodium.crypto_pwhash(
		32,
		password,
		salt,
		sodium.CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
		sodium.CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
	);

	// Message can be a string, buffer, array, etc.
	let ciphertext = await sodium.crypto_secretbox(message, nonce, key);
	delete message, password, key;

	let payload = {
		hint: hint,
		alg: 'XSalsa20',
		salt: await sodium.sodium_bin2hex(salt),
		nonce: await sodium.sodium_bin2hex(nonce),
		ciphertext: await sodium.sodium_bin2hex(ciphertext)
	};

	let val = "[crypt]" + window.btoa(JSON.stringify(payload)) + '[/crypt]';

	$(element).val(val);
}

async function sodium_decrypt(payload, element) {
	let arr = JSON.parse(window.atob(payload));

	if (arr.alg !== 'XSalsa20') {
		alert('Unsupported algorithm');
		return false;
	}

	let password = prompt((arr.hint.length) ? hex2bin(arr.hint) : aStr['passphrase']);

	if (!password) {
		return false;
	}

	let salt = await sodium.sodium_hex2bin(arr.salt);
	let nonce = await sodium.sodium_hex2bin(arr.nonce);
	let ciphertext = await sodium.sodium_hex2bin(arr.ciphertext);

	let key = await sodium.crypto_pwhash(
		32,
		password,
		salt,
		sodium.CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
		sodium.CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
	);

	let decrypted = await sodium.crypto_secretbox_open(ciphertext, nonce, key);
	delete password, key;

	if ($(element).css('display') === 'none' && typeof tinyMCE !== typeof undefined) {
		tinyMCE.activeEditor.setContent(decrypted.toString('utf-8'));
	}
	else {
		$(element).html(decrypted.toString('utf-8'));
	}
}

