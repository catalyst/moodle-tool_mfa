<a href="https://travis-ci.org/catalyst/moodle-tool_mfa">
<img src="https://travis-ci.org/catalyst/moodle-tool_mfa.svg?branch=master">
</a>

# moodle-tool_mfa

* [What is this?](#what-is-this)
* [Why another MFA plugin for Moodle?](#why-another-mfa-plugin-for-moodle)
* [Branches](#branches)
* [Installation](#installation)
* [Configuration](#configuration)
  * [Factors](#factors)
    * [IP range](#ip-range)
    * [TOTP](#totp)
    * [Auth type](#auth-type)
    * [Security Questions](#security-questions)
    * [Email](#email)
    * [Other factors](#other-factors)
* [Scores and examples](#scores-and-examples)
* [Support](#support)

## What is this?

This is a Moodle plugin which adds Multi-Factor authentication (MFA), also known as Two-factor authentication (2FA) on top of your existing chosen authentication plugins.

https://en.wikipedia.org/wiki/Multi-factor_authentication

## Why another MFA plugin for Moodle?

There are other 2FA plugins for moodle such as:

https://moodle.org/plugins/auth_a2fa

This one is different because it is NOT a Moodle authentication plugin. It leverages new API's that Catalyst specifically implemented in Moodle Core to enable plugins to *augment* the login process instead of replacing it. This means that this MFA plugin can be added on top of any other authentication plugin resulting in a much cleaner architecture, and it means you can compose a solution that does everything you need instead of compromising by swapping out the entire login flow.

See this tracker and the new dev docs for more info:

https://tracker.moodle.org/browse/MDL-66173

https://docs.moodle.org/dev/Login_callbacks

That other major difference is that we support multiple authentication factor *types* in this plugin, eg IP Range, Email, TOPT and in future others such as SMS or hardware tokens or anything else as sub-plugins.

## Branches

`master` is considered stable and supports these versions, with the caveat of backporting the API's needed.

## Installation

## Configuration



### General settings

* Grace period


### Factors

The main concept to understand is the concept of factors. You must have some combination of factors which each contribute to you overall score in order to login. By configuring multiple factors and weighting them you can easily have quite complex and flexible rules.

#### IP Range

Use this factor to say that if you are on a secure network then that counts for something. This is very useful because you can set it up so that you can login fully via a secure network, and once logged in configure other factors like TOTP, and then use those other factors for logging in when not on a secure network.

#### TOTP

This is standard TOTP using Google Authenticator or any other app which conforms to the open standard.

#### Auth Type

This is so you can specify that logging in via say SAML via ADFS which may have already done it's own MFA checks has a certain score which might satisfy the criteria completely and effectly make it exempt from additional checks.

#### Security Questions

If the tool_securityquestions plugin is installed then you can use this as an additional factor. Note that because most people don't have security questions setup until after they have logged in the first time, this could be used more as a backup factor. ie if you have lost your TOTP device then you could fail back to this.

https://github.com/catalyst/moodle-tool_securityquestions

#### Email

A simple factor which sends a short lived code to your email which you then need to enter to login. Generally speaking this is a low security factor because typically the same username and password which logs you into moodle is the same which logs you into your email so it doesn't add much value.

#### Other factors

In theory you could impement almost anything as a factor, such as time of day, retina scans, or push notificatons. For a list of potential factor see:

https://en.wikipedia.org/wiki/Multi-factor_authentication#Authentication_factors


### Scores and examples

If your score is high enough then you are able to login. Scores can be weighted for different factors. Some factors do not require any input, such as checking the IP Address is within a secure subnet, while other require input such as entering a code. Factors are checked in the priority order until you either have a cumulative score high enough to login, or you run out of factors and you are denied login.

As a rule of thumb any factors which do not require any input should have a higher priority, and it may help you to reason about the combinations of factors by ordering the largest scoring ones first.

When you configure the scores and priorities it will generate a list of valid factor permutations to make it easy to check it's configured the way you want.

#### Example 1

You require a score of 100 and you have 3 factors configured:

```
auth_saml => 100
iprange => 100
totp => 100
```

The it will show:

```
You must be:
* logged in via saml
OR
* on a secure network
OR
* use TOTP
```

#### Example 2

If you still require a score of 100 but change the 3 scores to 50 then it would say:

```
You must be:
* logged in via saml AND on a secure network
OR
* logged in via saml AND use TOTP
OR
* on a secure network AND use TOTP
```

#### Example 3

With a requirement of a score of 100 and a configuration of:

```
auth_saml => 100
iprange => 100
totp => 100
email => 50
security_quesstions => 50
```

Then these are valid conditions:

```
You must be:
* logged in via saml
OR
* be on a secure network
OR
* use TOTP
OR
* on validate your email AND answer your security questions
```


## Support

If you have issues please log them in github here

https://github.com/catalyst/moodle-auth_saml2/issues

Please note our time is limited, so if you need urgent support or want to
sponsor a new feature then please contact Catalyst IT Australia:

https://www.catalyst-au.net/contact-us

This plugin was developed by Catalyst IT Australia:

https://www.catalyst-au.net/

<img alt="Catalyst IT" src="https://cdn.rawgit.com/CatalystIT-AU/moodle-auth_saml2/master/pix/catalyst-logo.svg" width="400">
