# Typecho-GetIPIntel
A plugin for typecho to stop users behind proxy from commenting.

## Why?
As we all know, we only need to enter our nickname and email address when you want to leave a comment. But if you made your email address public, that can be used to fake your identity. 
My friends had that unpleasant experience, when someone left rude words in other blogs with their email. That person used GoAgent, a popular proxy software popular in those days, and it's impossible for us to find out his true identity.
I thought of anti-fraud services, but back in those days such service is expensive (like MaxMind).
But now with the free and excellent service of *GetIPIntel.net* money is no longer a trouble. 

## Features
1. Different modes on requests to GetIPIntel.net API
2. Different actions on comments failed the test
3. Custom threshold (sensitivity)

## Installation
Just drop the *GetIPIntel* folder into *usr/plugins/* folder under your Typecho installation.

## Credits
[GetIPIntel Official Website](http://getipintel.net)
