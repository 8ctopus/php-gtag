# cookies

    https://support.google.com/analytics/answer/11397207?hl=en

## user cookie

Store user

`_ga`

### architecture

GA1.1.random(10).timestamp

`GA1.1.1827526090.1689745728`

## session cookie

Store session state

> In Analytics, a session initiates when a user either opens your app in the foreground or views a page or screen and no session is currently active (e.g. their previous session has timed out). By default, a session ends (times out) after 30 minutes of user inactivity. There is no limit to how long a session can last.

> When a session starts, Google automatically collects a session_start event and generates a session ID (session_id) and session number (session_number) via the session_start event. Session ID is a timestamp of when a session began. Session number identifies the number of sessions that a user has started up to the current session (e.g., a user's third or fifth session on your site).

[ref](https://support.google.com/analytics/answer/9191807)

\_ga\_random(10)

`_ga_8XQMZ2E6TH`

### architecture

GS1.1.session_id(timestamp).session_number.session_engaged.last_activity?.?.?.?

_REM_ `last_activity` is always after `session_id` timestamp, maybe some last update

#### cookie on first day

GS1.1.1689765380.3.1.1689766550.0.0.0

#### cookie on next day on first attempt

GS1.1.1689828668.4.0.1689828668.0.0.0

#### cookie on next day on second attempt

GS1.1.1689828668.4.1.1689829000.0.0.0

#### cookie later on

GS1.1.1689832664.5.1.1689832969.0.0.0
