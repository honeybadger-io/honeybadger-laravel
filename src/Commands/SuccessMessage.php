<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

class SuccessMessage
{
    public static function withoutLinkToNotices() : string
    {
        return <<<'EX'
⚡ --- Honeybadger is installed! -----------------------------------------------
If you ever need help:

    - Check out the documentation: https://docs.honeybadger.io/lib/php/index.html
    - Email the 'badgers: support@honeybadger.io

Most people don't realize that Honeybadger is a small, bootstrapped company. We
really couldn't do this without you. Thank you for allowing us to do what we
love: making developers awesome.

Happy 'badgering!

Sincerely,
Ben, Josh and Starr
https://www.honeybadger.io/about/
⚡ --- End --------------------------------------------------------------------
EX;
    }

    public static function withLinkToNotice(string $noticeId) : string
    {
        $message = <<<'EX'
⚡ --- Honeybadger is installed! -----------------------------------------------
Good news: You're one deploy away from seeing all of your exceptions in
Honeybadger. For now, we've generated a test exception for you:

    https://app.honeybadger.io/notice/%s

If you ever need help:

    - Check out the documentation: https://docs.honeybadger.io/lib/php/index.html
    - Email the 'badgers: support@honeybadger.io

Most people don't realize that Honeybadger is a small, bootstrapped company. We
really couldn't do this without you. Thank you for allowing us to do what we
love: making developers awesome.

Happy 'badgering!

Sincerely,
Ben, Josh and Starr
https://www.honeybadger.io/about/
⚡ --- End --------------------------------------------------------------------
EX;
        return sprintf($message, $noticeId);
    }
}
