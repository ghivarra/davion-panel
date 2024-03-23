<!DOCTYPE html>
<html lang="id">
<head>
    <!-- PRECONNECT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- SIMPLE META -->
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, shrink-to-fit=no, user-scalable=yes">
    <meta name="google" content="notranslate">
    <meta name="robots" content="noindex, nofollow" />
    <meta name="author" content="<?= $_ENV['AUTHOR'] ?>">

    <!-- FAVICON & PAGE IDENTIFICATION -->
    <link data-id="favicon" rel="shortcut icon" href="<?= base_url("favicon.ico?v={$data['website']['logo_version']}") ?>" type="image/x-icon">
    <title><?= esc("{$data['title']} | {$data['website']['name']} - {$data['website']['tagline']}") ?></title>
    <meta name="description" content="<?= esc("{$data['website']['tagline']} - {$data['website']['description']}") ?>">

    <!-- ICONS -->
    <link data-id="iconApple" rel="apple-touch-icon" sizes="180x180" href="<?= imageUrl('icon/icon.png', 180) ?>">
    <link data-id="icon32" rel="icon" type="image/png" sizes="32x32" href="<?= imageUrl('icon/icon.png', 32) ?>">
    <link data-id="icon16" rel="icon" type="image/png" sizes="16x16" href="<?= imageUrl('icon/icon.png', 16) ?>">

    <!-- DEFAULT CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans&display=swap" rel="stylesheet" async>
</head>
<body>
    <div id="<?= $_ENV['VITE_APP_ID'] ?>" data-page='<?= $app->getPageData() ?>'></div>
</body>
</html>