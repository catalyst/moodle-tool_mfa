<a href="https://travis-ci.org/catalyst/moodle-tool_mfa">
<img src="https://travis-ci.org/catalyst/moodle-tool_mfa.svg?branch=master">
</a>

# moodle-tool_mfa

## Why another plugin MFA for Moodle?

There are other 2FA plugins for moodle such as:

https://moodle.org/plugins/auth_a2fa

This one is different because it is NOT a Moodle authentication plugin. It leverages new API's that Catalyst specifically implemented in Moodle Core to enable plugins to *augment* the login process instead of replacing it. This means that this MFA plugin can be added on top of any other authentication plugin resulting in a much cleaner architecture, and it means you can compose a solution that does everything you need instead of compromising by swapping out the entire login flow.

See this tracker and the new dev docs for more info:

https://tracker.moodle.org/browse/MDL-66173

https://docs.moodle.org/dev/Login_callbacks

That other difference is that we intend to support multiple authentication factor *types* in this plugin, eg TOPT or SMS or whatever else as sub-plugins. Initially we will only supoprt TOPT but the idea is that you can configure it to allow multiple types, or let the use decide which or various options they would prefer, and most of the authenticatin flow augmentation in shared regardless of which type is in use.

## Installation

## Configuration

## 

