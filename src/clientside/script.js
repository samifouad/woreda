function getCookie(name) {
  var regexp = new RegExp("(?:^" + name + "|;\s*"+ name + ")=(.*?)(?:;|$)", "g");
  var result = regexp.exec(document.cookie);
  return (result === null) ? null : result[1];
}

// image preloading
function preload(arrayOfImages) {
	$(arrayOfImages).each(function(){
		$('<img/>')[0].src = this;
	});
}
setTimeout(function() {
	preload([
		'/assets/images/loading.gif',
		'/assets/images/userpic.jpg'
	]);
}, 2500);

// tab menu links
function initialize() {
	//console.log("initialized");

	// for main site
	//$(".pageTabItem").unbind('click'); // to avoid stacking events
	/*
	$(".pageTabItem").on('click', function() {

		// setup for history/address bar
		var targetUrl = $(this).attr('data-url');
		window.history.pushState({url: "" + targetUrl + "", prev: ""+ window.location.pathname +""}, targetUrl, targetUrl);

		// loading selected tab
		setCurrentPage(targetUrl);

		// highlight
		//$(this).effect("highlight", {color:"#C8C8C8"}, 300);
	});
	*/

	AOS.refresh();

    $(".signUpButton").unbind('click');
    $(".signUpButton").on('click', function(e) {
       // e.preventDefault();//
        $("#signUpModal").modal("toggle");
        console.log("button clicked");
    });

	// internal link
	$(".clink").unbind('click');
	$(".clink").on('click', function() {

		// setup for history/address bar
		var targetUrl = $(this).attr('data-url');
		var targetContainer = $(this).attr('data-target');
		var targetArray = targetUrl.split('/');

		//setCurrentPrimary(targetUrl);
		//console.log(targetContainer);

			// setup for history/address bar
			window.history.pushState({url: "" + targetUrl + "", prev: ""+ window.location.pathname +""}, targetUrl, targetUrl);

			// loading
			setCurrentPage(targetUrl, targetContainer);
	});

	// external link
	$(".xlink").unbind('click');
	$(".xlink").on('click', function() {

		// setup for history/address bar
		var targetUrl = $(this).attr('data-url');
		var targetArray = targetUrl.split('/');

		if (targetUrl.toLowerCase().indexOf("https:") >= 0)
		{
			window.location = targetUrl;
		} else {
			// setup for history/address bar
			window.location =  "https://conoda.com"+ targetUrl;
		}
	});

	// internal link
	$(".mlink").unbind('click');
	$(".mlink").on('click', function() {

		console.log("mlink activated");

		// setup for history/address bar
		var targetPage = $(this).attr('data-page');
		var targetAction = $(this).attr('data-action');
		var targetUrl = $(this).attr('data-url');
		var targetArea = $(this).attr('data-target');
		//var targetArray = targetUrl.split('/');

		changeAuxMenuPage(targetPage, targetAction);
		setTimeout(function() {
			getAuxContent("/router?path="+ targetUrl, targetArea);
		}, 450);
		console.log("aux page loading");
		//console.log(targetUrl);
	});

	// internal link
	$(".mlinkb").on('click', function() {
		// setup for history/address bar
		var targetPage = $(this).attr('data-page');
		var targetAction = $(this).attr('data-action');
		changeAuxMenuPage(targetPage, targetAction);
	});
}

