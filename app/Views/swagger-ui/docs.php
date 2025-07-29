<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Swagger UI</title>
	<link rel="stylesheet" type="text/css" href="{{asset()}}dist/swagger-ui.css">
	<link rel="icon" type="image/png" href="{{asset()}}dist/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="{{asset()}}dist/favicon-16x16.png" sizes="16x16" />
	<style>
		html {
			box-sizing: border-box;
			overflow: -moz-scrollbars-vertical;
			overflow-y: scroll;
		}

		*,
		*:before,
		*:after {
			box-sizing: inherit;
		}

		body {
			margin: 0;
			background: #fafafa;
		}
	</style>
</head>

<body id="base-url" data-swagger_url="<?= base_url() . 'swagger/docs' ?>">
	<div id="swagger-ui"></div>
	<script src="{{asset()}}dist/swagger-ui-standalone-preset.js"></script>
	<script src="{{asset()}}dist/swagger-ui-bundle.js"></script>
	<script>
		window.onload = function() {
			// Begin Swagger UI call region
			const baseurl = document.querySelector("#base-url");
			const $url = baseurl.dataset.swagger_url;

			console.log(window.location.pathname);
			console.log(window.location.hostname);
			console.log(baseurl.dataset.swagger_url);

			const ui = SwaggerUIBundle({
				// url: window.location.protocol + "//" + window.location.hostname + "/Swagger/docs",
				// url: "https://petstore.swagger.io/v2/swagger.json",
				// url: "api.php",
				url: $url,
				dom_id: '#swagger-ui',
				deepLinking: true,
				presets: [
					SwaggerUIBundle.presets.apis,
					SwaggerUIStandalonePreset
				],
				layout: "StandaloneLayout"
			})
			// End Swagger UI call region
			window.ui = ui
		}
	</script>
</body>

</html>