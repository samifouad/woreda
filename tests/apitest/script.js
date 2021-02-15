// tab menu links
function initialize() {
	// internal link
	$(".mlink").unbind('click');
	$(".mlink").on('click', function() {

		// setup for history/address bar
		var targetUrl = $(this).attr('data-url');
		var targetContainer = $(this).attr('data-target');
		var targetArray = targetUrl.split('/');

		//setCurrentPrimary(targetUrl);
		//console.log(targetContainer);

		// setup for history/address bar
		window.history.pushState({url: "" + targetUrl + "", prev: ""+ window.location.pathname +""}, targetUrl, targetUrl);

		// loading
		setCurrentPage(targetUrl, ".content");
	});
}
 
// url routing function that changes content
function setCurrentPage (setUrl, target) {

	// can pass data between pages, currently umused
	var jsonData = { name: "John", time: "2pm" };
	var urlArray = setUrl.split('/');
	//console.log("/router?path="+ setUrl);
	getContent("/tests/apitest/route.php?path="+ setUrl, target, jsonData, function() {
			//
	});
}

function makeApiRequest (api, method, endpoint, jwt, data) {
	console.log('api: '+ api)
	console.log('method: '+ method)
	console.log('endpoint: '+ endpoint)
	console.log('jwt: '+ jwt)
	console.log('data: ')
	console.log(data)

	/*
	var targetDiv = $('.result-dump');
	$(targetDiv).empty()
	$(targetDiv).append('target: '+ targetUrl +'<br>')
	$(targetDiv).append('endpoint: '+ endpoint +'<br>')
	$(targetDiv).append('Data: <br><pre>'+ JSON.stringify(dataPack,undefined, 2) +'</pre><br>')
	$(targetDiv).append(getJwt(jwt))
	*/
}

function getJwt (name) {
	switch (name) {
		case 'none':
			return 'noooone';
		break;
		case 'admin':
			return 'aaaadmin';
		break;
		case 'steve':
			return 'steveeev';
		break;
		case 'susan':
			return 'suuuuusan';
		break;
		case 'custom':
			return 'get textarea';
		break;
	}
}

function gatherDataAndExecute (targetUrl) {
	switch (window.location.pathname)
	{
		case '/user-create':
			var dataPack = {}

			// get form data
			dataPack.firstname = $('input[name ="firstname"]').val()
			dataPack.lastname = $('input[name ="firstname"]').val()
			dataPack.number = $('input[name ="number"]').val()
			dataPack.username = $('input[name ="username"]').val()

			// get method
			var method = $('input:radio:checked[name ="method"]').val()

			// get endpoint
			var endpoint = $('input[name ="endpoint"]').val()

			// get jwt
			var jwt = $('input:radio:checked[name ="jwt"]').val()

			makeApiRequest(targetUrl, method, endpoint, jwt, dataPack)
		break;
		case '/user-validate':
			var firstname = $('input[name ="firstname"]').val()
			console.log(firstname)
			console.log('user validation page')
		break;
		default:
			var dataPack = {}

			// get form data
			dataPack.firstname = $('input[name ="firstname"]').val()
			dataPack.lastname = $('input[name ="firstname"]').val()
			dataPack.number = $('input[name ="number"]').val()
			dataPack.username = $('input[name ="username"]').val()

			// get method
			var method = $('input:radio:checked[name ="method"]').val()

			// get endpoint
			var endpoint = $('input[name ="endpoint"]').val()

			// get jwt
			var jwt = $('input:radio:checked[name ="jwt"]').val()

			makeApiRequest(targetUrl, method, endpoint, jwt, dataPack)
		break;
	}
}

// ajax data load
function getContent (xUrl, target, xData) {
	$.ajax({
		method: "GET",
		url: xUrl,
		cache: false,
		data: xData,
		dataType: "html"
	})
		.done(function( html, msg ) {
			$(target).empty(); // clear content
			// verify correct ui
			url = xUrl.replace('router.php?path=', '');
			$(target).append(html); // add new content
			$("html, body").animate({ scrollTop: 0 }, "fast");
			$("div.content").html();
			//console.log(html);
			initialize(); // activate links
		})
	.fail(function(xhr, textStatus, errorThrown) {
		switch (errorThrown)
		{
			case 'Unauthorized':
				window.location = "https://id.conoda.com/dosignout";
			break;
			case 'Bad Request':
				window.location = "https://id.conoda.com/dosignout";
			break;
		}
	})
	.always(function() {
		//initialize(); // activate tabs
		//console.log(resul);
	});
}

$(document).ready(function()
{
	// internal link
	$('input[name ="submit"]').on('click', function() {
		gatherDataAndExecute($(this).attr('value'))
	});

	// internal link
	$(".mlink").on('click', function() {

		// setup for history/address bar
		var targetUrl = $(this).attr('data-url');
		var targetContainer = $(this).attr('data-target');
		var targetArray = targetUrl.split('/');

		//setCurrentPrimary(targetUrl);
		//console.log(targetArray);

			// setup for history/address bar
			window.history.pushState({url: "" + targetUrl + "", prev: ""+ window.location.pathname +""}, targetUrl, targetUrl);

			// loading
			setCurrentPage(targetUrl, ".content");
	});

	// url routing for browser events (back/forward)
  window.onpopstate = function(e) {

		console.log(e);

		// verify not landing page
		if (e.state != null) {
			var pathArray = e.state.url.split('/');
			var pathOldArray = e.state.prev.split('/');

			if (pathArray[3] == "") { pathArray[3] = "user-create"; }

			// check to see if section changed
			if (pathArray[3] == pathOldArray[3])
			{
				setCurrentPage('/'+ pathArray[3], ".content");
			} else { // section different, also load menu
				setCurrentPage('/'+ pathArray[3], ".content");
			}
		} else {
			//console.log("defaulting");
			setCurrentPage('/user-create', ".content");
		}
  };

	// url routing for initial load of page
	var pathArray = window.location.pathname.split('/');

	if (pathArray[3] == "") { pathArray[3] = "user-create"; }

	// content at the first load of site
	setCurrentPage('/'+ pathArray[3], ".content");

	console.log('first load: ' + pathArray[3]);
});