// capitalizes first letter, lowercase the rest
function capitalize(string) {
		return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

function setActiveMenuItem(url) {
	console.log(url);
	// detect which button to swap as active
	$('.topMenu').each(function( index ) {
		// detect which button to demote to inactive
		//var found = $( this ).attr('data-url').toString();
		//url = url.toString();
		if ( $( this ).attr('data-url') == '/'+url) {
			$( this ).addClass( "sqButton-active" );
			$( this ).removeClass( "sqButton-inactive" );
		}
	});
}

function switchSearchButton() {
	elem = $('#searchIconButton');
	// check to see if menu is not already open
	if ( $( elem ).hasClass('search-text-button')) {
		$( elem ).addClass( "search-text-button-inactive" );
		$( elem ).removeClass( "search-text-button" );
		//console.log("click disabled");
	} else {
		if ($('.searchResults:visible').length != 1) {
			$( elem ).addClass( "search-text-button" );
			$( elem ).removeClass( "search-text-button-inactive" );
			//console.log("click enabled");
		} else {
			//console.log("already opened");
		}
	}
}

function changeMenu(currentUrl) {
	var urlArray = currentUrl.split('/');
	// detect which button to swap as active
	$('.topMenu').each(function( index ) {
		// detect which button to demote to inactive
		if ( $( this ).hasClass( "sqButton-active" ) ) {
			//console.log("Requested: "+ urlArray[1].replace('/', '') + " - Active: " + $( this ).attr('data-url').replace('/', ''));
			if (urlArray[1].replace('/', '') === $( this ).attr('data-url').replace('/', '')) {
				//console.log("No action needed.");
			} else {
				$( this ).addClass( "sqButton-inactive" );
				$( this ).removeClass( "sqButton-active" );
				//console.log("Requested: "+ urlArray[1] + " - Active: " + $( this ).attr('data-url').replace('/', ''));
				//console.log("Updating menu: "+ urlArray[1]);
				setActiveMenuItem(urlArray[1]);
			}
		}
	});
}

// url routing function that changes content
function setCurrentPage (setUrl, target) {

	// can pass data between pages, currently umused
	var jsonData = { name: "John", time: "2pm" };

	// restrict certain pages if logged in
	var restricted = ["login", "signup"];
	theUsr = getCookie("ConodaUser");
	var urlArray = setUrl.split('/');
	if (restricted.includes(urlArray[1]) && theUsr != undefined)
	{
		window.location = 'https://conoda.com'
	}
	changeMenu(setUrl);
	//console.log("/router?path="+ setUrl);
	getContent("/router?path="+ setUrl, target, jsonData, function() {
			//
	});
};

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
			url = xUrl.replace('/router?path=', '');
			$(target).append(html); // add new content
			$("html, body").animate({ scrollTop: 0 }, "fast");
			$("div#content").html();
			//console.log(html);
			initialize(); // activate tabs
		})
	.fail(function(xhr, textStatus, errorThrown) {
		switch (errorThrown)
		{
			case 'Unauthorized':
				window.location = "https://conoda.com/dosignout";
			break;
			case 'Bad Request':
				window.location = "https://conoda.com/dosignout";
			break;
		}
	})
	.always(function() {
		//initialize(); // activate tabs
		//console.log(resul);
	});
}

// ajax data load
function getAuxContent (url, target) {
	$.ajax({
		method: "GET",
		url: url,
		cache: false,
		data: JSON.stringify({"subject:title":"Test Name"}),
		dataType: "html"
	})
		.done(function( html, msg ) {
			$(target).empty(); // clear content
			// verify correct ui
			theurl = url.replace('/router?path=', '');
			$(target).append(html); // add new content
			//$("html, body").animate({ scrollTop: 0 }, "fast");
			//$("div#content").html();
			switch (target)
			{
				case '.auxASyncTarget2':
					$('.auxLoading2').css('display', 'none');
				break;
				case '.auxASyncTarget3':
					$('.auxLoading3').css('display', 'none');
				break;
				case '.auxASyncTarget4':
					$('.auxLoading4').css('display', 'none');
				break;
			}
			initialize(); // activate links
			console.log("success; html: "+ html);
			console.log("success; target: "+ msg);
		})
	.fail(function(xhr, textStatus, errorThrown) {
		switch (errorThrown)
		{
			case 'Unauthorized':
				window.location = "https://conoda.com/dosignout";
			break;
			case 'Bad Request':
				window.location = "https://conoda.com/dosignout";
			break;
		}
	})
	.always(function() {
		//initialize(); // activate tabs
		console.log("finished; url: "+ theurl);
		console.log("finished; target: "+ target);
	});
}

