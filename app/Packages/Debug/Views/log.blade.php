<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=config('app_name')?> Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?=use_css('bootstrap')?>">
    <script src="<?=use_js('jquery')?>"></script>
    <script src="<?=use_js('bootstrap')?>"></script>

    <style>
        #footer {
            position: fixed;
            height: 50px;
            background: #fff;
            padding: 10px;
            bottom: 0px;
            left: 0px;
            right: 0px;
            margin-bottom: 0px;
        }
    </style>
</head>

<body>

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-6">
                <h1><?=config('app_name')?> Logs</h1>
            </div>
            <div class="col-md-6" style="text-align: right; padding-top: 20px;">
                <a class="btn btn-primary btn-large" href="<?=url('admin/dashboard')?>"> Go to Dashboard</a>
            </div>
        </div>

        <pre class="logs" style="margin-bottom: 50px;font-size:smaller;">
            <?=$logs?>
        </pre>
    </div>

    <footer class="footer" id="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6" style="padding-top: 7px;"><?=config('app_name')?> Logs for <?=($date == today()) ? 'Today' : format_date('jS F, Y', $date)?></div>
                <div class="col-md-6" style="text-align: right;padding-top: 7px;">
                    <div class="btn btn-default btn-xs" onclick="history.go(0);">Refresh</div> |
                    <div class="btn btn-danger btn-xs" onclick="clearLogs()">Clear logs</div>
                </div>
            </div>
        </div>
    </footer>
    <script>
        function clearLogs() {
            alert("Sorry logs can't be cleared for now");
        }
    </script>
</body>

</html>