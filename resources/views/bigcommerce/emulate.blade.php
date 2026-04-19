<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emulate BigCommerce — {{ $site->name }}</title>
    @vite('packages/lantera/extension-framework/resources/js/bigcommerce/app.jsx')
</head>
<body>
    <div id="app" data-site="{{ json_encode($site->toArray()) }}"></div>
</body>
</html>