// TODO: implement better fallbacks and error handling
function changeAuxMenuPage (page, action) {
	switch (page) {
		case 'page2':
			if (action === 'open')
			{
				$("div.auxContentPage2").transition({right: '0px'}, 400, 'easeOutSine');
			} else { // close
				$("div.auxContentPage2").transition({right: '-402px'}, 400, 'easeInOutQuad');
			}
		break;
		case 'page3':
			if (action === 'open')
			{
				$("div.auxContentPage3").transition({right: '0px'}, 550, 'easeOutSine');
			} else { // close
				$("div.auxContentPage3").transition({right: '-402px'}, 400, 'easeInOutQuad');
			}
		break;
		case 'page4':
			if (action === 'open')
			{
				$("div.auxContentPage4").transition({right: '0px'}, 550, 'easeOutSine');
			} else { // close
				$("div.auxContentPage4").transition({right: '-402px'}, 400, 'easeInOutQuad');
			}
		break;
	}
	//return 'hello';
}

$(document).ready(function()
{
    // internal link
    $(".signUpButton").unbind('click');
    $(".signUpButton").on('click', function(e) {
       // e.preventDefault();//
        $("#signUpModal").modal("toggle");
        console.log("button clicked");
    });

	// close open dialog when clicks outside of it occur
	$(document).mouseup(function(e)
	{
		console.log(e.target);

			// left search menu
	    var container = $(".searchArea");

			if (container.has(e.target).length === 0)
			{
				if ($('.searchResults:hidden').length == 0)
				{
			    // if the target of the click isn't the container nor a descendant of the container
			    if (container != e.target)
			    {
			        $('.searchResults:visible').fadeOut("fast", function() {
					    	//console.log("fade out complete")
								switchSearchButton();
					  	});
					     $("div.search").text("");
					     $("div.search").transition({width: '5px'}, 500, 'snap');
						//	console.log("hide results");
			    } else {
						//console.log("keep results open");
					}
				} else {
					//console.log("no action");
				}
			}

			// right aux
			var auxContainer = $(".auxTopMenuContainer");
			var topContainer = $(".menuOptions");

			if (auxContainer.has(e.target).length === 0)
			{
				if ($('.auxContent:hidden').length == 0)
				{
					// if the target of the click isn't the container nor a descendant of the container
					if (auxContainer != e.target)
					{
							$('.auxContentContainment').fadeOut("fast", function() {
								// close all pages
								changeAuxMenuPage("page2", "close");
								changeAuxMenuPage("page3", "close");
								changeAuxMenuPage("page4", "close");

								// flush all pages
								$('.auxASyncTarget2').empty();
								$('.auxASyncTarget3').empty();
								$('.auxASyncTarget4').empty();

								// reset all loading dialogs
								$('.auxLoading2').css('display', 'block');
								$('.auxLoading3').css('display', 'block');
								$('.auxLoading4').css('display', 'block');
								//console.log(e.target);
							});
					} else {
					//	console.log("keep open");
					}
				} else {
					//console.log("no action");
				}
			} else {
				// make sure top menu clicks outside of aux menu close aux menu
				if (topContainer.has(e.target).length > 0)
				{
						$('.auxContentContainment').fadeOut("fast", function() {
							// close all pages
							changeAuxMenuPage("page2", "close");
							changeAuxMenuPage("page3", "close");
							changeAuxMenuPage("page4", "close");

							// flush all pages
							$('.auxASyncTarget2').empty();
							$('.auxASyncTarget3').empty();
							$('.auxASyncTarget4').empty();

							// reset all loading dialogs
							$('.auxLoading2').css('display', 'block');
							$('.auxLoading3').css('display', 'block');
							$('.auxLoading4').css('display', 'block');
						//	console.log(e.target);
						});
				} else {
				//	console.log("keep open");
					//console.log(e.target);
				}
			}
	});

	// prevent search box from providing linebreaks
	// TODO: process search results on enter
	// OR only process search results on text change
	$('div[contenteditable=true]').keydown(function(e) {
    if (e.keyCode == 13) {
      document.execCommand('insertHTML', false, '');
      return false;
    }
  });

	// internal link
	$(".clink").on('click', function() {

		// setup for history/address bar
		var targetUrl = $(this).attr('data-url');
		var targetContainer = $(this).attr('data-target');
		var targetArray = targetUrl.split('/');

		//setCurrentPrimary(targetUrl);
		//console.log(targetArray);

			// setup for history/address bar
			window.history.pushState({url: "" + targetUrl + "", prev: ""+ window.location.pathname +""}, targetUrl, targetUrl);

			// loading
			setCurrentPage(targetUrl, targetContainer);
	});

	// external link
	$(".xlink").on('click', function() {

		// setup for history/address bar
		var targetUrl = $(this).attr('data-url');
		var targetArray = targetUrl.split('/');

		if (targetUrl.toLowerCase().indexOf("https:") >= 0)
		{
			window.location = targetUrl;
		} else {
			// setup for history/address bar
			window.location =  "https://conoda.com"+ targetUrl;
		}
	});

	// internal link
	$(".clink-menu").on('click', function() {

		// setup for history/address bar
		var targetUrl = $(this).attr('data-url');
		var targetContainer = $(this).attr('data-target');
		var targetArray = targetUrl.split('/');

		$('.auxContentContainment').fadeOut("fast", function() {
			$('.auxContentPage2').css("right", "-402px");
			//console.log(e.target);
		});

			// setup for history/address bar
			window.history.pushState({url: "" + targetUrl + "", prev: ""+ window.location.pathname +""}, targetUrl, targetUrl);

			// loading
			setCurrentPage(targetUrl, targetContainer);
	});

	// internal link
	$(".mlink").on('click', function() {

		// setup for history/address bar
		var targetPage = $(this).attr('data-page');
		var targetAction = $(this).attr('data-action');
		var targetUrl = $(this).attr('data-url');
		var targetArea = $(this).attr('data-target');
		//var targetArray = targetUrl.split('/');

		changeAuxMenuPage(targetPage, targetAction);
		setTimeout(function() {
			getAuxContent("/router?path="+ targetUrl, targetArea);
		}, 650);
		//console.log("aux page loading");
		//console.log(targetUrl);
	});

	// internal link
	$(".mlinkb").on('click', function() {
		// setup for history/address bar
		var targetPage = $(this).attr('data-page');
		var targetAction = $(this).attr('data-action');
		changeAuxMenuPage(targetPage, targetAction);
	});

	// aux menu algorithm here
	// closing page
	// $("div.auxContentPage2").transition({right: '-402px'}, 400, 'easeInOutQuad');
	//
	// opening page
	// $("div.auxContentPage2").transition({right: '0px'}, 550, 'easeOutSine');

	// url routing for browser events (back/forward)
  window.onpopstate = function(e) {

		console.log(e);

		// verify not landing page
		if (e.state != null) {
			var pathArray = e.state.url.split('/');
			var pathOldArray = e.state.prev.split('/');

			if (pathArray[1] == "") { pathArray[1] = "news"; }
			if (pathArray[2] == "") { pathArray[2] = "home"; }

			// check to see if section changed
			if (pathArray[1] == pathOldArray[1])
			{
				setCurrentPage('/'+ pathArray[1] +'/'+ pathArray[2], "#content");
			} else { // section different, also load menu
				setCurrentPage('/'+ pathArray[1] +'/'+ pathArray[2], "#content");
			}
		} else {
			//console.log("defaulting");
			setCurrentPage('/news/home', "#content");
		}
  };

	// url routing for initial load of page
	var pathArray = window.location.pathname.split('/');

	if (pathArray[1] == "") { pathArray[1] = "news"; }

	if (pathArray[2] == "" || !pathArray[2]) { pathArray[2] = "home"; }

	// content at the first load of site
	setCurrentPage('/'+ pathArray[1] +'/'+ pathArray[2], "#content");

	theUsr = getCookie("ConodaUser");

	// search focus
	$(".search-text-button, .search-text-button-icon").on('click', function() {
		$("div.search").transition({width: '220px'}, 500, 'snap');
		$(".searchResults").fadeIn("fast", function() {
    	//console.log("fade in complete");
			switchSearchButton();
  	});
		$("div.search").focus();
	});

	// aux menu focus
	$(".auxMenu-text-button, .auxMenu-text-button-icon").on('click', function() {
		$(".auxContentContainment").fadeIn("fast", function() {
			//console.log("fade in complete");
			//switchSearchButton();
		});
	});
	//console.log(theUsr);


	console.log('first load: ' + pathArray[1] +'/'+ pathArray[2]);
});
