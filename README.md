# Toustobot

Švitorka švitořící toustovač.

## Usage

1. Build docker image:
```
docker build -t toustobot .
```

2. Get lunch menus:
```
docker run --rm toustobot get-menu
```

3. Get lunch menus and send them to #lunch channel on Slack:
```
docker run --rm toustobot get-menu --slack-url <SLACK_WEBHOOK_URL>
```
