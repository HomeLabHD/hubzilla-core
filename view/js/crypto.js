async function sodium_encrypt(element) {
	if (typeof tinyMCE !== typeof undefined) {
		tinyMCE.triggerSave(false,true);
	}

	const message = $(element).val();

	if (!message) {
		return false;
	}

	const password = prompt(aStr['passphrase']);

	if (!password) {
		return false;
	}

	const hint = bin2hex(prompt(aStr['passhint']));

/* This (Argon2) is more secure against bruteforce attacks than PBKDF2 but is overkill for javascript according to author
 * https://github.com/jedisct1/libsodium.js/issues/250#issuecomment-685971738
 *
 * It does not work in chromium mobile at all (freezing)
 *
 * This functions requires the browser-sumo/sodium.js

	const salt = await sodium.randombytes_buf(sodium.crypto_pwhash_SALTBYTES);
	const key = await sodium.crypto_pwhash(
		sodium.crypto_secretbox_KEYBYTES,
		password,
		salt,
		sodium.crypto_pwhash_OPSLIMIT_INTERACTIVE,
		sodium.crypto_pwhash_MEMLIMIT_INTERACTIVE,
		sodium.crypto_pwhash_ALG_DEFAULT
	);
*/

	const salt = crypto.getRandomValues(new Uint8Array(32)); // Generate a random salt (32 bytes)
	const key = await derivePBKDF2Hash(password, salt);

	const nonce = await sodium.randombytes_buf(sodium.crypto_secretbox_NONCEBYTES);

	// Message can be a string, buffer, array, etc.
	const ciphertext = await sodium.crypto_secretbox_easy(message, nonce, key);
	delete message, password, key;

	const payload = {
		hint: hint,
		alg: 'XSalsa20',
		salt: await sodium.to_hex(salt),
		nonce: await sodium.to_hex(nonce),
		ciphertext: await sodium.to_hex(ciphertext)
	};

	const val = "[crypt]" + window.btoa(JSON.stringify(payload)) + '[/crypt]';

	$(element).val(val);
}

async function sodium_decrypt(payload, element) {
	const arr = JSON.parse(window.atob(payload));

	if (arr.alg !== 'XSalsa20') {
		alert('Unsupported algorithm');
		return false;
	}

	const password = prompt((arr.hint.length) ? hex2bin(arr.hint) : aStr['passphrase']);

	if (!password) {
		return false;
	}

	const salt = await sodium.from_hex(arr.salt);
	const nonce = await sodium.from_hex(arr.nonce);
	const ciphertext = await sodium.from_hex(arr.ciphertext);

/* See sodium_encrypt()
	const key = await sodium.crypto_pwhash(
		sodium.crypto_secretbox_KEYBYTES,
		password,
		salt,
		sodium.crypto_pwhash_OPSLIMIT_INTERACTIVE,
		sodium.crypto_pwhash_MEMLIMIT_INTERACTIVE,
		sodium.crypto_pwhash_ALG_DEFAULT
	);
*/

	const key = await derivePBKDF2Hash(password, salt);

	const decrypted = await sodium.crypto_secretbox_open_easy(ciphertext, nonce, key);
	delete password, key;

	if ($(element).css('display') === 'none' && typeof tinyMCE !== typeof undefined) {
		tinyMCE.activeEditor.setContent(sodium.to_string(decrypted));
	}
	else {
		$(element).html(sodium.to_string(decrypted));
	}
}


async function derivePBKDF2Hash(password, salt) {
	const encoder = new TextEncoder();
	const passwordBuffer = encoder.encode(password);
	const saltBuffer = encoder.encode(salt);

	const key = await crypto.subtle.importKey(
		'raw', // key format
		passwordBuffer, // password as raw data
		{ name: 'PBKDF2' }, // algorithm
		false, // whether the key can be exported
		['deriveBits'] // key usages
	);

	const derivedBits = await crypto.subtle.deriveBits(
		{
			name: 'PBKDF2',
			salt: saltBuffer,
			iterations: 100000, // The higher the more secure should be at least 100000
			hash: 'SHA-256', // could also be SHA-512
		},
		key,
		256 // derived key length in bits (32 bytes)
	);

	return new Uint8Array(derivedBits);
}
