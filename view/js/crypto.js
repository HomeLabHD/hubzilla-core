async function sodium_encrypt(element) {
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

	let salt = await sodium.randombytes_buf(sodium.crypto_pwhash_SALTBYTES);
	let nonce = await sodium.randombytes_buf(sodium.crypto_secretbox_NONCEBYTES);

	let key = await sodium.crypto_pwhash(
		sodium.crypto_secretbox_KEYBYTES,
		password,
		salt,
		sodium.crypto_pwhash_OPSLIMIT_INTERACTIVE,
		sodium.crypto_pwhash_MEMLIMIT_INTERACTIVE,
		sodium.crypto_pwhash_ALG_DEFAULT
	);

	// Message can be a string, buffer, array, etc.
	let ciphertext = await sodium.crypto_secretbox_easy(message, nonce, key);
	delete message, password, key;

	let payload = {
		hint: hint,
		alg: 'XSalsa20',
		salt: await sodium.to_hex(salt),
		nonce: await sodium.to_hex(nonce),
		ciphertext: await sodium.to_hex(ciphertext)
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

	let salt = await sodium.from_hex(arr.salt);
	let nonce = await sodium.from_hex(arr.nonce);
	let ciphertext = await sodium.from_hex(arr.ciphertext);

	let key = await sodium.crypto_pwhash(
		sodium.crypto_secretbox_KEYBYTES,
		password,
		salt,
		sodium.crypto_pwhash_OPSLIMIT_INTERACTIVE,
		sodium.crypto_pwhash_MEMLIMIT_INTERACTIVE,
		sodium.crypto_pwhash_ALG_DEFAULT
	);

	let decrypted = await sodium.crypto_secretbox_open_easy(ciphertext, nonce, key);
	delete password, key;

	if ($(element).css('display') === 'none' && typeof tinyMCE !== typeof undefined) {
		tinyMCE.activeEditor.setContent(sodium.to_string(decrypted));
	}
	else {
		$(element).html(sodium.to_string(decrypted));
	}
}
