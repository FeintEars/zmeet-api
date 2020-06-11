var API = {

	// API request.
	request: function(method, url, params, success) {
		if (url.charAt(0) === '/') url = url.slice(1);
		// if (url == '') url = '/';
		url = window.location.origin + '/' + url;

		if (typeof params === 'object') {
			var id = Cookies.get('id'); if (id != undefined && params.id == undefined) params.id = id;
			var time = Cookies.get('time'); if (time != undefined && params.time == undefined) params.time = time;
			var hash = Cookies.get('hash'); if (hash != undefined && params.hash == undefined) params.hash = hash;

			var data = [];
			for (var key in params) data.push(key + '=' + params[key]);
			params = data.join('&');
		}
		else {
			var id = Cookies.get('id'); if (id != undefined) params += '&id=' + id;
			var time = Cookies.get('time'); if (time != undefined) params += '&time=' + time;
			var hash = Cookies.get('hash'); if (hash != undefined) params += '&hash=' + hash;
		}

		var local_success = function(resp) {
			resp = JSON.parse(resp);
			if (resp.status == 'error') {
				if (resp.error_code == 4) {
					Cookies.remove("id");
					Cookies.remove("time");
					Cookies.remove("hash");
					window.location.replace("/");
				}
			}

			if (resp.id) Cookies.set('id', resp.id);
			if (resp.time) Cookies.set('time', resp.time);
			if (resp.hash) Cookies.set('hash', resp.hash);
			if (resp.status == 'error') {
				alert("Error " + resp.error_code + ": " + resp.message);
			}
			else {
				success(resp);
			}
		}

		method = method.toLowerCase();
		if (method == 'get') {
			$.get(url + '?' + params, local_success);
		}
		else if (method == 'post') {
			$.post(url, params, local_success);
		}
	},

	// Just Example.
	// Get current user from API.
	me: null,
	loadme: function(success) {
		var user_id = Cookies.get('id');
		if (user_id == undefined) {
			API.me = null;
			success(API.me);
		}
		else {
            API.request('GET', '/api/user/read', {user_id: user_id}, function (resp) {
            	API.me = resp.user;
            	success(API.me);
            });
		}
	},

};
