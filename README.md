Copyright (c) 2016 Adam Cox

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Use this platform with caution.
Use this platform only with exchange accounts that do not have more than you can lose.
Use this platform as though it might be malicious code.
It has not had any security audit whatsoever.
Ensure that your API Key/Secret can only be accesible from your server's IP address.
Ensure that you disable withdrawels on the API key/Secret that you use with this software and only enable it if you are going to be using it to withdraw.
This platform is capable of creating buy/sell on market/margin orders that can drain your exchange balances.
There could be bots that will collect the buy/sell market orders from a different account that could essentially steal your funds.
The code can create margin orders that might cause immediate liquidation of your account.
It is not safe to use this software on a system that has any unencrypted software or encrypted software with the keys.
Do not put other bots on the same operating system since they might contain your API Keys/Secrets with withdraw enabled and this software could potentially access those keys.
Do not trust updates that you pull from this repository since someone could get into my server and push a bad copy to github. 
I do have my github keys password protected and access github with a Key/Secret as is available by github.

## Getting Started

1) Use git to clone repository to your system
2) Copy config.php to config_safe.php and add your API Keys/Secrets
3) Use PHP to run start.php
4) Use cron to run every hour: */59 * * * * /usr/bin/php /var/www/html/nickelbot/start.php

### https://bitcointalk.org/index.php?topic=1420693.0

#### Good luck!
